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
    
    public function actionRegistration()
    {
        $model = new RegistrationForm;
        if(isset($_POST['RegistrationForm']))
        {
            $email = new TEmail;
            $email->from_email = Yii::app()->params['adminEmail'];
            $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
            $email->to_email   = 'tttanyattt@mail.ru'; // !!! заменить
            $email->to_name    = '';
            $email->subject    = 'Заявка на регистрацию';
            $email->type = 'text/html';
            $description = (!empty($_POST['RegistrationForm']['description'])) ? '<p>Примечание:<b>'.$_POST['RegistrationForm']['description'].'</b></p>' : '' ;
            $email->body = '
              <div>
                  <p>Компания "'.$_POST['RegistrationForm']['firmName'].'" подала заявку на регистрацию в бирже перевозок ЛБР АгроМаркет.</p>
                  <p>Контактное лицо: <b>'.$_POST['RegistrationForm']['name'].'</b></p>
                  <p>Телефон: <b>'.$_POST['RegistrationForm']['phone'].'</b></p>
                  <p>Email: <b>'.$_POST['RegistrationForm']['email'].'</b></p>'.
                   $description .
              '</div>
              <hr/><h5>Это автоматическое уведомление, на него не следует отвечать.</h5>
            ';
            $email->sendMail();

            Yii::app()->user->setFlash('message', 'Ваша заявка отправлена. Спасибо за интерес, проявленный к нашей компании.');
            $this->redirect('/user/login/');
        } else {
            $this->render('registration', array('model' => $model));
        }
    }

    /* Show user options */
    public function actionOption()
    {
        $userId = Yii::app()->user->_id;
        $elementExitsts = UserField::model()->find(array('condition'=>'user_id = :id', 'params'=>array(':id' => $userId)));
        if($elementExitsts) {
            $model = Yii::app()->db->createCommand()
                ->select()
                ->from('user_field')
                ->where('user_id = :id', array(':id' => $userId))
                ->queryRow()
            ;
        } else {
            $model = new UserField;
            $data = array('mail_deadline' => true, 'site_transport_create_1' => true, 'site_transport_create_2' => true, 'site_kill_rate' => true, 'site_deadline' => true, 'site_before_deadline' => true);
            $model->attributes = $data;
            $model->save(); 
        }

        $this->render('option', array('model' => $model));
    }
    /* Save user options */
    public function actionSaveOption()
    {
        $allModelFields = array('mail_transport_create_1', 'mail_transport_create_2', 'mail_kill_rate', 'mail_deadline', 'mail_before_deadline', 'site_transport_create_1', 'site_transport_create_2', 'site_kill_rate', 'site_deadline', 'site_before_deadline');
        $data = $_POST;
        $modelFields = array();
        foreach($allModelFields as $field){
            if(!array_key_exists($field, $data)) {
                $modelFields[] = $field;
            }
        }

        $model = UserField::model()->find('user_id = :id', array('id' => Yii::app()->user->_id));
        $model->attributes = $data;
        foreach($modelFields as $field){
            $model[$field] = false;
        }
        $model->save();
        $this->render('option', array('model' => $model));
    }

    /* Show all events */
    public function actionEvent()
    {
        //$newEvents = $oldEvents = array();
        /*$events = Yii::app()->db->createCommand()
            ->select('u.*, t.location_from, t.location_to')
            ->from('user_event u, transport t')
            ->where('u.user_id = :id and t.id = u.transport_id', array(':id' => Yii::app()->user->_id))
            ->order('id desc')
            ->queryAll()
        ;

        foreach($events as $event){
            if($event['status']){
                $newEvents[] = $event;
            } else {
                $oldEvents[] = $event;
            }
        }

        if(!empty($newEvents)){
            UserEvent::model()->updateAll(array('status' => 0), 'status = 1');
        }

        $this->render('event', array('newEvents' => $newEvents, 'oldEvents' => $oldEvents));
        */
        
        
        $criteria = new CDbCriteria();
        $criteria->with = array('transport' => array('select'=>'*'));
        $criteria->addCondition('transport.id = t.transport_id');
        $criteria->addCondition('t.user_id = ' . Yii::app()->user->_id);
        $criteria->order = 't.id DESC';

        $dataProvider = new CActiveDataProvider('UserEvent',
            array(
                'criteria' => $criteria,
                'pagination'=>array(
                   'pageSize' => 8,
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