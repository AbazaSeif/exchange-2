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
        $userId = Yii::app()->user->_id;
        $pass = new PasswordForm();
        $mail = new MailForm();
        $dataContacts = array();
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
            if ($user->password === crypt(trim($_POST['PasswordForm']['password']), $user->password)){
                $user->password = crypt($_POST['PasswordForm']['new_password'], User::model()->blowfishSalt());
                if($model->validate() && $user->save()){
                    Dialog::message('flash-success', 'Внимание!', 'Ваш пароль изменен');
                }
            } else {
                Dialog::message('flash-success', 'Внимание!', 'Вы ввели неверный пароль');
            }
        }
        
        if(isset($_POST['MailForm'])) {
            $user = User::model()->findByPk($userId);
            
            if ($user->password === crypt(trim($_POST['MailForm']['password']), $user->password)) {
                $exists = User::model()->find(array(
                    'select'=>'email',
                    'condition'=>'email=:email',
                    'params'=>array(':email'=>$_POST['MailForm']['new_email']))
                );
                
                if(empty($exists)) { 
                    $user->email = trim($_POST['MailForm']['new_email']);
                    if($user->save() && $model->validate()) {
                        Dialog::message('flash-success', 'Внимание!', 'Ваш email изменен');
                    }
                } else {
                    Dialog::message('flash-success', 'Внимание!', 'Такой email уже используется');
                }
            } else {
                Dialog::message('flash-success', 'Внимание!', 'Вы ввели неверный пароль');
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
        
         
        $this->render('option', array('model' => $model, 'pass' => $pass, 'mail' => $mail, 'dataContacts' => $dataContacts), false, true);
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
            '2' => 'будет закрыта через ' . Yii::app()->params['interval'] . ' минут',
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
        $this->render('editcontact', array('model'=>$model), false, true);
    }
    
    public function actionCreateContact()
    {
        $model = new UserContactForm;
        $model->status = 1;
        if(isset($_POST['UserContactForm'])) {
            $emailExists=User::model()->find(array(
                'select'=>'email',
                'condition'=>'email=:email',
                'params'=>array(':email'=>$_POST['UserContactForm']['email']))
            );

            if(empty($emailExists)){
                $emailExists=User::model()->find(array(
                    'select'=>'email',
                    'condition'=>'email=:email',
                    'params'=>array(':email'=>$_POST['UserContactForm']['email']))
                );
            }

            if(empty($emailExists)) {
                $password = User::randomPassword();
                $curUser = User::model()->findByPk(Yii::app()->user->_id);
                $user = new User;
                $user->attributes = $_POST['UserContactForm'];
                $user->type_contact = 1;
                $user->status = 1;
                $user->parent = Yii::app()->user->_id;
                $user->password = crypt($password, User::model()->blowfishSalt());
                $user->company = 'Контактное лицо "' . $curUser->company . '" ('.$user->name.' '.$user->surname.')';
                
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
                
                    $email = new TEmail;
                    $email->from_email = Yii::app()->params['adminEmail'];
                    $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
                    $email->to_email   = $user->email;
                    $email->to_name    = '';
                    $email->subject    = "Приглашение";
                    $email->type = 'text/html';
                    $email->body = '<h1>Уважаемый(ая) ' . $user->name . ' ' . $user->secondname . ', </h1>' . 
                        '<p>Вы были зарегистрированы как контактное лицо "' . $curUser->company . '"</p>' .
                        '<p>Логин: ' . $user->email . '</p>' .
                        '<p>Пароль: ' . $password . '</p>' .
                        '<p>Изменить пароль Вы можете зайдя в кабинет пользователя с помощью указанных логина и пароля. </p>' . 
                        '<hr><h5>Это сообщение является автоматическим, на него не следует отвечать</h5>'
                    ;
                    $email->sendMail();
                    $this->redirect(array('/user/default/editcontact', 'id'=>$user->id));
                } else {
                    Yii::log($user->getErrors(), 'error');
                }
            } else {
                $model->attributes = $_POST['UserContactForm'];
                Yii::app()->user->setFlash('error', 'Указанный email уже используется. ');
            }
        }
        
        $this->render('editcontact', array('model'=>$model), false, true);
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
}