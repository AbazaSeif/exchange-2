<?php
class CronCommand extends CConsoleCommand
{
    public function run($args)
    {   
        /*$email = new TEmail;
        $email->from_email = 'support.ex@lbr.ru';
        $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
        $email->to_email   = 'tttanyattt@mail.ru';
        $email->to_name    = '';
        $email->subject    = 'Test';
        $email->type       = 'text/html';
        $email->body = '<div>test</div>
          <hr/><h5>Это уведомление является автоматическим, на него не следует отвечать.</h5>
        ';
        
        if($email->sendMail()) {
            Yii::log('cron call - send', 'warning');
        } else {
            Yii::log('cron call - error', 'warning');
        }
        */
        /*if(mail('tttanyattt@mail.ru', 'My Subject', 'test')) {
            mail('cheshenkov@lbr.ru', 'My Subject', 'test');
            mail('forvlasov@gmail.com', 'My Subject', 'test');
            Yii::log('cron call - send', 'warning');
        } else {
            Yii::log('cron call - error with send', 'warning');
        }*/
        
        
        /*$email = new TEmail;
        $email->from_email = 'tttanyattt@mail.ru';
        $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
        $email->to_email   = 'tttanyattt@mail.ru';
        $email->to_name    = '';
        $email->subject    = 'Test';
        $email->type       = 'text/html';
        $email->body = '<div>cron test</div>
          <hr/><h5>Message from Cron.</h5>
        ';
        $email->sendMail();
        */
        $this->testMail('krilova@lbr.ru');
        $this->testMail('cheshenkov@lbr.ru');
        $this->testMail('xchesh666@gmail.com');
        //$this->testMail('forvlasov@gmail.com');
        //$this->testMail('vlasov@lbr.ru');
        
        //////////////////////////////////
        
        /*$this->deadlineTransport();
        $this->beforeDeadlineTransport();
        $this->newTransport();
        $this->mailKillRate();
        $this->errorDate();
         * */
         
    }
    
    public function testMail($email)
    {
        $email = new TEmail;
        $email->from_email = 'support.ex@lbr.ru';
        $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
        $email->to_email   = $email;
        $email->to_name    = '';
        $email->subject    = 'Test';
        $email->type       = 'text/html';
        $email->body       = '<div>cron test</div>
            <hr/><h5>Message from Cron.</h5>
        ';
        $email->sendMail();
        //Yii::log('cron call - function testMail() works', 'warning');
    }
    
    public function errorDate()
    {
        $timeNow = date("Y-m-d H:i");
        
        $transports = Yii::app()->db->createCommand()
            ->select('id')
            ->from('transport')
            ->where('status = 1 and date_close between "1980-01-01" and "' . $timeNow. '"')
            ->queryAll()
        ;
        foreach($transports as $transport){
            Transport::model()->updateByPk($transport['id'], array('status' => 0));
        }
    }
    
