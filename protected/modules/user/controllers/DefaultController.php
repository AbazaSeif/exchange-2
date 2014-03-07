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
    
    /* Show user options */
    public function actionOption()
    {
        $userId = Yii::app()->user->_id;
        $model = UserField::model()->find('user_id = :id', array('id' => $userId));
        $pass = new PasswordForm();
        //$pass->password = '';
        //var_dump($pass);
        //$pass->id = $userId;
        if(isset($_POST['UserField'])) {
            $model->attributes = $_POST['UserField'];
            $model->save();
        }
        /*
        var_dump($_POST['PasswordForm']);
        echo '<br>';
        var_dump($_POST['PasswordForm']['password']);
        echo '<br>';
        var_dump($_POST['password']);
        exit;
        */
        if(isset($_POST['PasswordForm'])) {
            if(isset($_POST['PasswordForm']['password']) && isset($_POST['PasswordForm']['new_password'])){
                //var_dump('"' . $_POST['PasswordForm']['password'] . '"'); exit;
                $user = User::model()->findByPk($userId);
                if ($user->password === crypt(trim($_POST['PasswordForm']['password']), $user->password)){
                    //$user->password = crypt($_POST['new_password'], User::model()->blowfishSalt());
                    //$user->save();
                    Dialog::message('flash-success', 'Внимание!', 'Ваш пароль изменен');
                } else {
                    Dialog::message('flash-success', 'Внимание!', 'Вы ввели неверный пароль');
                }
                
                //$pass = new PasswordForm();
                //$pass->password = '';
                //$pass = new PasswordForm();
                //$this->render('option', array('model' => $model, 'pass' => $pass), false, true);
            }
        }
        $this->render('option', array('model' => $model, 'pass' => $pass), false, true);
    }
    
    /* Save user options */
    public function actionSaveOption()
    {
        //$allModelFields = array('mail_transport_create_1', 'mail_transport_create_2', 'mail_kill_rate', 'mail_deadline', 'mail_before_deadline', 'site_transport_create_1', 'site_transport_create_2', 'site_kill_rate', 'site_deadline', 'site_before_deadline', 'with_nds');
        $allModelFields = array('mail_transport_create_1', 'mail_transport_create_2', 'mail_kill_rate', 'mail_deadline', 'mail_before_deadline', 'with_nds');
        $data = $_POST;
        
        $modelFields = array();
        foreach($allModelFields as $field){
            if(!array_key_exists($field, $data)) {
                $modelFields[] = $field;
            }
        }
        
        $model = UserField::model()->find('user_id = :id', array('id' => Yii::app()->user->_id));
        $model->attributes = $data;
        $model['with_nds'] = true;
        
        foreach($modelFields as $field){
            $model[$field] = false;
        }
       
        $model->save();
        $this->render('option', array('model' => $model));
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