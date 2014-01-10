<?php
class CronCommand extends CConsoleCommand 
{
    const LBRMAIL = 'lbr@example.ru';
	const MINUTES = 30;

	public function run($args)
	{
		$this->deadlineTransport();
		$this->beforeDeadlineTransport();
		$this->newTransport();
	}
    
    // Search for transport with deadline	
	public function deadlineTransport()
	{
	    $timeNow = date("Y-m-d H:i");
		$transportIds = '';

		$transports = Yii::app()->db->createCommand()
			->select('id')
			->from('transport')
			->where('date_to like :time', array(':time' => $timeNow . '%'))
			->queryAll()
		;
		$count = count($transports);
		
		if($count){
		    // пользователи, которые хотят получать mail-уведомления
		    $users2 = array();
		    $temp = Yii::app()->db->createCommand()
				->select('user_id')
				->from('user_field')
				->where('mail_deadline = :type', array(':type' => true))
				->queryAll()
			;
			foreach($temp as $t){
				$users2[] = $t['user_id'];
			}
			
			foreach($transports as $transport){
				$this->getUsers($transport['id'], 'mail_deadline', $users2);
				
				if(!empty($transportIds)) $transportIds .= ', ';
				$transportIds .= $transport['id'];
			}
			
			Transport::model()->updateAll(array('status' => 0), 'id in (' . $transportIds . ')');			
		}
	}
	
	// Search for transport before deadline	
	public function beforeDeadlineTransport()
	{
		$time = date("Y-m-d H:i", strtotime("+" . self::MINUTES . " minutes"));
		
		$transports = Yii::app()->db->createCommand()
			->select('id')
			->from('transport')
			->where('date_to like :time', array(':time' => $time . '%'))
			->queryAll()
		;
		$count = count($transports);
		
		if($count){
		    // пользователи, которые хотят получать mail-уведомления
		    $users2 = array();
		    $temp = Yii::app()->db->createCommand()
				->select('user_id')
				->from('user_field')
				->where('mail_before_deadline = :type', array(':type' => true))
				->queryAll()
			;
			foreach($temp as $t){
				$users2[] = $t['user_id'];
			}
			
			foreach($transports as $transport){
			    $transportId = $transport['id'];
				$this->getUsers($transportId, 'mail_before_deadline', $users2);
			}		
		}
	}
	
	public function getUsers($transportId, $mailType, $users2)
	{
	    // список всех пользователей для текущей перевозки
	    $rateMembers = Yii::app()->db->createCommand()
			->selectDistinct('user_id')
			->from('rate')
			->where('transport_id = :id', array(':id' => $transportId))
			->queryAll()
		;
		if(!empty($rateMembers)){
			//$users = $users1 = $users2 = array();
			$users = $users1 = array();
			foreach($rateMembers as $member){
				$users1[] = $member['user_id'];
			}

			$users = array_intersect($users1, $users2);
			if(!empty($users)){
			    $mailInfo = $this->sendMailAboutDeadline($transportId, $mailType);
				foreach($users as $userId){
				   $this->sendMail($userId, $mailInfo[0], $mailInfo[1]);
				}
			}
		}
	}
	
	// Search for recently added records
	public function newTransport()
	{
        $transportIds = '';
		$usersInternational = $usersLocal = $usersInternationalAndLocal = array();
	    $transportNew = Yii::app()->db->createCommand()
			->select('id, type')
			->from('transport')
			->where('new_transport = :status', array(':status' => 1))
			->queryAll()
		;
		$count = count($transportNew);
		$transportIdType = array(0 => array(), 1 => array());
		
		if($count){
			foreach($transportNew as $transport){
				if(!empty($transportIds)) $transportIds .= ', ';
				$transportIds .= $transport['id'];
				
				if($transport['type']){
				    $transportIdType[1][] = $transport['id'];
				} else {
				    $transportIdType[0][] = $transport['id'];
				}
			}
			Transport::model()->updateAll(array('new_transport' => 0), 'id in (' . $transportIds . ')');
			
			if(!empty($transportIdType[0])){ // international transportation
			    $temp = Yii::app()->db->createCommand()
					->select('user_id')
					->from('user_field')
					->where('mail_transport_create_1 = :type', array(':type' => true))
					->queryAll()
				;
				foreach($temp as $t){
				    $usersInternational[] = $t['user_id'];
				}
			}
			
			if(!empty($transportIdType[1])){ // local transportation
			    $temp = Yii::app()->db->createCommand()
					->select('user_id')
					->from('user_field')
					->where('mail_transport_create_2 = :type', array(':type' => true))
					->queryAll()
				;
				foreach($temp as $t){
				    $usersLocal[] = $t['user_id'];
				}
			}

			if(!empty($usersInternational) && !empty($usersLocal)){ // both transportation
			    $usersInternationalAndLocal = array_intersect($usersInternational, $usersLocal);
				$temp = array_diff($usersInternational, $usersInternationalAndLocal);
				$usersLocal = array_diff($usersLocal, $usersInternationalAndLocal);
				$usersInternational = $temp;
			}
			
			if(!empty($usersInternational)){
				$this->sendMailAboutNew($usersInternational, $transportIdType, 0);
			}
			
			if(!empty($usersLocal)){
				$this->sendMailAboutNew($usersLocal, $transportIdType, 1);
			}
			
			if(!empty($usersInternationalAndLocal)){
				$this->sendMailAboutNew($usersInternationalAndLocal, $transportIdType);
			}
		}
	}
	
