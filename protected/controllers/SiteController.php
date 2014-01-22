<?php
class SiteController extends Controller
{
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
                        'yiichat'=>array('class'=>'YiiChatAction'),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex($s = null)
	{
	    $this->forward('user/transport/index');
	}
	
	/*** ПЕРЕНЕСТИ ********************************************************************/
	public function getPrice($id)
	{
	    $row = Yii::app()->db->createCommand()
		    ->select()
			->from('rate')
			->where('id = :id', array(':id' => $id)) // !!!! заменить
			->queryRow()
		;
		return $row['price'];
	}
	
	public function actionUpdateCounter()
	{
	    
		return 333;
	}
	
	public function actionOption()
	{
	    $model = Yii::app()->db->createCommand()
		    ->select()
			->from('user_field')
			->where('user_id = :id', array(':id' => 2)) // !!!! заменить
			->queryRow()
		;
		
	    $this->render('option', array('model' => $model));
	}
	
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

		$model = UserField::model()->find('user_id = :id', array('id' => 2)); //!!! заменить
		$model->attributes = $data;
		foreach($modelFields as $field){
			$model[$field] = false;
		}
		$model->save();
		$this->render('option', array('model' => $model));
	}
        
	public function actionDescription($id)
	{
		Yii::import('application.extensions.chat.classes.*');
		$transportInfo=Yii::app()->db->createCommand("SELECT * from transport where id='".$id."'")->queryRow();
		//var_dump($transportInfo);

		$allRatesForTransport = Yii::app()->db->createCommand()
			->select('r.date, r.price, u.name')
			->from('rate r')
			->join('user u', 'r.user_id=u.id')
			->where('r.transport_id=:id', array(':id'=>$id))
			->order('r.date desc')
			->queryAll()
		;
		
		$this->render('chat', array('rateData' => $dataProvider, 'transportInfo' => $transportInfo));
	}
	
	public function actionActive()
	{
	    $transportId = '';
	    $temp = Yii::app()->db->createCommand()
			->selectDistinct('transport_id')
			->from('rate')
			->where('user_id = :id', array(':id' => 3)) ///!!!! заменить
			->queryAll()
		;
		
		foreach($temp as $t){
			$transportId[] = $t['transport_id'];
		}
		
		$criteria = new CDbCriteria();
		$criteria->addInCondition('id', $transportId);
		$criteria->compare('status', 1);
		
		$dataProvider = new CActiveDataProvider('Transport',
			array(
				'criteria' => $criteria,
				'pagination'=>array(
				   'pageSize' => 2,
				   'pageVar' => 'page',
				),
				
				//Настройки для сортировки
				'sort' => array(
					//атрибуты по которым происходит сортировка
					'attributes'=>array(
						'date_published'=>array(
							'asc'=>'date_published ASC',
							'desc'=>'date_published DESC',
							'default'=>'desc',
						)
					),
					'defaultOrder'=>array(
						'date_published' => CSort::SORT_DESC,
					),                        
				),
			)
		);
			
		$this->render('active', array('data' => $dataProvider));
	}
	
	// if set $s parameter - user win 
	// else - user lost
	public function actionArchive($s = null)
	{
	    $userId = 3; ///!!!! заменить
	    $transportId = $rateId = array();
	    $temp = Yii::app()->db->createCommand()
			->selectDistinct('transport_id')
			->from('rate')
			->where('user_id = :id', array(':id' => $userId)) 
			->queryAll()
		;
		
		foreach($temp as $t){
			$transportId[] = $t['transport_id'];
		}
		// all rates where user took part
		$temp = Yii::app()->db->createCommand()
			->select('id')
			->from('rate')
			->where('user_id = :id', array(':id' => $userId))
			->queryAll()
		;
		
		foreach($temp as $t){
			$rateId[] = $t['id'];
		}
		
		// all win rates
		$criteria = new CDbCriteria();
		if(isset($s)) {
		    $criteria->addInCondition('rate_id', $rateId);
		} else {
		    $criteria->addNotInCondition('rate_id', $rateId);
		}
		$criteria->compare('status', 0);
		
		$dataProvider = new CActiveDataProvider('Transport',
			array(
				'criteria' => $criteria,
				'pagination'=>array(
				   'pageSize' => 2,
				   'pageVar' => 'page',
				),

				'sort' => array(
					'attributes'=>array(
						'date_published'=>array(
							'asc'=>'date_published ASC',
							'desc'=>'date_published DESC',
							'default'=>'desc',
						)
					),
					'defaultOrder'=>array(
						'date_published' => CSort::SORT_DESC,
					),                        
				),
			)
		);
			
		$this->render('active', array('data' => $dataProvider));
	}

	public function actionEvent()
	{
	    $newEvents = $oldEvents = array();
	    $events = Yii::app()->db->createCommand()
		    ->select('u.*, t.location_from, t.location_to')
			->from('user_event u, transport t')
			->where('u.user_id = :id and t.id = u.transport_id', array(':id' => 3)) // !!!! заменить
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
	}
	
	public function actionTransport()
	{
        //$items = 
		
		$model = Yii::app()->db->createCommand()
		    ->select()
			->from('transport')
			->queryAll()
		;

		$this->render('transport', array('model' => $model));
	}
	
	public function addFormat($date)
	{
	    if((int)$date < 10) $date = '0' . $date;
		return $date;
	}
	
	public function getEventMessage($eventType)
	{
	    $message = array(
		    '1' => 'закрыта',
            '2' => 'будет закрыта через ' . Yii::app()->params['interval'] . ' минут',
            '3' => 'новая международная перевозка',
            '4'	=> 'новая местная перевозка',
            '5' => 'ваша ставка была перебита'		
		);
		
		return $message[$eventType];
	}
	
	/******************************************************************/
	
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

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

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	 
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}