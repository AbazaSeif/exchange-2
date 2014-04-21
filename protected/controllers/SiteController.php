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
    /*
    public function actionTestMail()
    {
        $model = new Test;
        if (isset($_POST['Test'])) {
            $email = new TEmail;
            $email->from_email = Yii::app()->params['adminEmail'];
            $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
            $email->to_email   = $_POST['Test']['email'];
            $email->to_name    = '';
            $email->subject    = 'Test';
            $email->type       = 'text/html';
            $email->body = '<div>test</div>
              <hr/><h5>Это уведомление является автоматическим, на него не следует отвечать.</h5>
            ';
            $email->sendMail();
            $model->email = 'Письмо отправлено';
            //Yii::log('site call - ' . 'sendmail_path = ' . ini_get('sendmail_path'), 'warning');
        }
        $this->render('test', array('model' => $model));
    }*/
    public function actionTestMail2()
    {
        $email = new TEmail2;
        $email->from_email = Yii::app()->params['adminEmail'];
        $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
        $email->to_email   = 'krilova@lbr.ru';
        $email->to_name    = '';
        $email->subject    = 'Test';
        $email->type       = 'text/html';
        $email->body = 
                '<!-- Content -->
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
                                                                                    Иван Иванович,
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="1" height="5" style="height:5px; float: left" alt="" />
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="color:#a1a1a1; font-family:Verdana; font-size:12px; line-height:18px; text-align:left; font-weight:normal">
                                                                                    Спешим уведомить Вас о том, что на бирже появились новые международные заявки на перевозку.
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
                                                <!-- transport list -->
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td class="text" style="color:#a1a1a1; font-family:Verdana; font-size:12px; line-height:18px; text-align:left" valign="top">
                                                            <table>
                                                                <tr style="color:#000000; font-family:Verdana; font-size:18px; line-height:20px; text-align:left; font-weight:normal">
                                                                    <td style="padding-top: 15px">
                                                                    Свердловская область, г. Березовский, Режевской тракт,15 км - Амурская обл., г. Благовещенск , с.Садовое
                                                                    </td>
                                                                </tr>
                                                                <tr style="color: #a1a1a1; font-family:Verdana; font-size:12px; line-height:18px; text-align:left; font-weight:normal">
                                                                    <td>
                                                                    Описание перевозки
                                                                    </td>
                                                                </tr>
                                                                <tr style="font-family:Verdana; font-size:12px; line-height:18px; text-align:left; font-weight:normal">
                                                                    <td style="padding-top: 15px; padding-bottom: 10px">
                                                                        <a href="http://exchange.lbr.ru/transport/description/id/29/" class="link-u" style="color:#2b9208; text-decoration:underline" target="_blank"><span class="link-u" style="color:#008672; text-decoration:underline">Посмотреть подробнее</span></a>
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
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td class="text" style="color:#a1a1a1; font-family:Verdana; font-size:12px; line-height:18px; text-align:left" valign="top">
                                                            <table>
                                                                <tr style="color:#000000; font-family:Verdana; font-size:18px; line-height:20px; text-align:left; font-weight:normal">
                                                                    <td style="padding-top: 15px">
                                                                    Свердловская область, г. Березовский, Режевской тракт,15 км - Амурская обл., г. Благовещенск , с.Садовое
                                                                    </td>
                                                                </tr>
                                                                <tr style="color: #a1a1a1; font-family:Verdana; font-size:12px; line-height:18px; text-align:left; font-weight:normal">
                                                                    <td>
                                                                    Описание перевозки
                                                                    </td>
                                                                </tr>
                                                                <tr style="font-family:Verdana; font-size:12px; line-height:18px; text-align:left; font-weight:normal">
                                                                    <td style="padding-top: 15px; padding-bottom: 10px">
                                                                        <a href="http://exchange.lbr.ru/transport/description/id/29/" class="link-u" style="color:#2b9208; text-decoration:underline" target="_blank"><span class="link-u" style="color:#008672; text-decoration:underline">Посмотреть подробнее</span></a>
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
        $email->sendMail();

        echo '111';
    }
    
    public function actionRegistration()
    {
        $model = new RegistrationForm;
        if (isset($_POST['RegistrationForm'])) {
            $model->attributes = $_POST['RegistrationForm'];
            if($model->validate()) {
                $emailExists = array();
                $innExists = User::model()->find(array(
                    'condition'=>'inn=:inn',
                    'params'=>array(':inn'=>$_POST['RegistrationForm']['inn']))
                );
                if(empty($innExists)) {
                    $emailExists = User::model()->find(array(
                        'condition'=>'email=:email',
                        'params'=>array(':email'=>$_POST['RegistrationForm']['email']))
                    );
                }

                if(empty($innExists) && empty($emailExists)) {
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
                            $this->sendMail(Yii::app()->params['logistEmailRegional'], 1, $_POST['RegistrationForm']);
                            $this->sendMail(Yii::app()->params['logistEmailInternational'], 1, $_POST['RegistrationForm']);
                        } else if((int)$_POST['RegistrationForm']['show'] == 1){
                            $newFerrymanFields->show_intl = true;
                            $newFerrymanFields->show_regl = false;
                            $this->sendMail(Yii::app()->params['logistEmailInternational'], 1, $_POST['RegistrationForm']);
                        } else {
                            $newFerrymanFields->show_intl = false;
                            $newFerrymanFields->show_regl = true;
                            $this->sendMail(Yii::app()->params['logistEmailRegional'], 1, $_POST['RegistrationForm']);
                        }
                        
                        $newFerrymanFields->save();
                        $this->sendMail($_POST['email'], 0, $_POST['RegistrationForm']);
                        
                        Dialog::message('flash-success', 'Отправлено!', 'Ваша заявка отправлена. Когда ваша заявка будет рассмотрена Вы получите на почту инструкции по активации. Спасибо за интерес, проявленный к нашей компании');
                    } else Yii::log($user->getErrors(), 'error');
                } else if(!empty($emailExists)) {
                    Dialog::message('flash-success', 'Внимание!', 'Пользователь с таким Email уже зарегистрирован в базе, если у Вас возникли проблемы с авторизацией свяжитесь с нашим отделом логистики. ');
                } else {
                    Dialog::message('flash-success', 'Внимание!', 'Пользователь с таким ИНН/УНП уже зарегистрирован в базе, если у Вас возникли проблемы с авторизацией свяжитесь с нашим отделом логистики. ');
                }
            } else {
                Dialog::message('flash-success', 'Внимание!', 'Ваша заявка отклонена, т.к. заполнены не все обязательные поля.');  
            }
            $this->redirect('/');
        } else {
            $this->render('registration', array('model' => $model));
        }
    }
    
    public function actionRestore()
    { 
        $model = new RestoreForm;
        if(isset($_POST['RestoreForm'])) {
            $model->attributes = $_POST['RestoreForm'];
            if($model->validate()) {
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
            } else {
                Dialog::message('flash-success', 'Внимание!', 'Вы заполнили не все обязательные поля.');  
            }
            $this->redirect('/site/restore/');
        } else {
            $this->render('restore', array('model' => $model));
        }
    }
     
    public function sendMail($to, $typeMessage, $post)
    {
        $email = new TEmail;
        $email->from_email = Yii::app()->params['adminEmail'];
        $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
        $email->to_email   = $to;
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
