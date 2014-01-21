<?php
class TransportController extends Controller
{
	public function actionIndex()
	{
	    $lastRates = array();	
		$criteria = new CDbCriteria();
		$dataProvider = new CActiveDataProvider('Transport',
                    array(
                        'criteria' => $criteria,
                        'pagination'=>array(
                           'pageSize' => 2,
                           'pageVar' => 'page',
                        ),
                        'sort'=>array(
                                'attributes'=>array(
                                        'date_from'=>array(
                                                'asc'=>'status ASC',
                                                'desc'=>'status DESC',
                                                'default'=>'desc',
                                        ),
                                        'date_to'=>array(
                                                'asc'=>'status ASC',
                                                'desc'=>'status DESC',
                                                'default'=>'desc',
                                        ),
                                        'date_published'=>array(
                                                'asc'=>'date_published ASC',
                                                'desc'=>'date_published DESC',
                                                'default'=>'desc',
                                        )
                                ),
                                'defaultOrder'=>array(
                                        'date_published' => CSort::SORT_DESC,
                                ),                        
                        ),
                    )
		);
		
		$this->render('view', array('data' => $dataProvider, 'title'=>'Все перевозки'));
	}
	
	public function actionDescription($id)
	{
		$transportInfo=Yii::app()->db->createCommand("SELECT * from transport where id='".$id."'")->queryRow();

		$allRatesForTransport = Yii::app()->db->createCommand()
			->select('r.date, r.price, u.name')
			->from('rate r')
			->join('user u', 'r.user_id=u.id')
			->where('r.transport_id=:id', array(':id'=>$id))
			->order('r.date desc')
			->queryAll()
		;
		
		//$this->render('user/transport/item', array('rateData' => $dataProvider, 'transportInfo' => $transportInfo));
		$this->render('user.views.transport.item', array('rateData' => $dataProvider, 'transportInfo' => $transportInfo));
	}
	
	/* Ajax update rate for current transport */
	public function actionUpdateRatesPrice()
	{
	    $id = $_POST['id'];
		$newPrice = $_POST['newRate'];
		
		if($newPrice) {
		    $obj = array(
			   'transport_id'  => $id,
			   'user_id' => Yii::app()->user->_id,
			   'date'    => date("Y-m-d H:i:s"),
			   'price'   => (int)$newPrice
			);

			$modelRate = new Rate;
			$modelRate->attributes = $obj;
			$modelRate->save();
			
			$model = Transport::model()->findByPk($id);
			$rateId = $model->rate_id;
			
			// send mail
			if(!empty($rateId)){ // empty if no rates
			    $rateModel = Rate::model()->findByPk($rateId);
			    $this->mailKillRate($rateId, $rateModel);
				$this->siteKillRate($rateId, $rateModel);
			}
			
			$model->rate_id = $modelRate->id;
			$model->save();
		}
		
		$sql = 'select price from rate where transport_id = '.$id.' group by transport_id order by date desc limit 1';
		$price = Yii::app()->db->createCommand($sql)->queryScalar();
	    $data = Yii::app()->db->createCommand()
			//->select('r.date, r.price, u.name')
			->select('r.*, u.name, u.surname')
			->from('rate r')
			->join('user u', 'r.user_id=u.id')
			->where('r.transport_id=:id', array(':id'=>$id))
			->order('r.date asc')
			->queryAll()
		;
		//var_dump($data);
		foreach($data as $k=>$v){
			//$rows[$k]['time']=$this->getDateFormatted($v['created']);
			$data[$k]['time']=date('d.m.Y H:i:s', strtotime($v['date']));
			/*$rows[$k]['name']=$v['name'];
			$rows[$k]['surname']=$v['surname'];
			$rows[$k]['price']=$v['price'];*/
		}
		
		$array = array('price'=>$price, 'all'=>$data);
		
		//echo $data;//json_encode($data);
		echo json_encode($array);
	}
	
    public function addFormat($date)
	{
	    if((int)$date < 10) $date = '0' . $date;
		return $date;
	}
	
	/* Get latest price for current transport */
	public function getPrice($id)
	{
	    $row = Yii::app()->db->createCommand()
		    ->select()
			->from('rate')
			->where('id = :id', array(':id' => $id))
			->queryRow()
		;
		return $row['price'];
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
}