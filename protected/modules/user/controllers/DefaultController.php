<?php

class DefaultController extends Controller
{
    public function actionIndex()
    {
        $this->render('index');
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

    
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }
    
    /* User options and change password */
    public function actionOption()
    {
        $dataContacts = array();
        $userId = Yii::app()->user->_id;
        $user = User::model()->findByPk($userId);
        $curEmail = $user->email;
        $pass = new PasswordForm;
        
        $mail = new MailForm;
        //$mail->new_email = null;
        
        $model = UserField::model()->find('user_id = :id', array('id' => $userId));
        
        if(isset($_POST['UserField'])) {
            $model->attributes = $_POST['UserField'];
            $model->show = $_POST['UserField']['show'];
            if($_POST['UserField']['show'] == 'all') {
                $model->show_intl = true;
                $model->show_regl = true;
            } else if($_POST['UserField']['show'] == 'regl'){
                $model->show_regl = true;
                $model->show_intl = false;
            } else {
                $model->show_regl = false;
                $model->show_intl = true;
            }
            $model->save();
        } else {
            if((int)$model->show_intl && (int)$model->show_regl) $model->show = 'all';
            else if((int)$model->show_intl) $model->show = 'intl';
            else $model->show = 'regl';
        }

        if(isset($_POST['PasswordForm'])) {
            $user = User::model()->findByPk($userId);
            $pass->attributes = $_POST['PasswordForm'];
            if ($pass->validate()) {
                $user->password = crypt($pass->new_password, User::model()->blowfishSalt());
                if($user->save(false)) {
                    Yii::app()->user->setFlash('success', 'Ваш пароль изменен.');
                    $this->sendMail($user->email, $pass->new_password);
                    
                    $this->redirect('/user/default/option');
                    Yii::app()->end();
                } else Yii::app()->user->setFlash('error', 'Ошибка при сохранении.');
            }
        }
        
        if(isset($_POST['MailForm'])) {            
            $mail->attributes = $_POST['MailForm'];
            if($mail->validate()) {
                $user->email = trim($_POST['MailForm']['new_email']);
                if($user->save()) {
                    $mail->new_email = null;
                    $curEmail = $user->email;
                    Yii::app()->user->setFlash('success', 'Ваш email изменен.');
                    
                    $this->redirect('/user/default/option');
                    Yii::app()->end();
                } else Yii::app()->user->setFlash('error', 'Ошибка при сохранении.');
            }
        }
        
        $criteriaContacts = new CDbCriteria();
        $criteriaContacts->compare('parent', $userId);
        $criteriaContacts->compare('type_contact', 1);
        
        $sort = new CSort();
        $sort->sortVar = 'sort';
        $sort->defaultOrder = 'surname ASC';

        $sort->attributes = array (
            'surname' => array (
                'asc' => 'surname ASC',
                'desc' => 'surname DESC',
                'default' => 'asc',
            ),
        );

        $dataContacts = new CActiveDataProvider('User',
            array(
                'criteria' => $criteriaContacts,
                'sort' => $sort,
                'pagination' => array(
                    'pageSize' => '10'
                )
            )
        );
        
        $this->render('option', array(
            'model' => $model, 
            'pass' => $pass, 
            'curEmail' => $curEmail, 
            'mail' => $mail, 
            'dataContacts' => $dataContacts
        ), false, true);
    }
    
    /* Show all events */
    public function actionEvent()
    {
        $criteria = new CDbCriteria();
        $criteria->with = array('transport' => array('select'=>'*'));
        $criteria->addCondition('transport.id = t.transport_id');
        $criteria->addCondition('t.user_id = ' . Yii::app()->user->_id);
        $criteria->order = 't.id DESC';
        
        $dataProvider = new CActiveDataProvider('UserEvent',
            array(
                'criteria' => $criteria,
                'pagination'=>array(
                   'pageSize' => 10,
                   'pageVar' => 'event',
                ),
                'sort'=>array(
                    'defaultOrder'=>array(
                         'status' => CSort::SORT_DESC,
                    ),
                ),
            )
        );
        
        $this->render('event', array('data' => $dataProvider));
    }
    
    public function getEventMessage($eventType)
    {
        $message = array(
            '1' => 'закрыта',
            '2' => 'будет закрыта через ' . Yii::app()->params['minNotify'] . ' минут',
            '3' => 'новая международная перевозка',
            '4'	=> 'новая региональная перевозка',
            '5' => 'Ваша ставка была перебита'		
        );

        return $message[$eventType];
    }
    
    public function actionEditContact($id)
    {
        $user = User::model()->findByPk($id);
        $model = new UserContactForm;
        $model->attributes = $user->attributes;
        $model->id = $id;
        if(isset($_POST['UserContactForm'])) {
            $user->attributes = $_POST['UserContactForm'];
            if($user->save()) {
                $this->redirect(array('/user/default/editcontact', 'id'=>$user->id));
            } else {
                Yii::log($user->getErrors(), 'error');
            }
        }
        $this->render('editcontact', array('model'=>$model));
    }
    
