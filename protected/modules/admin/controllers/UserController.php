<?php

class UserController extends Controller 
{
    protected function beforeAction($action) 
    {
        if (parent::beforeAction($action)) {
            // Добавление CSS файла для пользователей.
        }
        return true;
    }

    public function actionIndex($status = 5) 
    {
        if(Yii::app()->user->checkAccess('trReadUser'))
        {
            $criteria = new CDbCriteria();
            $criteria->condition = 'type_contact = 0';
            
            if($status != 5) {
                $criteria->condition = 't.status = :status';
                $criteria->params = array(':status' => $status);
            }
            
            $sort = new CSort();
            $sort->sortVar = 'sort';
            // сортировка по умолчанию 
            $sort->defaultOrder = 'company ASC';
            $dataProvider = new CActiveDataProvider('User', 
                array(
                    'criteria'=>$criteria,
                    'sort'=>$sort,
                    'pagination'=>array(
                        'pageSize'=>'10'
                    )
                )
            );
            if ($id_item = Yii::app()->user->getFlash('saved_id')){
                $model = User::model()->findByPk($id_item);
                $form  = new UserForm;
                $form->attributes = $model->attributes;
                
                $form->country = $model->country;
                $form->company = $model->company;
                $form->country = $model->country;
                $form->password = $model->password;
                $form->region = $model->region;
                $form->district = $model->district;
                $form->inn = $model->inn;
                $form->name = $model->name;
                $form->surname = $model->surname;
                $form->phone = $model->phone;
                $form->email = $model->email;
                $form->status = $model->status;
                
                $form->id = $id_item;
                $view = $this->renderPartial('user/edituser', array('model'=>$form), true, true);
            }
            $this->render('user/user', array('data'=>$dataProvider, 'view'=>$view));
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionCreateUser()
    {
        if(Yii::app()->user->checkAccess('trCreateUser')) {
            $form = new UserForm;
            $emailExists = array();
            if(isset($_POST['UserForm'])) {
                if(!empty($_POST['UserForm']['email'])) {
                    $emailExists = User::model()->find(array(
                        'select'    => 'email',
                        'condition' => 'email=:email',
                        'params'    => array(':email'=>$_POST['UserForm']['email']))
                    );
                }
                
                $innExists = User::model()->find(array(
                    'select'    => 'inn',
                    'condition' => 'inn=:inn',
                    'params'    => array(':inn'=>$_POST['UserForm']['inn']))
                );
                
                if(empty($emailExists) && empty($innExists)) {
                    $model = new User;
                    $model->attributes = $_POST['UserForm'];
                    $model->password = crypt($_POST['UserForm']['password'], User::model()->blowfishSalt());
                    $model->type_contact = 0;
                    
                    if($model->save()) {
                        $message = 'Создан пользователь ' . $model->name . ' ' . $model->surname;
                        Changes::saveChange($message);

                        $newFerrymanFields = new UserField;
                        $newFerrymanFields->user_id = $model->id;
                        $newFerrymanFields->mail_transport_create_1 = false;
                        $newFerrymanFields->mail_transport_create_2 = false;
                        $newFerrymanFields->mail_kill_rate = false;
                        $newFerrymanFields->mail_before_deadline = false;
                        $newFerrymanFields->mail_deadline = true;
                        $newFerrymanFields->with_nds = false;  
                        $newFerrymanFields->show_intl = true;
                        $newFerrymanFields->show_regl = true;
                        $newFerrymanFields->save();

                        Yii::app()->user->setFlash('saved_id', $model->id);
                        Yii::app()->user->setFlash('message', 'Пользователь "'.$model->company.'" создан успешно.');
                        $form->attributes = $model->attributes;
                        $this->render('user/edituser', array('model'=>$form), false, true);
                    } else Yii::log($model->getErrors(), 'error');
                } else {
                    if(!empty($emailExists) && !empty($innExists)) {
                        Yii::app()->user->setFlash('error', 'Указанные email и inn уже используются. ');
                    } else if(!empty($emailExists)) {
                        Yii::app()->user->setFlash('error', 'Указанный email уже используется. ');
                    } else {
                        Yii::app()->user->setFlash('error', 'Указанный inn уже используется. ');
                    }
                    $criteria = new CDbCriteria();
                    $sort = new CSort();
                    $sort->sortVar = 'sort';
                    $sort->defaultOrder = 'surname ASC';
                    $dataProvider = new CActiveDataProvider('User', 
                        array(
                            'criteria'=>$criteria,
                            'sort'=>$sort,
                            'pagination'=>array(
                                'pageSize'=>'13'
                            )
                        )
                    );
                    $form->attributes = $_POST['UserForm'];
                    $this->render('user/edituser', array('model'=>$form), false, true);
               }
            } else $this->render('user/edituser', array('model'=>$form), false, true);
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionEditUser($id)
    {
        $model = User::model()->findByPk($id);
        $form = new UserForm;
        $form->attributes = $model->attributes;
        $form->id = $id;
        $message = '';
        if (Yii::app()->user->checkAccess('trEditUser')) {
            $contacts = Yii::app()->db->createCommand()
                ->select('name, secondname, surname, email')
                ->from('user')
                ->where('parent = '. $id)
                ->queryAll()
            ;
            
            if (isset($_POST['UserForm'])) {
                $changes = $innExists = $emailExists = array();
                if($_POST['UserForm']['status'] == User::USER_NOT_CONFIRMED || $_POST['UserForm']['status'] == User::USER_ACTIVE || !empty($_POST['UserForm']['reason'])) {
                    if($_POST['UserForm']['status'] == User::USER_NOT_CONFIRMED || $_POST['UserForm']['status'] == User::USER_ACTIVE){
                        $_POST['UserForm']['reason'] = null;
                    }

                    foreach ($_POST['UserForm'] as $key => $value) {
                        if (trim($model[$key]) != trim($value) && $key != 'password' && $key != 'password_confirm') {
                            $changes[$key]['before'] = $model[$key];
                            $changes[$key]['after'] = $value;
                            $model[$key] = trim($value);
                            if($key == 'company'){
                                $allContacts = Yii::app()->db->createCommand()
                                    ->select('id')
                                    ->from('user')
                                    ->where('parent = '. $model->id)
                                    ->queryAll()
                                ;
                                if(!empty($allContacts)){
                                    foreach ($allContacts as $contact) {
                                        $modelContact = User::model()->findByPk($contact['id']);
                                        $contactName = $modelContact->name;
                                        if(!empty($modelContact->surname)) $contactName .= ' '.$modelContact->surname;
                                        $modelContact->company = 'Контактное лицо "' . $model->company . '" ('.$contactName.')';
                                        $modelContact->save();
                                    }
                                }
                            }
                        } else if($key == 'password' && !empty($_POST['UserForm']['password_confirm']) && $model->password !== crypt(trim($_POST['UserForm']['password_confirm']), $model->password)) {
                            $model->password = crypt($_POST['UserForm']['password_confirm'], User::model()->blowfishSalt());
                            $changes[$key] = 'Изменен пароль';
                        }
                    }

                    if (!empty($changes)) {
                        $message = 'У пользователя с id = ' . $id . ' были изменены слудующие поля: ';
                        $k = 0;
                        foreach ($changes as $key => $value) {
                            $k++;
                            if($key == 'password'){
                                $message .= $k . ') ' . $changes[$key];    
                            } else {
                                $message .= $k . ') Поле ' . $key . ' c "' . $changes[$key]['before'] . '" на "' . $changes[$key]['after'] . '"; ';
                            }

                            if($key == 'inn' || $key == 'email') {
                                if($key == 'inn') {
                                    $innExists = User::model()->find(array(
                                        'select'    => 'inn',
                                        'condition' => 'inn=:inn',
                                        'params'    => array(':inn'=>$_POST['UserForm']['inn']))
                                    );

                                    if(!empty($innExists)) Yii::app()->user->setFlash('error', 'Указанный inn уже используется. ');
                                } else {
                                    $emailExists = User::model()->find(array(
                                        'select'    => 'email',
                                        'condition' => 'email=:email',
                                        'params'    => array(':email'=>$_POST['UserForm']['email']))
                                    );

                                    if(!empty($emailExists)) Yii::app()->user->setFlash('error', 'Указанный email уже используется. ');
                                }
                            }
                        }
                    }

                    if(!empty($innExists) || !empty($emailExists)) {
                        $form->attributes = $_POST['UserForm'];
                    } else {
                        if(!empty($message)) {
                            Changes::saveChange($message);
                            if (!empty($_POST['UserForm']['password_confirm'])) {
                                $model->password = crypt($_POST['UserForm']['password_confirm'], User::model()->blowfishSalt());
                            }

                            if ($model->save()) {
                                Yii::app()->user->setFlash('saved_id', $model->id);
                                Yii::app()->user->setFlash('message', 'Пользователь "' . $model->company . '" сохранен успешно.');
                                $form->attributes = $model->attributes;
                                $this->sendAboutChangeStatus($model, $changes);
                            } else Yii::log($model->getErrors(), 'error');
                        }
                    }
                } else {
                    Yii::app()->user->setFlash('error', 'Поле "Причина" не может быть пустым.');
                    $form->attributes = $_POST['UserForm'];
                    //$this->render('user/edituser', array('model'=>$form), false, true);
                }
            } 
            $this->render('user/edituser', array('model' => $form, 'contacts' => $contacts), false, true);
        } else {
            throw new CHttpException(403, Yii::t('yii', 'У Вас недостаточно прав доступа.'));
        }
    }

    public function sendAboutChangeStatus($model, $changes)
    {
        // Send mail about changes in field "Status"
        if(array_key_exists('status', $changes)) {
            $reason = $name = '';
            if(!empty($model->name)) $name = $model->name;
            if(!empty($model->secondname)){
                if(!empty($name)) $name .= ' ';
                $name .= $model->secondname;
            }
            if($model->status != User::USER_NOT_CONFIRMED && $model->status != User::USER_ACTIVE){
                $reason = '<p>Причина: '.$model->reason.'</p>';
            }

            $email = new TEmail;
            $email->from_email = Yii::app()->params['adminEmail'];
            $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
            $email->to_email   = $model->email;
            $email->to_name    = '';
            $email->subject    = "Уведомление об изменении статуса";
            $email->type = 'text/html';
            $email->body = '<h1>'.$name.', </h1>' . 
                '<p>Статус вашей учетной записи был изменен на "'.User::$userStatus[$model->status].'" </p>' .
                $reason .
                '</hr><h5>Это сообщение является автоматическим, на него не следует отвечать</h5>'
            ;
            $email->sendMail();
        }
        /*** ---- Test ---- *****************************************/
        if(array_key_exists('status', $changes)) {
            $reason = $name = '';
            if(!empty($model->name)) $name = $model->name;
            if(!empty($model->secondname)){
                if(!empty($name)) $name .= ' ';
                $name .= $model->secondname;
            }
            if($model->status != User::USER_NOT_CONFIRMED && $model->status != User::USER_ACTIVE){
                $reason = 'Причина: '.$model->reason;
            }

            $email = new TEmail2;
            $email->from_email = Yii::app()->params['adminEmail'];
            $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
            $email->to_email   = 'krilova@lbr.ru';
            $email->to_name    = '';
            $email->subject    = 'Уведомление о смене статуса';
            $email->type = 'text/html';
            $message = '<!-- Content -->
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
                                                                                    '.$name.',
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <img src="http://exchange.lbr.ru/images/mail/empty.gif" width="1" height="5" style="height:5px; float: left" alt="" />
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="width: 100%; padding-top: 10px; padding-bottom: 10px; color:#666666; font-family:Verdana; font-size:12px; line-height:18px; text-align:left; font-weight:normal">'
           ;

           if($model->status == User::USER_ACTIVE) $message .= 'Ваша учетная запись была активирована на бирже перевозок "ЛБР-Агромаркет".';
           else if($model->status == User::USER_WARNING) $message .= 'Вам было вынесено предупреждение.';
           else if($model->status == User::USER_BLOCKED) $message .= 'Ваша учетная запись была заблокирована.';
           else if($model->status == User::USER_TEMPORARY_BLOCKED) $message .= 'Ваша учетная запись была заблокирована до 20/10/2014 года.';
           else $message .= 'Статус вашей учетной записи был изменен на "'.User::statusLabel($model->status).'".';

           if(!empty($reason)) 
               $message .= '<br /><br />
                    <span style="color: #000; ">'.$reason.'</span>'
               ;

           $message .= '</td>
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
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td class="text" style="color:#666666; font-family:Verdana; font-size:10px; line-height:10px; text-align:left; padding-top: 10px; padding-bottom: 5px" valign="top">
                                                            Если Вы считаете, что статус был изменен ошибочно, просим связаться с нашим отделом логистики либо направить email на почтовый ящик support.ex@lbr.ru.
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
            $email->body = $message;
            $email->sendMail();
        }   
    }
    
    public function actionDeleteUser($id, $status = 5)
    {
        $model = User::model()->findByPk($id);
        if (Yii::app()->user->checkAccess('trDeleteUser')) {
            $name = $model->company;
            if (User::model()->deleteByPk($id)) {
                $message = 'Удален пользователь "' . $name . '"';
                Changes::saveChange($message);
                User::model()->deleteAllByAttributes(array('parent'=>$id));
                Yii::app()->user->setFlash('message', 'Пользователь удален успешно.');
                if($status == 5) $this->redirect('/admin/user/');
                else $this->redirect('/admin/user/index/status/'.$status);
            }
        } else {
            throw new CHttpException(403, Yii::t('yii', 'У Вас недостаточно прав доступа.'));
        }
    }
}