	// Send mail with recently added transports
	public function sendMailAboutNew($users, $transportIds, $type = 2)
	{
	    foreach($users as $userId){
			$user = Yii::app()->db->createCommand()
				->select()
				->from('user')
				->where('id = :id', array(':id' => $userId))
				->queryRow()
			;
            
			$name = $user['name'];
			$email = $user['email'];
            
			$subject = 'Уведомление о появлении новых перевозок';
			
			switch($type){
			    case 0: $subject = 'Уведомление о появлении новых международных перевозок'; break;
			    case 1: $subject = 'Уведомление о появлении новых локальных перевозок'; break;
			}
			
			$message = "
			<html>
			<head>
			  <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
			</head>
			<body>
			  <p>Уважаемый " . $name . "</p>
			  <p>Были опубликованы новые перевозки. </p>";
			 
			if($type == 0 || $type == 2){
			   $message .= "<p><b>Международные</b> перевозки с номерами: " . implode(',', $transportIds[0]) . ".</p>";
			} 
			if($type == 1 || $type == 2){
			   $message .= "<p><b>Локальные</b> перевозки с номерами: " . implode(',', $transportIds[1]) . ".</p>";
			}
			
			$message .= "
			</body>
			</html>
			";
			
			$headers  = 'MIME-Version: 1.0' . '\r\n';
			$headers .= 'Content-type: text/html; charset=utf-8' . '\r\n';
			$headers .= 'To: ' . $name . '<' . $email . '>' . '\r\n';
			$headers .= 'From: Биржа перевозок ЛБР АгроМаркет <' . self::LBRMAIL . '>' . '\r\n';

			mail($email, $subject, $message, $headers);		
		}
	}
	
	// Send mail about transport's deadline
	//public function sendMailAboutDeadline($userId, $transportId, $mailType)
	public function sendMailAboutDeadline($transportId, $mailType)
	{
	   /* $user = Yii::app()->db->createCommand()
			->select()
			->from('user')
			->where('id = :id', array(':id' => $userId))
			->queryRow()
		;
		
		$email = $user['email'];
		*/
		
		$subject = 'Уведомление';
		
		$message = "
		<html>
		<head>
		  <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
		</head>
		<body>";
		
		if($mailType == 'mail_deadline'){
		    $message .= "<p>Перевозка с номером " . $transportId . " закрыта.</p>";
			$subject = 'Уведомление о завершении перевозки';
		} else if($mailType == 'mail_before_deadline'){
		    $message .= "<p>Перевозка с номером " . $transportId . " будет закрыта через " . self::MINUTES . " минут.</p>";
		    $subject = 'Уведомление о скором завершении перевозки';
		} else {
		    $message .= "<p>Ваше предложение по перевозке с номером " . $transportId . " было перебито.</p>";
		}
		
		$message .= "
		</body>
		</html>
		";

		/*
		$headers  = 'MIME-Version: 1.0' . '\r\n';
		$headers .= 'Content-type: text/html; charset=utf-8' . '\r\n';
		$headers .= 'To: ' . $user['name'] . '<' . $email . '>' . '\r\n';
		$headers .= 'From: Биржа перевозок ЛБР АгроМаркет <' . self::LBRMAIL . '>' . '\r\n';

		mail($email, $subject, $message, $headers);
		*/
		return array($subject, $message);
	}
	
	public function sendMail($userId, $subject, $message){
	    $user = Yii::app()->db->createCommand()
			->select()
			->from('user')
			->where('id = :id', array(':id' => $userId))
			->queryRow()
		;
		
		$email = $user['email'];
		
		$headers  = 'MIME-Version: 1.0' . '\r\n';
		$headers .= 'Content-type: text/html; charset=utf-8' . '\r\n';
		$headers .= 'To: ' . $user['name'] . '<' . $email . '>' . '\r\n';
		$headers .= 'From: Биржа перевозок ЛБР АгроМаркет <' . self::LBRMAIL . '>' . '\r\n';

		mail($email, $subject, $message, $headers);
	}
}