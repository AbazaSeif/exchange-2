<?php
class CronCommand extends CConsoleCommand
{
    public function run($args)
    {   
        $this->deadlineTransport();
        $this->beforeDeadlineTransport();
        $this->newTransport();
        $this->mailKillRate();
        $this->errorDate();
        $this->checkBlockDate();
    }
    
    public function errorDate()
    {
        //$timeNow = date("Y-m-d H:i");
        $timeNow = date("Y-m-d H:i", strtotime("-1 minutes"));
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
    
    public function checkBlockDate()
    {
        $timeNow = date("Y-m-d");
        $users = Yii::app()->db->createCommand()
            ->select('id')
            ->from('user')
            ->where('block_date like :time', array(':time' => $timeNow . '%'))
            ->queryAll()
        ;

        $count = count($users);
        if($count) {
            foreach($users as $user) {
                $changes = array('status'=>'1');
                $model = User::model()->findByPk($user['id']);
                $message = 'Cron активировал статус для "'.$model->company.'" (блокировка была до '.$model->block_date.')';
                $model->status = 1;
                $model->block_date = null;
                $model->reason = null;
                $model->save();
                User::sendAboutChangeStatus($model, $changes);
                Yii::log($message, 'info');
            }
        }
    }
    
    // Search for transport with deadline
    public function deadlineTransport()
    {
        //$timeNow = date("Y-m-d H:i");
        $timeNow = date("Y-m-d H:i", strtotime("-1 minutes"));
        $transportIds = '';

        $transports = Yii::app()->db->createCommand()
            ->select('id')
            ->from('transport')
            ->where('(date_close like :time and date_close_new IS NULL) or date_close_new like :time', array(':time' => $timeNow . '%'))
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
                $model = Transport::model()->findByPk($transport['id']);
                //if(empty($model->date_close_new)){
                    // send mail to logist
                    $this->sendMailToUsers($transport['id']);
                    $this->getUsers($transport['id'], 'mail_deadline', $usersMail, $usersSite, 1);

                    if(!empty($transportIds)) $transportIds .= ', ';
                    $transportIds .= $transport['id'];
                //}
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
                //$this->sendMailAboutDeadline($usersM, $transportId, $mailType);
                // send mail to users
                $this->sendMailAboutDeadline2($usersM, $transportId, $mailType);
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
            ->select('id, type, location_from, location_to, description')
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
                    $transportIdType[1][$id]['description'] = $transport['description'];
                } else {
                    $transportIdType[0][$id]['id'] = $id;
                    $transportIdType[0][$id]['from'] = $transport['location_from'];
                    $transportIdType[0][$id]['to'] = $transport['location_to'];
                    $transportIdType[0][$id]['description'] = $transport['description'];
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
                //$this->sendMailAboutNew($usersInternational, $transportIdType, 0);
                $this->sendMailAboutNew2($usersInternational, $transportIdType, 0);
            }

            if(!empty($usersLocal)){
                //$this->sendMailAboutNew($usersLocal, $transportIdType, 1);
                $this->sendMailAboutNew2($usersLocal, $transportIdType, 1);
            }

            if(!empty($usersInternationalAndLocal)){
                //$this->sendMailAboutNew($usersInternationalAndLocal, $transportIdType);
                $this->sendMailAboutNew2($usersInternationalAndLocal, $transportIdType);
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
                            $email = new TEmail2;
                            $email->from_email = Yii::app()->params['adminEmail'];
                            $email->from_name = 'Биржа перевозок ЛБР АгроМаркет';
                            $email->to_email = $userElement->email;
                            $email->to_name = '';
                            $email->subject = 'Перебита ставка';
                            $email->type = 'text/html';
                            /*$email->body = '<h1>Уважаемый(ая) ' . $userElement->name . ' ' . $userElement->surname . ', </h1>' .
                                '<div>Ваша ставка для перевозки "'.$transportElement->location_from . ' - ' . $transportElement->location_to.'" была перебита</div>'.
                                '<hr><h5>Это сообщение является автоматическим, на него не нужно отвечать.</h5>'
                            ;*/
                            $email->body = '<!-- Content -->
                                <tr>
                                    <td>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#dfdfdf"></td>
                                                <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#c1c1c1"></td>
                                                <td bgcolor="#ffffff">
                                                    <!-- Main Content -->
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td>
                                                                <img src="http://exchange.lbr.ru/images/mail/content_top.jpg" alt="" border="0" width="620" height="12" style="float: left"/>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="20"></td>
                                                            <td>
                                                                <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="1" height="15" style="height:15px; float: left" alt="" />
                                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                    <tr>
                                                                        <td>
                                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                                                                <tr>
                                                                                    <td class="img" style="font-size:0pt; line-height:0pt; text-align:left; " valign="top" width="185">
                                                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="1" height="25" style="height:25px; float: left" alt="" />
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <a href="http://exchange.lbr.ru/" target="_blank">
                                                                                                        <img src="http://exchange.lbr.ru/images/logo.png" alt="" border="0" width="179" height="66" style="float: left"/>
                                                                                                    </a>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="20" height="1" style="width:20px" alt="" style="float: left"/>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </table>
                                                                                    </td>
                                                                                    <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" valign="top" width="20"><img src="http://exchange.lbr.ru/images/mail/img_right_shadow.jpg" alt="" border="0" width="8" height="131" style="float: left"/></td>
                                                                                    <td class="text" style="margin: 0; color:#a1a1a1; font-family:Verdana; font-size:12px; line-height:18px; text-align:left" valign="top">
                                                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                                                                            <tr>
                                                                                                <td style="color:#666666; font-family:Verdana; font-size:20px; line-height:24px; text-align:left; font-weight:normal">
                                                                                                    Перебита ставка
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="1" height="10" style="height:10px; float: left" alt="" />
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td style="width: 100%; padding-top: 10px; padding-bottom: 10px; color:#666666; font-family:Verdana; font-size:14px; line-height:20px; text-align:left; font-weight:normal">
                                                                                                    Ваша ставка была перебита:
                                                                                                    <br/><br/>
                                                                                                    <a href="http://exchange.lbr.ru/transport/description/id/29/" class="link-u" style="color:#2b9208; text-decoration:underline" target="_blank">
                                                                                                        <span class="link-u" style="color:#008672; font-weight: bold; text-decoration:underline">
                                                                                                        ' . $transportElement->location_from . ' - ' . $transportElement->location_to . '
                                                                                                        </span>
                                                                                                    </a>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left; float: left" width="20"></td>
                                                        </tr>
                                                    </table>
                                                    <img src="http://exchange.lbr.ru/images/mail/content_bottom.jpg" alt="" border="0" width="620" height="20" style="float: left"/>
                                                    <!-- END Main Content -->
                                                </td>
                                                <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#c1c1c1"></td>
                                                <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#dfdfdf"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <!-- END Content -->';
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
        $subject = 'Уведомление о появлении новых заявок на перевозку';

        switch($type) {
            case 0: $subject = 'Уведомление о появлении новых международных заявок на перевозку'; break;
            case 1: $subject = 'Уведомление о появлении новых региональных заявок на перевозку'; break;
        }

        $message = "<p>На бирже перевозок ЛБР АгроМаркет появились новые заявки на перевозку. </p>";

        if($type == 0 || $type == 2){
           $message .= "<p><b>Международные</b> перевозки: </p>";
           foreach($transportIds[0] as $item){
               $message .= '<p><a href="http://exchange.lbr.ru/transport/description/id/'.$item['id'].'/">'.$item['from'].' &mdash; '.$item['to'].'</a></p>';
           }
        }
        if($type == 1 || $type == 2){
           $message .= "<p><b>Региональные</b> перевозки:</p>";
           foreach($transportIds[1] as $item){
               $message .= '<p><a href="http://exchange.lbr.ru/transport/description/id/'.$item['id'].'/">'.$item['from'].'-'.$item['to'].'</a></p>';
           }
        }	
        
        foreach($users as $userId){
            $this->sendMail($userId, $subject, $message);	
        }
    }

    public function sendMailAboutNew2($users, $transportIds, $type = 2)
    {
        $message = '';
        $subject = 'Уведомление о появлении новых заявок на перевозку';
        $transportCount = false;
        /*
        switch($type) {
            case 0: $subject = 'Уведомление о появлении новых международных заявок на перевозку'; break;
            case 1: $subject = 'Уведомление о появлении новых региональных заявок на перевозку'; break;
        }
        */

        if($type == 0 || $type == 2) {
           //$message .= "<p><b>Международные</b> перевозки: </p>";
           if($transportCount) {
               $message .= '
                   <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <img src="http://exchange.lbr.ru/images/mail/separator.jpg" alt="" border="0" width="581" height="1" style="border: 0; float: left"/>
                            </td>
                        </tr>
                   </table>'
               ;
           }
           foreach($transportIds[0] as $item){
               //$message .= '<p><a href="http://exchange.lbr.ru/transport/description/'.$item['id'].'">'.$item['from'].' &mdash; '.$item['to'].'</a></p>';
           
               $message .= 
                       '<table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="text" style="color:#a1a1a1; font-family:Verdana; font-size:12px; line-height:18px; text-align:left" valign="top">
                                    <table>
                                        <tr style="color:#000000; font-family:Verdana; font-size:18px; line-height:20px; text-align:left; font-weight:normal">
                                            <td style="padding-top: 15px">
                                            '.$item['from'].' &mdash; '.$item['to'].'
                                            </td>
                                        </tr>
                                        <tr style="color: #a1a1a1; font-family:Verdana; font-size:12px; line-height:18px; text-align:left; font-weight:normal">
                                            <td>
                                            '.$item['description'].'
                                            </td>
                                        </tr>
                                        <tr style="font-family:Verdana; font-size:12px; line-height:18px; text-align:left; font-weight:normal">
                                            <td style="padding-top: 15px; padding-bottom: 10px">
                                                <a href="http://exchange.lbr.ru/transport/description/id/'.$item['id'].'/" class="link-u" style="color:#2b9208; text-decoration:underline" target="_blank"><span class="link-u" style="color:#008672; text-decoration:underline">Посмотреть подробнее</span></a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>'
               ;
           }
           $transportCount = true;
        }
        if($type == 1 || $type == 2) {
           //$message .= "<p><b>Региональные</b> перевозки:</p>";
           if($transportCount) {
               $message .= '
                   <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <img src="http://exchange.lbr.ru/images/mail/separator.jpg" alt="" border="0" width="581" height="1" style="border: 0; float: left"/>
                            </td>
                        </tr>
                   </table>'
               ;
           }
           foreach($transportIds[1] as $item){
               //$message .= '<p><a href="http://exchange.lbr.ru/transport/description/'.$item['id'].'">'.$item['from'].'-'.$item['to'].'</a></p>';
               $message .= 
                       '<table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="text" style="color:#a1a1a1; font-family:Verdana; font-size:12px; line-height:18px; text-align:left" valign="top">
                                    <table>
                                        <tr style="color:#000000; font-family:Verdana; font-size:18px; line-height:20px; text-align:left; font-weight:normal">
                                            <td style="padding-top: 15px">
                                            '.$item['from'].' &mdash; '.$item['to'].'
                                            </td>
                                        </tr>
                                        <tr style="color: #666666; font-family:Verdana; font-size:12px; line-height:18px; text-align:left; font-weight:normal">
                                            <td>
                                            '.$item['description'].'
                                            </td>
                                        </tr>
                                        <tr style="font-family:Verdana; font-size:12px; line-height:18px; text-align:left; font-weight:normal">
                                            <td style="padding-top: 15px; padding-bottom: 10px">
                                                <a href="http://exchange.lbr.ru/transport/description/id/'.$item['id'].'/" class="link-u" style="color:#2b9208; text-decoration:underline" target="_blank"><span class="link-u" style="color:#008672; text-decoration:underline">Посмотреть подробнее</span></a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>'
               ;
           }
           $transportCount = true;
        }
        
        foreach($users as $userId){
            $this->sendMail2($userId, $subject, $message);	
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
    
    public function sendMail2($userId, $subject, $message)
    {
        $user = Yii::app()->db->createCommand()
            ->select()
            ->from('user')
            ->where('id = :id', array(':id' => $userId))
            ->queryRow()
        ;
        if(!empty($user['email'])) {
            $name = $user['name'];
            if(!empty($user['secondname'])) $name .= ' ' . $user['secondname'];
            if(!empty($name)) $name .= ',';

            $email = new TEmail2;
            $email->from_email = Yii::app()->params['adminEmail'];
            $email->from_name = 'Биржа перевозок ЛБР АгроМаркет';
            $email->to_email = $user['email'];
            $email->to_name = '';
            $email->subject = $subject;
            $email->type = 'text/html';
            $email->body = '<!-- Content -->
                <tr>
                    <td>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#dfdfdf"></td>
                                <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#c1c1c1"></td>
                                <td bgcolor="#ffffff">
                                    <!-- Main Content -->
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td>
                                                <img src="http://exchange.lbr.ru/images/mail/content_top.jpg" alt="" border="0" width="620" height="12" style="float: left"/>
                                            </td>
                                        </tr>
                                    </table>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="20"></td>
                                            <td>
                                                <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="1" height="15" style="height:15px; float: left" alt="" />
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td>
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                                                <tr>
                                                                    <td class="img" style="font-size:0pt; line-height:0pt; text-align:left; " valign="top" width="185">
                                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                            <tr>
                                                                                <td>
                                                                                    <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="1" height="25" style="height:25px; float: left" alt="" />
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <a href="http://exchange.lbr.ru/" target="_blank">
                                                                                        <img src="http://exchange.lbr.ru/images/logo.png" alt="" border="0" width="179" height="66" style="float: left"/>
                                                                                    </a>
                                                                                </td>
                                                                                <td>
                                                                                    <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="20" height="1" style="width:20px" alt="" style="float: left"/>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                    <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" valign="top" width="20"><img src="http://exchange.lbr.ru/images/mail/img_right_shadow.jpg" alt="" border="0" width="8" height="131" style="float: left"/></td>
                                                                    <td class="text" style="margin: 0; color:#a1a1a1; font-family:Verdana; font-size:12px; line-height:18px; text-align:left" valign="top">
                                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                                                            <tr>
                                                                                <td style="color:#000000; font-family:Verdana; font-size:20px; line-height:24px; text-align:left; font-weight:normal">
                                                                                    '.$name.'
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="1" height="5" style="height:5px; float: left" alt="" />
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="color:#666666; font-family:Verdana; font-size:12px; line-height:18px; text-align:left; font-weight:normal">
                                                                                    Спешим уведомить Вас о том, что на бирже появились новые заявки на перевозку.
                                                                                    <br /><br />
                                                                                    <a href="http://exchange.lbr.ru/" class="link-u" style="color:#2b9208; text-decoration:underline" target="_blank"><span class="link-u" style="color:#008672; text-decoration:underline">Перейти на биржу</span></a>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td>
                                                                        <img src="http://exchange.lbr.ru/images/mail/separator.jpg" alt="" border="0" width="581" height="1" style="border: 0; float: left"/>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                                '.$message.'
                                            </td>
                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left; float: left" width="20"></td>
                                        </tr>
                                    </table>
                                    <img src="http://exchange.lbr.ru/images/mail/content_bottom.jpg" alt="" border="0" width="620" height="20" style="float: left"/>
                                    <!-- END Main Content -->
                                </td>
                                <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#c1c1c1"></td>
                                <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#dfdfdf"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- END Content -->'
            ;
            
            $email->sendMail();
        }
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
    //sendMailToLogist2
    public function sendMailToUsers($transportId, $email = null, $subject = null, $message = null)
    {
        $transport = Transport::model()->findByPk($transportId);
        
        if(empty($subject)) $subject = 'Закрыта заявка на перевозку';
        if(empty($message)) {
            $message = '<a href="http://exchange.lbr.ru/transport/description/id/'.$transportId.'/" class="link-u" style="color:#2b9208; text-decoration:underline" target="_blank">
                <span class="link-u" style="color:#008672; font-weight: bold; text-decoration:underline">
                ' . $transport->location_from . ' &mdash; ' . $transport->location_to . '
                </span>
            </a>';
        }
        
        $email = new TEmail2;
        $email->from_email = Yii::app()->params['adminEmail'];
        $email->from_name = 'Биржа перевозок ЛБР АгроМаркет';        
        $email->to_name = '';
        $email->subject = 'Закрыта заявка на перевозку';
        $email->type = 'text/html';
        $email->body = '<!-- Content -->
                <tr>
                    <td>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#dfdfdf"></td>
                                <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#c1c1c1"></td>
                                <td bgcolor="#ffffff">
                                    <!-- Main Content -->
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td>
                                                <img src="http://exchange.lbr.ru/images/mail/content_top.jpg" alt="" border="0" width="620" height="12" style="float: left"/>
                                            </td>
                                        </tr>
                                    </table>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="20"></td>
                                            <td>
                                                <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="1" height="15" style="height:15px; float: left" alt="" />
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td>
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                                                <tr>
                                                                    <td class="img" style="font-size:0pt; line-height:0pt; text-align:left; " valign="top" width="185">
                                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                            <tr>
                                                                                <td>
                                                                                    <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="1" height="25" style="height:25px; float: left" alt="" />
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <a href="http://exchange.lbr.ru/" target="_blank">
                                                                                        <img src="http://exchange.lbr.ru/images/logo.png" alt="" border="0" width="179" height="66" style="float: left"/>
                                                                                    </a>
                                                                                </td>
                                                                                <td>
                                                                                    <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="20" height="1" style="width:20px" alt="" style="float: left"/>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                    <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" valign="top" width="20"><img src="http://exchange.lbr.ru/images/mail/img_right_shadow.jpg" alt="" border="0" width="8" height="131" style="float: left"/></td>
                                                                    <td class="text" style="margin: 0; color:#a1a1a1; font-family:Verdana; font-size:12px; line-height:18px; text-align:left" valign="top">
                                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                                                            <tr>
                                                                                <td style="color:#666666; font-family:Verdana; font-size:20px; line-height:24px; text-align:left; font-weight:normal">
                                                                                    '.$subject.'
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="1" height="10" style="height:10px; float: left" alt="" />
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="width: 100%; padding-top: 10px; padding-bottom: 10px; color:#666666; font-family:Verdana; font-size:14px; line-height:20px; text-align:left; font-weight:normal">
                                                                                    '.$message.'
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left; float: left" width="20"></td>
                                        </tr>
                                    </table>
                                    <img src="http://exchange.lbr.ru/images/mail/content_bottom.jpg" alt="" border="0" width="620" height="20" style="float: left"/>
                                    <!-- END Main Content -->
                                </td>
                                <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#c1c1c1"></td>
                                <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#dfdfdf"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- END Content -->'
        ;
        if(empty($email)) {
            //send Mail to logist
            if($transport->type == 0) $email->to_email = Yii::app()->params['logistEmailInternational'];
            else $email->to_email = Yii::app()->params['logistEmailRegional']; 
        }
        
        $email->sendMail();
    }

    public function sendMailAboutDeadline2($users, $transportId, $mailType)
    {
        $subject = 'Закрыта заявка на перевозку';
        $message = '';
        $transport = Transport::model()->findByPk($transportId);
        if($mailType == 'mail_deadline'){
            //$subject = 'Закрыта заявка на перевозку';
            $message .= '<a href="http://exchange.lbr.ru/transport/description/id/'.$transportId.'/" class="link-u" style="color:#2b9208; text-decoration:underline" target="_blank">
                <span class="link-u" style="color:#008672; font-weight: bold; text-decoration:underline">
                ' . $transport->location_from . ' &mdash; ' . $transport->location_to . '
                </span>
            </a>';
        } else if($mailType == 'mail_before_deadline'){
            $subject = 'Уведомление';
            $message .= 'Заявка на перевозку будет закрыта через 30 минут:
                      <br/><br/>' .
                    '<a href="http://exchange.lbr.ru/transport/description/id/'.$transportId.'/" class="link-u" style="color:#2b9208; text-decoration:underline" target="_blank">
                <span class="link-u" style="color:#008672; font-weight: bold; text-decoration:underline">
                ' . $transport->location_from . ' &mdash; ' . $transport->location_to . '
                </span>
            </a>
            <br/><br/>
            будет закрыта через '  . Yii::app()->params['minNotify'] . ' минут.';
        } /*else {
            $message .= 'Ваша ставка была перебита:' . 
                '<br/><br/>' .
                '<a href="http://exchange.lbr.ru/transport/description/id/'.$transportId.'/" class="link-u" style="color:#2b9208; text-decoration:underline" target="_blank">
                <span class="link-u" style="color:#008672; font-weight: bold; text-decoration:underline">
                ' . $transport->location_from . ' &mdash; ' . $transport->location_to . '
                </span>
            </a>';
        }*/

        foreach($users as $userId){
           $user = User::model()->findByPk($userId);
           $this->sendMailToUsers($transportId, $user->email, $subject, $message);
        }
    }
}
