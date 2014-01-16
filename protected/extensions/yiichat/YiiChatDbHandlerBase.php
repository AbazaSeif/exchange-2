<?php 
abstract class YiiChatDbHandlerBase extends CComponent implements IYiiChat 
{
	protected $_identity;
	protected $_chat_id;
	protected $_data;

	protected function getIdentity(){ return $this->_identity; }
	protected function getChatId(){ return $this->_chat_id; }
	protected function getData(){ return $this->_data; }

	// abstract optional
	protected function getTableName(){
		return "rate";
	}
	
	// abstract strict
	protected function getDb(){}
	protected function getIdentityName(){}
	protected function getDateFormatted($value){}
	protected function createPostUniqueId(){}
	protected function acceptMessage($message){}

	/**
	 	post a message into your database.
	**/
	
	public function yiichat_post($chat_id, $identity, $message, $data){
		$this->_chat_id = $chat_id;
		$this->_identity = $identity;
		$this->_data = $data;
		$message_filtered = trim($this->acceptMessage($message));
		
		if($message_filtered != "") {
		    $obj = array(
			   'transport_id'  => $chat_id,
			   'user_id' => 3, // !!!!!!!!!!!!!!!!!!
			   'date'    => date("Y-m-d H:i:s"),
			   'price'   => (int)$message
			);
			
			$modelRate = new Rate;
			$modelRate->field_id = $this->createPostUniqueId();
			$modelRate->post_identity = $identity;
			$modelRate->attributes = $obj;
			$modelRate->save();
			
			$model = Transport::model()->findByPk($chat_id);
			$rateId = $model->rate_id;
			
			// send mail
			if(!empty($rateId)){ // empty if no offers
			    $rateModel = Rate::model()->findByPk($rateId);
			    $this->mailKillRate($rateId, $rateModel);
				$this->siteKillRate($rateId, $rateModel);
			}
			
			
			$model->rate_id = $modelRate->id;
			$model->save();
			
			// now retrieve the post
			// $obj['time']=$this->getDateFormatted($obj['created']);
            // date('d.m.Y H:i', strtotime($obj['date']));
           
			$obj['time'] = date('d.m.Y H:i:s', strtotime($this->getDateFormatted($obj['date'])));
			
			// $obj['user']='Вася';
			
			return $obj;
		} else {
			return array();
        }
	}
	
	// Send mail to user if his rate was killed
	public function mailKillRate($rateId, $rateModel)
	{
	    $users = array();
		$temp = Yii::app()->db->createCommand()
			->select('user_id')
			->from('user_field')
			->where('mail_kill_rate = :type', array(':type' => true))
			->queryAll()
		;
		foreach($temp as $t){
			$users[] = $t['user_id'];
		}

		if(in_array($rateModel->user_id, $users)){
		    $userModel = User::model()->findByPk($rateModel->user_id);
			$email = $userModel->email;
			$subject = 'Уведомление';

			$headers  = 'MIME-Version: 1.0' . '\r\n';
			$headers .= 'Content-type: text/html; charset=utf-8' . '\r\n';
			$headers .= 'To: ' . $userModel->name . '<' . $email . '>' . '\r\n';
			$headers .= 'From: Биржа перевозок ЛБР АгроМаркет <' . Yii::app()->params['adminEmail'] . '>' . '\r\n';
            
			$message = "<html>
			<head>
			  <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
			</head>
			<body>
				<>
				<p>Вашу ставку для перевозки с номером " . $rateModel->transport_id . " перебили </p>
			</body>
			</html>
			";
		    mail($email, $subject, $message, $headers);
		}
	}
	public function siteKillRate($rateId, $rateModel)
	{
	    $users = array();		
		$temp = Yii::app()->db->createCommand()
			->select('user_id')
			->from('user_field')
			->where('site_kill_rate = :type', array(':type' => true))
			->queryAll()
		;
		foreach($temp as $t){
			$users[] = $t['user_id'];
		}

		if(in_array($rateModel->user_id, $users)){
			$obj = array(
				'user_id' => $rateModel->user_id,
				'transport_id' => $rateModel->transport_id,
				'status' => 1,
				'type' => 1, // !!! message color ( заменить )
				'event_type' => 5,
			);
			
			Yii::app()->db->createCommand()->insert('user_event',$obj);
		}
	}
	/**
	 	retrieve posts from your database, considering the last_id argument:
		$last_id is the ID of the last post sent by the other person:
			when -1: 
				you must reetrive all posts this scenario occurs when 
				the chat initialize, retriving your posts and those posted
				by the others.
			when >0: 
				you must retrive thoses posts that match this criteria:
					a) having an owner distinct as $identity
					b) having an ID greater than $last_id
				this scenario occurs when the post widget refreshs using
				a timer, in order to receive the new posts since last_id.
	 */
	public function yiichat_list_posts($chat_id, $identity, $last_id, $data){
		$this->_chat_id = $chat_id;
		$this->_identity = $identity;
		$this->_data = $data;
		$limit = 3;
		$where_string='';
		$where_params=array();

		// case all posts:
		if($last_id == -1){
			//$where_string = 'chat_id=:chat_id';
			$where_string = 'transport_id=:chat_id';
			$where_params = array(
				':chat_id' => $chat_id,
			);
			$rows = $this->db->createCommand()
			->select()
			->from($this->getTableName())
			->where($where_string,$where_params)
			//->limit(1)
			//->order('date desc')
			->order('date asc')
			->queryAll();
			
			foreach($rows as $k=>$v){
				//$rows[$k]['time']=$this->getDateFormatted($v['created']);
				
				$rows[$k]['time']=date('d.m.Y H:i:s', strtotime($v['date']));
				
				//$this->getDateFormatted($v['date']);
				//$rows[$k]['user']='Вася';
            }
            
			return $rows;
		} else {
			// case timer, new posts since last_id, not identity
			$where_string = '((transport_id=:chat_id) and (post_identity<>:identity))';
			$where_params = array(
				':chat_id' => $chat_id,
				':identity' => $identity,
			);
			$rows = $this->db->createCommand()
			->select()
			->from($this->getTableName())
			->where($where_string,$where_params)
			//->order('created desc') // in this case desc,late will be sort asc 
			->order('date asc') // in this case desc,late will be sort asc 
			->queryAll();
			$ar = $this->getLastPosts($rows, $limit, $last_id);
			foreach($ar as $k=>$v)
				//$ar[$k]['time']=$this->getDateFormatted($v['created']);
			    $ar[$k]['time']=date('d.m.Y H:i:s', strtotime($v['date']));
                            //$ar[$k]['user']='Вася';
			return $ar;
		}
	}

	/**
	 	retrieve the last posts since the last_id, must be used
		only when the records has been filtered (case timer).
	 */
	private function getLastPosts($rows, $limit, $last_id){
		if(count($rows)==0)
			return array();
		$n=-1;
		for($i=0;$i<count($rows);$i++)
			if($rows[$i]['id']==$last_id){
				$n=$i;
				break;
			}
		if($last_id=='' || $last_id==null){
			if($n==-1)
				$n = $i-1;
			if($n==0){
				// TEST CASE: 7
				return $rows;
			}else{
				// TEST CASES: 6 and 8
				$cnk2 = array_chunk($rows, $limit);
				return array_reverse($cnk2[0]);
			}
		}
		if($n > 0){
			$cnk = array_chunk($rows,$n);
			$cnk2 = array_chunk($cnk[0], $limit);
			return array_reverse($cnk2[0]);
		} else {
			return array();
		}
	}
}