    // Search for transport with deadline
    public function deadlineTransport()
    {
        $timeNow = date("Y-m-d H:i");
        $transportIds = '';

        $transports = Yii::app()->db->createCommand()
            ->select('id')
            ->from('transport')
            ->where('date_close like :time', array(':time' => $timeNow . '%'))
            ->queryAll()
        ;

        $count = count($transports);

        if($count) {
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

            foreach($transports as $transport){
                $this->sendMailToLogist($transport['id']);
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
        $time = date("Y-m-d H:i", strtotime("+" . Yii::app()->params['minNotify'] . " minutes"));
        $transports = Yii::app()->db->createCommand()
            ->select('id')
            ->from('transport')
            ->where('date_close like :time', array(':time' => $time . '%'))
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

            //$usersS = array_intersect($usersAll, $usersSite);
            $usersS = $usersM;
            if(!empty($usersS)) {
                foreach($usersS as $user) {
                    $obj = array(
                        'user_id' => $user,
                        'transport_id' => $transportId,
                        'status' => 1,
                        'status_online' => 1,
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

                if($transport['type']) {
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
                $usersInternational = $this->searchUsers('mail_transport_create_1', $usersInternational);
                $usersInternationalSite = $usersInternational;
            }

            if(!empty($transportIdType[1])) { // local transportation
                $usersLocal = $this->searchUsers('mail_transport_create_2', $usersLocal);
                $usersLocalSite = $usersLocal;
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
          
            if(!empty($usersInternationalSite)){
                $this->saveNewTransportEvent($transportIdType[0], $usersInternationalSite, 0);
            }

            if(!empty($usersLocalSite)){
                $this->saveNewTransportEvent($transportIdType[1], $usersLocalSite, 1);
            }
        }
    }

    public function saveNewTransportEvent($transportIds, $users, $type)
    {
        foreach($users as $user){
            foreach($transportIds as $transportId){
                $event = 3;
                if($type) $event = 4;
                $obj = array(
                    'user_id' => $user,
                    'transport_id' => $transportId['id'],
                    'status' => 1,
                    'status_online' => 1,
                    'type' => 1,
                    'event_type' => $event,
                );

                Yii::app()->db->createCommand()->insert('user_event',$obj);
            }
        }
    }
    
    public function mailKillRate()
    {
        $transportKillRate = Yii::app()->db->createCommand()
            ->select('transport_id, prev_id, id')
            ->from('user_event')
            ->where('status = :status and event_type = :type', array(':status' => 1, ':type' => 5))
            ->queryAll()
        ;

        $count = count($transportKillRate);

        if($count){
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
            
            if(isset($users)){
                foreach($transportKillRate as $transport) {
                    if(in_array($transport['prev_id'], $users)){
                        $transportElement = Transport::model()->findByPk($transport['transport_id']);
                        $userElement = User::model()->findByPk($transport['prev_id']);
                        if(isset($userElement->email)){
                            $email = new TEmail;
                            $email->from_email = Yii::app()->params['adminEmail'];
                            $email->from_name = 'Биржа перевозок ЛБР АгроМаркет';
                            $email->to_email = $userElement->email;
                            $email->to_name = '';
                            $email->subject = 'Перебита ставка';
                            $email->type = 'text/html';
                            $email->body = '<h1>Уважаемый(ая) ' . $userElement->name . ' ' . $userElement->surname . ', </h1>' .
                                '<div>Ваша ставка для перевозки "'.$transportElement->location_from . ' - ' . $transportElement->location_to.'" была перебита</div>'.
                                '<hr><h5>Это сообщение является автоматическим, на него не нужно отвечать.</h5>'
                            ;
                            $email->sendMail();
                        }
                        UserEvent::model()->updateByPk($transport['id'], array('status' => 0));
                    }
                }
            }
        }
    }

    // Send mail with recently added transports
    public function sendMailAboutNew($users, $transportIds, $type = 2)
    {
        $subject = 'Уведомление о появлении новых перевозок';

        switch($type){
            case 0: $subject = 'Уведомление о появлении новых международных перевозок'; break;
            case 1: $subject = 'Уведомление о появлении новых региональных перевозок'; break;
        }

        $message = "<p>На бирже перевозок ЛБР АгроМаркет появились новые перевозки. </p>";

        if($type == 0 || $type == 2){
           $message .= "<p><b>Международные</b> перевозки: </p>";
           foreach($transportIds[0] as $item){
               $message .= '<p><a href="http://exchange.lbr.ru/transport/description/'.$item['id'].'">'.$item['from'].' &mdash; '.$item['to'].'</a></p>';
           }
        }
        if($type == 1 || $type == 2){
           $message .= "<p><b>Региональные</b> перевозки:</p>";
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
        $transport = Transport::model()->findByPk($transportId);
        if($mailType == 'mail_deadline'){
            $message .= '<p>Заявка на перевозку "<a href="http://exchange.lbr.ru/transport/description/id/' . $transportId . '">' . $transport['location_from'] . ' &mdash; ' . $transport['location_to'] . '</a>" закрыта.</p>';
            $subject = 'Уведомление о завершении перевозки';
        } else if($mailType == 'mail_before_deadline'){
            $message .= '<p>Заявка на перевозку "<a href="http://exchange.lbr.ru/transport/description/id/' . $transportId . '">' . $transport['location_from'] . ' &mdash; ' . $transport['location_to'] . '</a>" будет закрыта через ' . Yii::app()->params['minNotify'] . ' минут.</p>';
            $subject = 'Уведомление о скором закрытии заявки на перевозку';
        } else {
            $message .= '<p>Ваше предложение для перевозки "<a href="http://exchange.lbr.ru/transport/description/id/' . $transportId . '">' . $transport['location_from'] . ' &mdash; ' . $transport['location_to'] . '</a>" было перебито.</p>';
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
        foreach($temp as $t) {
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
        $email->from_name = 'Биржа перевозок ЛБР АгроМаркет';
        $email->to_email = $user['email'];
        $email->to_name = '';
        $email->subject = $subject;
        $email->type = 'text/html';
        $email->body = "<h1>Уважаемый(ая) " . $user['name'] . " " . $user['surname'] . ", </h1>" .
            $message . "<hr><h5>Это сообщение является автоматическим, на него не нужно отвечать.</h5>"
        ;
        $email->sendMail();
    }
    
    public function sendMailToLogist($transportId)
    {
        $transport = Transport::model()->findByPk($transportId);
        $email = new TEmail;
        $email->from_email = Yii::app()->params['adminEmail'];
        $email->from_name = 'Биржа перевозок ЛБР АгроМаркет';
        
        if($transport->type == 0) $email->to_email = Yii::app()->params['logistEmailInternational'];
        else $email->to_email = Yii::app()->params['logistEmailRegional'];
        
        $email->to_name = '';
        $email->subject = 'Заявка на перевозку закрыта';
        $email->type = 'text/html';
        $email->body = '<p>Заявка на перевозку "<a href="http://exchange.lbr.ru/transport/description/id/' . $transportId . '">' . $transport->location_from . ' &mdash; ' . $transport->location_to . '</a>" закрыта.</p>'.
            '<hr><h5>Это сообщение является автоматическим, на него не нужно отвечать.</h5>';
        ;
        
        $email->sendMail();
    }
}