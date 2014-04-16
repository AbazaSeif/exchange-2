<?php
class SiteController extends Controller
{
    public function actionIndex($s = null)
    {
        $this->forward('/transport/i/');
    }
    
    public function actions()
    {
        return array(
            'captcha'=>array(
                'class'=>'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
        );
    }

    public function actionLogin()
    {
        $model = new LoginForm;

        // if it is ajax validation request
        if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if(isset($_POST['LoginForm']))
        {
            $model->attributes=$_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->render('login',array('model'=>$model));
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
        
        $this->render('item', array('rateData' => $dataProvider, 'transportInfo' => $transportInfo));
    }
    
    /* check if in table user_field doesn't exists row for user and create it */
    public function actionFields()
    {
        $users = Yii::app()->db->createCommand()
            ->select('id')
            ->from('user')
            ->queryAll()
        ;

        if(!empty($users)) {
            foreach($users as $user) {
                $record = UserField::model()->find(array(
                    'select'=>'id',
                    'condition'=>'user_id=:id',
                    'params'=>array(':id'=>$user['id']))
                );
                if(empty($record)){
                    $newFerrymanFields = new UserField;
                    $newFerrymanFields->user_id = $user['id'];
                    $newFerrymanFields->mail_transport_create_1 = false;
                    $newFerrymanFields->mail_transport_create_2 = false;
                    $newFerrymanFields->mail_kill_rate = false;
                    $newFerrymanFields->mail_before_deadline = false;
                    $newFerrymanFields->mail_deadline = true;
                    $newFerrymanFields->with_nds = false;            
                    $newFerrymanFields->save(); 
                }
            }
        }
    }
    /* check if in field date_close is empty */
    public function actionDateClose()
    {
        $users = Yii::app()->db->createCommand()
            ->select('id, date_close')
            ->from('user')
            ->queryAll()
        ;

        if(!empty($users)) {
            foreach($users as $user) {
                if(empty($user['date_close'])){
                    $user = User::model()->findByPk($user['id']);
                    $user->date_close = date("Y-m-d H:i", strtotime($user->date_from . "-" . Yii::app()->params['hoursBefore'] . " hours"));
                    if(!$user->save()) Yii::log($model->getErrors(), 'error');
                }
            }
        }
    }
    
    public function actionFeedback()
    {
        $model = new FeedbackForm();
        if(isset($_POST['FeedbackForm'])) {
            $phone = '';
            if(!empty($_POST['FeedbackForm']['phone'])) $phone = '<p>Телефон: '.$_POST['FeedbackForm']['phone'].'</p>';
            $email = new TEmail;
            $email->from_email = $_POST['FeedbackForm']['email'];
            $email->from_name  = $_POST['FeedbackForm']['surname'] . ' ' . $_POST['FeedbackForm']['name'];
            $email->to_email   = Yii::app()->params['supportEmail'];
            $email->to_name    = '';
            $email->subject    = 'Биржа перевозок "Обратная связь"';
            $email->type = 'text/html';
            $email->body = '<div>'.
                    '<p>Пользователь воспользовался формой обратной связи на "Бирже перевозок ЛБР".</p>'.
                    '<p>'.$_POST['FeedbackForm']['surname'].' '.$_POST['FeedbackForm']['name'].'</p>'.
                    '<p>Email: '.$_POST['FeedbackForm']['email'].'</p>'.
                    $phone.
                    '<p>Текст сообщения: </p>'.
                    '<p>'.$_POST['FeedbackForm']['message'].'</p>'.
                '</div>
                <hr/><h5>Это уведомление является автоматическим, на него не следует отвечать.</h5>
            ';
            $email->sendMail();
            Dialog::message('flash-success', 'Отправлено!', 'Ваше сообщение отправлено');
            $this->redirect('/');
        } else $this->render('feedback', array('model' => $model));
    }
    
    public function actionHelp()
    {
        $this->render('help');
    }
    
    public function actionMailT()
    {
        /*$headers["From"] = "tttanyattt@mail.ru";
        $headers["To"] = "tttanyattt@mail.ru";
        $headers["Subject"] = "User feedback";
        
        $mail = & Mail::factory('smtp', array('host' => 'mail.lbr.ru', 'port' => 25)); 
        $mail->send('tttanyattt@mail.ru', $headers, 'jjjj'); */
        $config['smtp_username'] = 'krilova@lbr.ru';  //Смените на имя своего почтового ящика.
        $config['smtp_port']     = '25'; // Порт работы. Не меняйте, если не уверены.
        $config['smtp_host']     = 'mail.lbr.ru';  //сервер для отправки почты
        $config['smtp_password'] = 'pht76xu';  //Измените пароль
        $config['smtp_debug']   = true;  //Если Вы хотите видеть сообщения ошибок, укажите true вместо false
        $config['smtp_charset']  = 'windows-1251';  //кодировка сообщений. (или UTF-8, итд)
        $config['smtp_from']     = 'test'; //Ваше имя - или имя Вашего сайта. Будет показывать при прочтении в поле "От кого"
        function smtpmail($mail_to, $subject, $message, $headers='') {
            global $config;
            $SEND = "Date: ".date("D, d M Y H:i:s") . " UT\r\n";
            $SEND .=    'Subject: =?'.$config['smtp_charset'].'?B?'.base64_encode($subject)."=?=\r\n";
            if ($headers) $SEND .= $headers."\r\n\r\n";
            else
            {
                    $SEND .= "Reply-To: ".$config['smtp_username']."\r\n";
                    $SEND .= "MIME-Version: 1.0\r\n";
                    $SEND .= "Content-Type: text/plain; charset=\"".$config['smtp_charset']."\"\r\n";
                    $SEND .= "Content-Transfer-Encoding: 8bit\r\n";
                    $SEND .= "From: \"".$config['smtp_from']."\" <".$config['smtp_username'].">\r\n";
                    $SEND .= "To: $mail_to <$mail_to>\r\n";
                    $SEND .= "X-Priority: 3\r\n\r\n";
            }
            $SEND .=  $message."\r\n";
             if( !$socket = fsockopen($config['smtp_host'], $config['smtp_port'], $errno, $errstr, 30) ) {
                if ($config['smtp_debug']) echo $errno."&lt;br&gt;".$errstr;
                return false;
             }

            if (!server_parse($socket, "220", __LINE__)) return false;

            fputs($socket, "HELO " . $config['smtp_host'] . "\r\n");
            if (!server_parse($socket, "250", __LINE__)) {
                if ($config['smtp_debug']) echo '<p>Не могу отправить HELO!</p>';
                fclose($socket);
                return false;
            }
            fputs($socket, "AUTH LOGIN\r\n");
            if (!server_parse($socket, "334", __LINE__)) {
                if ($config['smtp_debug']) echo '<p>Не могу найти ответ на запрос авторизаци.</p>';
                fclose($socket);
                return false;
            }
            fputs($socket, base64_encode($config['smtp_username']) . "\r\n");
            if (!server_parse($socket, "334", __LINE__)) {
                if ($config['smtp_debug']) echo '<p>Логин авторизации не был принят сервером!</p>';
                fclose($socket);
                return false;
            }
            fputs($socket, base64_encode($config['smtp_password']) . "\r\n");
            if (!server_parse($socket, "235", __LINE__)) {
                if ($config['smtp_debug']) echo '<p>Пароль не был принят сервером как верный! Ошибка авторизации!</p>';
                fclose($socket);
                return false;
            }
            fputs($socket, "MAIL FROM: <".$config['smtp_username'].">\r\n");
            if (!server_parse($socket, "250", __LINE__)) {
                if ($config['smtp_debug']) echo '<p>Не могу отправить комманду MAIL FROM: </p>';
                fclose($socket);
                return false;
            }
            fputs($socket, "RCPT TO: <" . $mail_to . ">\r\n");

            if (!server_parse($socket, "250", __LINE__)) {
                if ($config['smtp_debug']) echo '<p>Не могу отправить комманду RCPT TO: </p>';
                fclose($socket);
                return false;
            }
            fputs($socket, "DATA\r\n");

            if (!server_parse($socket, "354", __LINE__)) {
                if ($config['smtp_debug']) echo '<p>Не могу отправить комманду DATA</p>';
                fclose($socket);
                return false;
            }
            fputs($socket, $SEND."\r\n.\r\n");

            if (!server_parse($socket, "250", __LINE__)) {
                if ($config['smtp_debug']) echo '<p>Не смог отправить тело письма. Письмо не было отправленно!</p>';
                fclose($socket);
                return false;
            }
            fputs($socket, "QUIT\r\n");
            fclose($socket);
            return TRUE;
        }

        function server_parse($socket, $response, $line = __LINE__) {
            global $config;
            while (@substr($server_response, 3, 1) != ' ') {
                if (!($server_response = fgets($socket, 256))) {
                    if ($config['smtp_debug']) echo '<p>Проблемы с отправкой почты!</p>'.$response.'<br>'.$line.'<br>';
                    return false;
                }
            }
            if (!(substr($server_response, 0, 3) == $response)) {
                if ($config['smtp_debug']) echo '<p>Проблемы с отправкой почты!</p>'.$response.'<br>'.$line.'<br>';
                return false;
            }
            return true;
        }
        
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset="windows-1251"' . "\r\n";
        $headers .= 'From: tttanyattt@mail.ru'. "\r\n";
        smtpmail('tttanyattt@mail.ru', 'Тема письма', 'Текст письма', $headers);
    }
    
    public function actionRegistration()
    {
        $model = new RegistrationForm;
        /************************/
        /*$email = new TEmail;
        $email->from_email = Yii::app()->params['adminEmail']; // 'cheshenkov@lbr.ru'; //
        $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
        $email->to_email   = 'tttanyattt@mail.ru';//'frenk0510@ya.ru';//'tttanyattt@mail.ru';    //'support.ex@lbr.ru';//$to;
        $email->to_name    = '';
        $email->subject    = 'Заявка на регистрацию';
        $email->type       = 'text/html';
        $email->body = '<div>jjjjjjjjjjjjjjjjjjjj</div>
          <hr/><h5>Это уведомление является автоматическим, на него не следует отвечать.</h5>
        ';
        $email->sendMail();*/
         /************************/
        if (isset($_POST['RegistrationForm'])) {
            $newUser = User::model()->find(array(
                'condition'=>'inn=:inn',
                'params'=>array(':inn'=>$_POST['RegistrationForm']['inn']))
            );
            if(empty($newUser)) {
                $userInfo = array();
                $user = new User();
                $user->attributes = $_POST['RegistrationForm'];
                $user->status = User::USER_NOT_CONFIRMED;
                $user->company = $_POST['RegistrationForm']['ownership'] . ' "' . trim($_POST['RegistrationForm']['company']) . '"';
                $user->password = crypt($_POST['RegistrationForm']['password'], User::model()->blowfishSalt());
                $user->inn = trim($_POST['RegistrationForm']['inn']);
                $user->country = trim($_POST['RegistrationForm']['country']);
                $user->region = trim($_POST['RegistrationForm']['region']);
                $user->city = trim($_POST['RegistrationForm']['city']);
                $user->district = trim($_POST['RegistrationForm']['district']);
                $user->secondname = trim($_POST['RegistrationForm']['secondname']);
                $user->surname = trim($_POST['RegistrationForm']['surname']);
                $user->phone = trim($_POST['RegistrationForm']['phone']);
                $user->email = trim($_POST['RegistrationForm']['email']);
                
                if($user->save()) {
                    $newFerrymanFields = new UserField;
                    $newFerrymanFields->user_id = $user->id;
                    $newFerrymanFields->mail_transport_create_1 = false;
                    $newFerrymanFields->mail_transport_create_2 = false;
                    $newFerrymanFields->mail_kill_rate = false;
                    $newFerrymanFields->mail_before_deadline = false;
                    $newFerrymanFields->mail_deadline = true;
                    $newFerrymanFields->with_nds = (bool)$_POST['RegistrationForm']['nds'];
                    if((int)$_POST['RegistrationForm']['show'] == 0){
                        $newFerrymanFields->show_intl = true;
                        $newFerrymanFields->show_regl = true;
                    } else if((int)$_POST['RegistrationForm']['show'] == 1){
                        $newFerrymanFields->show_intl = true;
                        $newFerrymanFields->show_regl = false;
                    } else {
                        $newFerrymanFields->show_intl = false;
                        $newFerrymanFields->show_regl = true;
                    }
                    $newFerrymanFields->save();

                    $this->sendMail(Yii::app()->params['supportEmail'], 1, $_POST['RegistrationForm']);
                    $this->sendMail($_POST['email'], 0, $_POST['RegistrationForm']);

                    Dialog::message('flash-success', 'Отправлено!', 'Ваша заявка отправлена. Вы получите на почту инструкции по активации, когда ваша заявка будет рассмотрена. Спасибо за интерес, проявленный к нашей компании');
                } else Yii::log($user->getErrors(), 'error');
            } else {
                Dialog::message('flash-success', 'Внимание!', 'Пользователь с таким ИНН/УНП уже зарегистрирован в базе, если у Вас возникли проблемы с авторизацией свяжитесь с нашим отделом логистики. ');  
            }
            $this->redirect('/site/login/');
        } else {
            $this->render('registration', array('model' => $model));
        }
    }
    
    public function actionRestore()
    { 
        $model = new RestoreForm;
        if(isset($_POST['RestoreForm'])) {
            $user = array();
            $input = $_POST['RestoreForm']['inn'];
            if(!empty($_POST['RestoreForm']['inn'])) {
                if(is_numeric($input)) {
                    $user = User::model()->find(array(
                        'condition'=>'inn=:inn',
                        'params'=>array(':inn'=>$input))
                    );
                } else {
                    $user = User::model()->find(array(
                        'condition'=>'email=:email',
                        'params'=>array(':email'=>$input))
                    );
                }
                if(!empty($user)) {
                    if($user->status != User::USER_NOT_CONFIRMED && $user->status != User::USER_TEMPORARY_BLOCKED && $user->status != User::USER_BLOCKED){
                        if($user->email) {
                            $password = User::randomPassword();
                            $user->password = crypt($password, User::model()->blowfishSalt());
                            if($user->save()) {
                                // send mail to ferryman with new password
                                $email = new TEmail;
                                $email->from_email = Yii::app()->params['adminEmail'];
                                $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
                                $email->to_email   = $user->email;
                                $email->to_name    = '';
                                $email->subject    = 'Смена пароля';
                                $email->type = 'text/html';
                                $email->body = '<div>'.
                                        '<p>Ваш пароль для "Онлайн биржи перевозок ЛБР-АгроМаркет" был изменен:</p>'.
                                        '<p>Новый пароль: <b>'.$password.'</b></p>'.
                                        '<p>Для смены пароля зайдите в свой аккаунт и воспользуйтесь вкладкой "Настроки->Смена пароля"</p>'.
                                    '</div>
                                    <hr/><h5>Это уведомление является автоматическим, на него не следует отвечать.</h5>
                                ';
                                $email->sendMail();
                                Dialog::message('flash-success', 'Отправлено!', 'Новый пароль был выслан на Ваш почтовый ящик.');
                            } else Yii::log($user->getErrors(), 'error');
                        } else {
                            Dialog::message('flash-success', 'Внимание!', 'Ваша заявка на восстановление доступа отправлена, в ближайшее время с вами свяжутся представители нашей компании.');
                            // send mail to logist
                            $email = new TEmail;
                            $email->from_email = Yii::app()->params['adminEmail'];
                            $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
                            $email->to_email   = Yii::app()->params['supportEmail'];
                            $email->to_name    = '';
                            $email->subject    = 'Восстановление доступа';
                            $email->type = 'text/html';
                            $email->body = '<div>'.
                                    '<p>Перевозчик "'. $user->company. '" ИНН/УНП = '. $user->inn .' запросил восстановление доступов, однако в базе не указан email, просьба связаться с пользователем и узнать email, указать его в бирже перевозок и 1С. Далее попросить пользователя еще раз воспользоваться услугой восстановления доступа.</p>'.
                                    '<p>Контактный телефон: '. $user->phone . '</p>'.
                                '</div>
                                <hr/><h5>Это уведомление является автоматическим, на него не следует отвечать.</h5>
                            ';
                            $email->sendMail();
                        }
                    } else {
                        if(User::USER_NOT_CONFIRMED == $user->status) $message = 'не подтверждена';
                        else if(User::USER_TEMPORARY_BLOCKED == $user->status) $message = 'временно заблокирована';
                        else if(User::USER_BLOCKED == $user->status) $message = 'заблокирована';
                        Dialog::message('flash-error', 'Внимание!', ' Восстановление доступа невозможно. Ваша учетная запись ' . $message.'.');
                    }
                } else {
                    Dialog::message('flash-error', 'Внимание!', 'Пользователя с таким ИНН/УНП и Email не найдено, свяжитесь с отделом логистики.');
                }
            } else Dialog::message('flash-error', 'Внимание!', 'Заполнены не все обязательные поля');
            $this->redirect('/');
            
        } else {
            $this->render('restore', array('model' => $model));
        }
    }
     
    public function sendMail($to, $typeMessage, $post)
    {
        $email = new TEmail;
        $email->from_email = 'cheshenkov@lbr.ru'; //Yii::app()->params['adminEmail'];
        $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
        $email->to_email   = 'tttanyattt@mail.ru';    //'support.ex@lbr.ru';//$to;
        $email->to_name    = '';
        $email->subject    = 'Заявка на регистрацию';
        $email->type       = 'text/html';
        
        if(!empty($typeMessage)) {
            $description = (!empty($post['description'])) ? '<p>Примечание:<b>'.$post['description'].'</b></p>' : '' ;
            $email->body = '
              <div>
                  <p>Компания "'. $post['ownership'] . ' '.$post['company'].'" подала заявку на регистрацию в бирже перевозок ЛБР АгроМаркет.</p>
                  <p>Контактное лицо: <b>'.$post['name']. ' ' .$post['surname'].'</b></p>
                  <p>Телефон: <b>'.$post['phone'].'</b></p>
                  <p>Email: <b>'.$post['email'].'</b></p>'.
                   $description .
              '</div>
              <hr/><h5>Это уведомление является автоматическим, на него не следует отвечать.</h5>
            ';
        } else {
            $email->body = '
                <div> 
                    <p>Ваша регистрация будет рассмотрена и Вам будет выслано подтверждение на почтовый ящик. </p>
                </div>
                <hr/><h5>Это уведомление является автоматическим, на него не следует отвечать.</h5>
            ';
        }
        $email->sendMail();
    }
     
    public function actionQuick() 
    { 
        $model = new QuickForm;
        $model->attributes = $_POST['QuickForm'];
        if($model->validate()) {
            $user = User::model()->findByPk($model->user);
            $email = new TEmail;
            $email->from_email = $user->email;
            $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
            $email->to_email   = Yii::app()->params['supportEmail'];
            $email->to_name    = 'Модератору';
            $email->subject    = '';
            $email->type = 'text/html';
            
            $email->body = "<div>
                    <p>
                      Пользоваетель " . $user->company." (" . $user->email . ") 
                      находясь в перевозке с id = ".$model->transport." обратился к модератору Биржи перевозок ЛБР 'АгроМаркет'
                      со следующим обращением:
                    </p>
                    <p>" . $model->message . "</p>
                </div>
            ";
            $email->sendMail();
        }
        
        Dialog::message('flash-success', 'Отправлено!', 'Спасибо, '.$user->name.'! Ваше письмо отправлено!');
        $this->redirect(array('transport/description/id/1'));
    }
    
    public function actionError()
    {
        if($error=Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }
}
