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
        $model = UserField::model()->find('user_id = :id', array('id' => $userId));
        $pass = new PasswordForm();
        if(isset($_POST['UserField'])) {
            $model->attributes = $_POST['UserField'];
            $model->save();
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
        $this->render('option', array('model' => $model, 'pass' => $pass), false, true);
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
    
    /* Show user options */
    public function actionContact()
    {
        $model = array();
        $model = new UserContact;
        if(isset($_POST['UserContact'])) {
            $model->attributes = $_POST['UserContact'];
            if($model->validate() && $model->save()){
                //Yii::app()->user->setFlash('saved_id', $model->id);
                //Yii::app()->user->setFlash('message', 'Контакт '.$model->login.' создан успешно.');
                //$this->redirect('/admin/contact/');
            }
            //print_r($model->getErrors());
        }
        $this->render('contact', array('model' => $model), false, true);
    }

    public function getEventMessage($eventType)
    {
        $message = array(
            '1' => 'закрыта',
            '2' => 'будет закрыта через ' . Yii::app()->params['interval'] . ' минут',
            '3' => 'новая международная перевозка',
            '4'	=> 'новая местная перевозка',
            '5' => 'Ваша ставка была перебита'		
        );

        return $message[$eventType];
    }

    public function actionUpdateEventCounter()
    {
        $sql = 'select count(*) from user_event where status = 1 and user_id = ' . Yii::app()->user->_id;
        $activeEvents = Yii::app()->db->createCommand($sql)->queryScalar();
        if($activeEvents == 0) $activeEvents = '';		
        echo $activeEvents;
    }
}