    public function actionCreateContact()
    {
        $model = new UserContactForm;
        $model->status = 1;
        if(isset($_POST['UserContactForm'])) {
            $model->attributes = $_POST['UserContactForm'];
            if($model->validate()) {
                $password = User::randomPassword();
                $curUser = User::model()->findByPk(Yii::app()->user->_id);
                $user = new User;
                $user->attributes = $_POST['UserContactForm'];
                $user->type_contact = 1;
                $user->status = 1;
                $user->parent = Yii::app()->user->_id;
                $user->password = crypt($password, User::model()->blowfishSalt());
                $name = $user->name;
                if(!empty($user->surname)) $name .= ' '.$user->surname;
                $user->company = 'Контактное лицо "' . $curUser->company . '" ('.$name.')';
                
                if($user->save()) {
                    $newFerrymanFields = new UserField;
                    $newFerrymanFields->user_id = $user->id;
                    $newFerrymanFields->mail_transport_create_1 = false;
                    $newFerrymanFields->mail_transport_create_2 = false;
                    $newFerrymanFields->mail_kill_rate = false;
                    $newFerrymanFields->mail_before_deadline = false;
                    $newFerrymanFields->mail_deadline = true;
                    $newFerrymanFields->with_nds = false; 
                    $newFerrymanFields->show_regl = true;
                    $newFerrymanFields->show_intl = true;
                    $newFerrymanFields->save();
                
                    $this->sendMailToNewContact($user, $curUser, $password);
                    $this->redirect(array('/user/default/editcontact', 'id'=>$user->id));
                } else {
                    Yii::log($user->getErrors(), 'error');
                }
            } 
//            else {
//                $model->attributes = $_POST['UserContactForm'];
//                Yii::app()->user->setFlash('error', 'Указанный email уже используется. ');
//            }
        }
        
        $this->render('editcontact', array('model'=>$model));
    }
    
    public function actionDeleteContact($id)
    {
        $model = User::model()->findByPk($id);
        $contactName = $model->surname . ' ' . $model->name;
        if(User::model()->deleteByPk($id)){
            Yii::app()->user->setFlash('message', 'Контактное лицо "' . $contactName . '" удалено успешно.');
            $this->redirect('/user/option/');
        }
    }
    
    public function actionUpdateEventCounter()
    {
        $sql = 'select count(*) from user_event where status = 1 and user_id = ' . Yii::app()->user->_id;
        $activeEvents = Yii::app()->db->createCommand($sql)->queryScalar();
        if($activeEvents == 0) $activeEvents = '';		
        echo $activeEvents;
    }
    
    public function sendMail($email, $password)
    {
        $email = new TEmail2;
        $email->from_email = Yii::app()->params['adminEmail'];
        $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
        $email->to_email   = $email;
        $email->to_name    = '';
        $email->subject    = 'Восстановление доступа';
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
                                            <img src="http://exchange.lbr.ru/images/test/content_top789.jpg" alt="" border="0" width="620" height="12" style="float: left"/>
                                        </td>
                                    </tr>
                                </table>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="20"></td>
                                        <td>
                                            <img src="http://exchange.lbr.ru/images/test/empty.gif" width="1" height="15" style="height:15px; float: left" alt="" />
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td>
                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                                            <tr>
                                                                <td class="img" style="font-size:0pt; line-height:0pt; text-align:left; " valign="top" width="185">
                                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                        <tr>
                                                                            <td>
                                                                                <img src="http://exchange.lbr.ru/images/test/empty.gif" width="1" height="25" style="height:25px; float: left" alt="" />
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <a href="http://exchange.lbr.ru/" target="_blank">
                                                                                    <img src="http://exchange.lbr.ru/images/logo.png" alt="" border="0" width="179" height="66" style="float: left"/>
                                                                                </a>
                                                                            </td>
                                                                            <td>
                                                                                <img src="http://exchange.lbr.ru/images/test/empty.gif" width="20" height="1" style="width:20px" alt="" style="float: left"/>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                                <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" valign="top" width="20"><img src="http://exchange.lbr.ru/images/test/img_right_shadow.jpg" alt="" border="0" width="8" height="131" style="float: left"/></td>
                                                                <td class="text" style="margin: 0; color:#a1a1a1; font-family:Verdana; font-size:12px; line-height:18px; text-align:left" valign="top">
                                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                                                        <tr>
                                                                            <td style="color:#666666; font-family:Verdana; font-size:20px; line-height:24px; text-align:left; font-weight:normal">
                                                                                Смена пароля
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <img src="http://exchange.lbr.ru/images/test/empty.gif" width="1" height="10" style="height:10px; float: left" alt="" />
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="width: 100%; padding-top: 10px; padding-bottom: 10px; color:#666666; font-family:Verdana; font-size:12px; line-height:18px; text-align:left; font-weight:normal">
                                                                                Ваш пароль на портале "Онлайн биржа перевозок ЛБР-АгроМаркет" был изменен:
                                                                                <br/><br/>
                                                                                <span style="color:#000000; font-weight: bold">
                                                                                    Новый пароль: '.$password.'
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td>
                                                                    <img src="http://exchange.lbr.ru/images/test/separator.jpg" alt="" border="0" width="581" height="1" style="border: 0; float: left"/>
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
                                <img src="http://exchange.lbr.ru/images/test/content_bottom.jpg" alt="" border="0" width="620" height="20" style="float: left"/>
                                <!-- END Main Content -->
                            </td>
                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#c1c1c1"></td>
                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="1" bgcolor="#dfdfdf"></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- END Content -->
        ';
        $email->sendMail();
    }
    
