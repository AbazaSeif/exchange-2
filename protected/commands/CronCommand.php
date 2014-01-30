<?php
class CronCommand extends CConsoleCommand 
{
    public function run($args)
    {
        //$this->deadlineTransport();
        //$this->beforeDeadlineTransport();
        $this->newTransport();
    }

    // Search for transport with deadline	
    public function deadlineTransport()
    {
        $timeNow = date("Y-m-d H:i", strtotime("+" . Yii::app()->params['hoursBefore'] . " hours"));
        $transportIds = '';

        $transports = Yii::app()->db->createCommand()
            ->select('id')
            ->from('transport')
            ->where('date_to like :time', array(':time' => $timeNow . '%'))
            ->queryAll()
        ;

        $count = count($transports);

        if($count){
            // users who want to get mail
            $usersMail = $usersSite = array();
            $temp = Yii::app()->db->createCommand()
                    ->select('user_id')
                    ->from('user_field')
                    ->where('mail_deadline = :type', array(':type' => true))
                    ->queryAll()
            ;
            foreach($temp as $t){
                    $usersMail[] = $t['user_id'];
            }

            $temp = Yii::app()->db->createCommand()
                    ->select('user_id')
                    ->from('user_field')
                    ->where('site_deadline = :type', array(':type' => true))
                    ->queryAll()
            ;
            foreach($temp as $t){
                    $usersSite[] = $t['user_id'];
            }

            foreach($transports as $transport){
                    $this->getUsers($transport['id'], 'mail_deadline', $usersMail, $usersSite, 1);

                    if(!empty($transportIds)) $transportIds .= ', ';
                    $transportIds .= $transport['id'];
            }

            Transport::model()->updateAll(array('status' => 0), 'id in (' . $transportIds . ')');			
        }
    }

    // Search for transport before deadline	
    public function beforeDeadlineTransport()
    {
        $time = date("Y-m-d H:i", strtotime("+" . Yii::app()->params['hoursBefore'] . " hours " . Yii::app()->params['minNotify'] . " minutes"));

        $transports = Yii::app()->db->createCommand()
                ->select('id')
                ->from('transport')
                ->where('date_to like :time', array(':time' => $time . '%'))
                ->queryAll()
        ;
        $count = count($transports);

        if($count){
            $usersMail = $usersSite = array();
            $temp = Yii::app()->db->createCommand()
                ->select('user_id')
                ->from('user_field')
                ->where('mail_before_deadline = :type', array(':type' => true))
                ->queryAll()
            ;
            foreach($temp as $t){
                $usersMail[] = $t['user_id'];
            }

            $temp = Yii::app()->db->createCommand()
                ->select('user_id')
                ->from('user_field')
                ->where('site_before_deadline = :type', array(':type' => true))
                ->queryAll()
            ;
            foreach($temp as $t){
                $usersSite[] = $t['user_id'];
            }

            foreach($transports as $transport) {
                $transportId = $transport['id'];
                $this->getUsers($transportId, 'mail_before_deadline', $usersMail, $usersSite, 2);
            }		
        }
    }

    /* get users who made a rate for current transportation*/
    public function getUsers($transportId, $mailType, $usersMail, $usersSite, $messageType) 
    {
        // list of users for current transportation
        $rateMembers = Yii::app()->db->createCommand()
            ->selectDistinct('user_id')
            ->from('rate')
            ->where('transport_id = :id', array(':id' => $transportId))
            ->queryAll()
        ;

        if(!empty($rateMembers)) {
            $usersAll = $usersM = $usersS = array();
            foreach($rateMembers as $member) {
                    $usersAll[] = $member['user_id'];
            }

            // search for users who wanted to get mail and made a rate
            $usersM = array_intersect($usersAll, $usersMail);
            if(!empty($usersM)){
                $this->sendMailAboutDeadline($usersM, $transportId, $mailType);
            }

            $usersS = array_intersect($usersAll, $usersSite);
            if(!empty($usersS)) {
                foreach($usersS as $user) {
                    $obj = array(
                        'user_id' => $user,
                        'transport_id' => $transportId,
                        'status' => 1,
                        'type' => 1,
                        'event_type' => $messageType,
                    );

                    Yii::app()->db->createCommand()->insert('user_event',$obj);
                }
            }
        }
    }

