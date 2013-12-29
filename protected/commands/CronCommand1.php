<?php
class CronCommand extends CConsoleCommand 
{
	public function run($args)
	{
		$this->deadlineTransport();
		$this->newTransport();
	}
    
    // Search for transport with deadline	
	public function deadlineTransport()
	{
	    $timeNow = date("Y-m-d H:i");
		$transportIds = '';

		$transportForUpdate = Yii::app()->db->createCommand()
			->select('id')
			->from('transport')
			->where('date_to like :time', array(':time' => $timeNow . '%'))
			->queryAll()
		;
		$count = count($transportForUpdate);
		
		if($count){
			foreach($transportForUpdate as $transport){
				$rateMembers = Yii::app()->db->createCommand()
					->selectDistinct('user_id')
					->from('rate')
					->where('transport_id = :id', array(':id' => $transport['id']))
					->queryAll()
				;
				// Sent mail 
				if(!empty($rateMembers)){
					foreach($rateMembers as $member){
						$userId = $member['user_id'];
						$user = Yii::app()->db->createCommand()
							->select()
							->from('user')
							->where('id = :id', array(':id' => $userId))
							->queryRow()
						;
						
						$to = $user['email'];
						$subject = 'Уведомление о завершении перевозки';
						
						$message = "
						<html>
						<head>
						  <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
						</head>
						<body>
						  <p>Перевозка с номером " . $transport['id'] . " закрыта.</p>
						</body>
						</html>
						";
						
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
						$headers .= 'To: ' . $user['name'] . '<' . $user['email'] . '>' . "\r\n";
						$headers .= 'From: Биржа перевозок ЛБР АгроМаркет <test@example.com>' . "\r\n";

						mail($to, $subject, $message, $headers);
					}
				}
				
				if(!empty($transportIds)) $transportIds .= ', ';
				$transportIds .= $transport['id'];
			}
			
			Transport::model()->updateAll(array('status' => 0), 'id in (' . $transportIds . ')');			
		}
	}
	
	// Search for recently added records
	public function newTransport()
	{
        $transportIds = '';
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
			    $usersInternational = Yii::app()->db->createCommand()
					->select('user_id')
					->from('user_field')
					->where('mail_transport_create_2 = :type', array(':type' => true))
					->queryAll()
				;
				if(!empty($usersInternational)){
					foreach($usersInternational as $userId){
					    $user = Yii::app()->db->createCommand()
							->select()
							->from('user')
							->where('id = :id', array(':id' => $userId))
							->queryRow()
						;
						
						$to = $user['email'];
						$subject = 'Уведомление о появлении новой международной перевозки';
						
						$message = "
						<html>
						<head>
						  <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
						</head>
						<body>
						  <p>Были опубликованы перевозки со следующими номерами: " . implode(',', $transportIdType[0]) . ". </p>
						</body>
						</html>
						";
						
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
						$headers .= 'To: ' . $user['name'] . '<' . $user['email'] . '>' . "\r\n";
						$headers .= 'From: Биржа перевозок ЛБР АгроМаркет <test@example.com>' . "\r\n";

						mail($to, $subject, $message, $headers);
					}
				}
			}
			
			if(!empty($transportIdType[1])){ // local transportation
			    $usersLocal = Yii::app()->db->createCommand()
					->select('user_id')
					->from('user_field')
					->where('mail_transport_create_1 = :type', array(':type' => true))
					->queryAll()
				;
				if(!empty($usersLocal)){
					foreach($usersLocal as $userId){
					    $user = Yii::app()->db->createCommand()
							->select()
							->from('user')
							->where('id = :id', array(':id' => $userId))
							->queryRow()
						;
						
						$to = $user['email'];
						$subject = 'Уведомление о появлении новой локальной перевозки';
						
						$message = "
						<html>
						<head>
						  <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
						</head>
						<body>
						  <p>Были опубликованы перевозки со следующими номерами: " . implode(',', $transportIdType[1]) . ". </p>
						</body>
						</html>
						";
						
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
						$headers .= 'To: ' . $user['name'] . '<' . $user['email'] . '>' . "\r\n";
						$headers .= 'From: Биржа перевозок ЛБР АгроМаркет <test@example.com>' . "\r\n";

						mail($to, $subject, $message, $headers);
					}
				}
			}
		}
	}
}