    public function sendMailToNewContact($user, $curUser, $password)
    {
        $name = '';
        if(!empty($user->name)) $name = $user->name;
        if(!empty($user->secondname)){
            if(!empty($name)) $name .= ' ';
            $name .= $user->secondname;
        }
        if(!empty($name)) $name .= ',';
        
        $email = new TEmail2;
        $email->from_email = Yii::app()->params['adminEmail'];
        $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
        $email->to_email   = $user->email;
        $email->to_name    = '';
        $email->subject    = "Приглашение";
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
                                                <img src="http://exchange.lbr.ru/images/test/content_top789.jpg" alt="" border="0" width="620" height="12" style="float: left"/>
                                            </td>
                                        </tr>
                                    </table>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" width="20"></td>
                                            <td>
                                                <img src="http://exchange.lbr.ru/images/test/empty.gif" width="1" height="15" style="height:15px; float: left" alt="" />
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td>
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                                                <tr>
                                                                    <td class="img" style="font-size:0pt; line-height:0pt; text-align:left; " valign="top" width="185">
                                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                            <tr>
                                                                                <td>
                                                                                    <img src="http://exchange.lbr.ru/images/test/empty.gif" width="1" height="25" style="height:25px; float: left" alt="" />
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <a href="http://exchange.lbr.ru/" target="_blank">
                                                                                        <img src="http://exchange.lbr.ru/images/logo.png" alt="" border="0" width="179" height="66" style="float: left"/>
                                                                                    </a>
                                                                                </td>
                                                                                <td>
                                                                                    <img src="http://exchange.lbr.ru/images/test/empty.gif" width="20" height="1" style="width:20px" alt="" style="float: left"/>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                    <td class="img" style="font-size:0pt; line-height:0pt; text-align:left" valign="top" width="20"><img src="http://exchange.lbr.ru/images/test/img_right_shadow.jpg" alt="" border="0" width="8" height="131" style="float: left"/></td>
                                                                    <td class="text" style="margin: 0; color:#666666; font-family:Verdana; font-size:12px; line-height:18px; text-align:left" valign="top">
                                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                                                            <tr>
                                                                                <td style="color:#000000; font-family:Verdana; font-size:20px; line-height:24px; text-align:left; font-weight:normal">
                                                                                    '.$name.'
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <img src="http://exchange.lbr.ru/images/test/empty.gif" width="1" height="5" style="height:5px; float: left" alt="" />
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="width: 100%; padding-top: 10px; padding-bottom: 10px; color:#666666; font-family:Verdana; font-size:12px; line-height:18px; text-align:left; font-weight:normal">
                                                                                    Вы были зарегистрированы на бирже перевозок ЛБР-Агромаркет как контактное лицо '.$curUser->company.'.
                                                                                    <br /><br />
                                                                                    Логин: '.$user->email.'
                                                                                    <br />
                                                                                    Пароль: '.$password.'
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td>
                                                                        <img src="http://exchange.lbr.ru/images/test/separator.jpg" alt="" border="0" width="581" height="1" style="border: 0; float: left"/>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td class="text" style="color:#666666; font-family:Verdana; font-size:12px; line-height:14px; text-align:left; padding-top: 10px; padding-bottom: 5px" valign="top">
                                                            Изменить текущий пароль Вы можете в кабинете пользователя в меню "Настройки".
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text" style="color:#666666; font-family:Verdana; font-size:10px; line-height:10px; text-align:left; padding-top: 10px; padding-bottom: 5px" valign="top">
                                                            Это сообщение является автоматическим, на него не следует отвечать.
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td class="img" style="font-size:0pt; line-height:0pt; text-align:left; float: left" width="20"></td>
                                        </tr>
                                    </table>
                                    <img src="http://exchange.lbr.ru/images/test/content_bottom.jpg" alt="" border="0" width="620" height="20" style="float: left"/>
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
    
    public function actionDeleteOldEvents()
    {
        $closedTransports = Transport::model()->findAll('status=:closed OR status=:test', array(':closed'=>0, ':test'=>2));
        foreach ($closedTransports as $transport) {
            UserEvent::model()->deleteAll('transport_id = '.$transport->id);
        }
    }
}