    // Search for recently added records
    public function newTransport()
    {
        $transportIds = '';
        $usersInternational = $usersInternationalSite = $usersLocal = $usersLocalSite = $usersInternationalAndLocal = array();
        $transportNew = Yii::app()->db->createCommand()
            ->select('id, type, location_from, location_to')
            ->from('transport')
            ->where('new_transport = :status', array(':status' => 1))
            ->queryAll()
        ;

        $count = count($transportNew);
        $transportIdType = array(0 => array(), 1 => array());
        
        if($count){
            foreach($transportNew as $transport) {
                if(!empty($transportIds)) $transportIds .= ', ';
                $id = $transport['id'];
                $transportIds .= $id;

                if($transport['type']){
                    $transportIdType[1][$id]['id'] = $id;
                    $transportIdType[1][$id]['from'] = $transport['location_from'];
                    $transportIdType[1][$id]['to'] = $transport['location_to'];
                } else {
                    $transportIdType[0][$id]['id'] = $id;
                    $transportIdType[0][$id]['from'] = $transport['location_from'];
                    $transportIdType[0][$id]['to'] = $transport['location_to'];
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
                $temp = Yii::app()->db->createCommand()
                    ->select('user_id')
                    ->from('user_field')
                    ->where('site_transport_create_1 = :type', array(':type' => true))
                    ->queryAll()
                ;
                foreach($temp as $t){
                    $usersInternationalSite[] = $t['user_id'];
                }
                
                $usersInternational = $this->searchUsers('mail_transport_create_1', $usersInternational);
                $usersInternationalSite = $this->searchUsers('site_transport_create_1', $usersInternationalSite);
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

                $temp = Yii::app()->db->createCommand()
                    ->select('user_id')
                    ->from('user_field')
                    ->where('site_transport_create_2 = :type', array(':type' => true))
                    ->queryAll()
                ;
                foreach($temp as $t){
                    $usersLocalSite[] = $t['user_id'];
                }

                $usersLocal = $this->searchUsers('mail_transport_create_2', $usersLocal);
                $usersLocalSite = $this->searchUsers('site_transport_create_2', $usersLocalSite);
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
            /********************************************************/
            if(!empty($usersInternationalSite)){
                $this->saveNewTransportEvent($transportIdType[0], $usersInternationalSite);
            }

            if(!empty($usersLocalSite)){
                $this->saveNewTransportEvent($transportIdType[1], $usersLocalSite);
            }
        }
    }

    public function saveNewTransportEvent($transportIds, $users)
    {
        foreach($users as $user){
            foreach($transportIds as $transportId){
                $obj = array(
                    'user_id' => $user,
                    'transport_id' => $transportId,
                    'status' => 1,
                    'type' => 1,
                    'event_type' => 3,
                );

                Yii::app()->db->createCommand()->insert('user_event',$obj);
            }
        }
    }

    // Send mail with recently added transports
    public function sendMailAboutNew($users, $transportIds, $type = 2)
    {
        $subject = 'Уведомление о появлении новых перевозок';

        switch($type){
            case 0: $subject = 'Уведомление о появлении новых международных перевозок'; break;
            case 1: $subject = 'Уведомление о появлении новых локальных перевозок'; break;
        }

        $message = "<p>На бирже перевозок ЛБР АгроМаркет появились новые перевозки. </p>";

        if($type == 0 || $type == 2){
           $message .= "<p><b>Международные</b> перевозки: </p>";
           foreach($transportIds[0] as $item){
               $message .= '<p><a href="http://exchange.lbr.ru/transport/description/'.$item['id'].'">'.$item['from'].'-'.$item['to'].'</a></p>';
           }
        } 
        if($type == 1 || $type == 2){
           $message .= "<p><b>Локальные</b> перевозки:</p>";
           foreach($transportIds[1] as $item){
               $message .= '<p><a href="http://exchange.lbr.ru/transport/description/'.$item['id'].'">'.$item['from'].'-'.$item['to'].'</a></p>';
           }
        }	
        
        foreach($users as $userId){
            $this->sendMail($userId, $subject, $message);			
        }
    }

    // Send mail about transport's deadline
    public function sendMailAboutDeadline($users, $transportId, $mailType)
    {
        $subject = 'Уведомление';
        $message = '';

        if($mailType == 'mail_deadline'){
            $message .= "<p>Перевозка с номером " . $transportId . " закрыта.</p>";
            $subject = 'Уведомление о завершении перевозки';
        } else if($mailType == 'mail_before_deadline'){
            $message .= "<p>Перевозка с номером " . $transportId . " будет закрыта через " . Yii::app()->params['minNotify'] . " минут.</p>";
            $subject = 'Уведомление о скором завершении перевозки';
        } else {
            $message .= "<p>Ваше предложение по перевозке с номером " . $transportId . " было перебито.</p>";
        }

        foreach($users as $userId){
           $this->sendMail($userId, $subject, $message);
        }
    }

    public function searchUsers($field, $array)
    {
        $temp = Yii::app()->db->createCommand()
            ->select('user_id')
            ->from('user_field')
            ->where($field . ' = :type', array(':type' => true))
            ->queryAll()
        ;
        foreach($temp as $t){
            $array[] = $t['user_id'];
        }
        return $array;
    }

    public function sendMail($userId, $subject, $message)
    {
        $user = Yii::app()->db->createCommand()
            ->select()
            ->from('user')
            ->where('id = :id', array(':id' => $userId))
            ->queryRow()
        ;
        
        $email = new TEmail;
        $email->from_email = Yii::app()->params['adminEmail'];
        $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
        $email->to_email   = 'tttanyattt@mail.ru';//$user['email'];
        $email->to_name    = '';
        $email->subject    = $subject;
        $email->type = 'text/html';
        $email->body = "<h1>Уважаемый(ая) " . $user['name'] . " " . $user['surname'] . ", </h1>" . 
            $message . "<hr><h5>Это сообщение является автоматическим, на него не нужно отвечать.</h5>"
        ;
        $email->sendMail();
    }
}
