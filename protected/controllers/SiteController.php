<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
    
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
	public function actionIndex()
	{
            // renders the view file 'protected/views/site/index.php'
            // using the default layout 'protected/views/layouts/main.php'
            
            //echo __METHOD__;
            //$this->render('index');
            /************************************************************/
            $criteria = new CDbCriteria();
            //$criteria->together = true; // relations
            //$criteria->with = array('newsRegions');
            //$criteria->compare('published', 1);
            //$criteria->order = 'date_published DESC';

            //$count = Transport::model()->count($criteria);
            
            $dataProvider = new CActiveDataProvider('Transport',
                array(
                    'criteria' => $criteria,
                    'pagination'=>array(
                       'pageSize' => 2,
                       'pageVar' => 'page',
                    ),
                    
                    //Настройки для сортировки
                    'sort'=>array(
                        //атрибуты по которым происходит сортировка
                        'attributes'=>array(
                            'status'=>array(
                                'asc'=>'status ASC',
                                'desc'=>'status DESC',
                                //по умолчанию, сортируем поле rating по убыванию (desc)
                                'default'=>'desc',
                            ),
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
            
            $this->render('view', array('data' => $dataProvider));
            
            
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
		
		//var_dump($allRatesForTransport);
		//var_dump($transportInfo);
		//echo '=============='; exit;
		//$this->render('chat', array('rateData' => $dataProvider, 'transportData' => $transportInfo));
		$this->render('chat2', array('rateData' => $dataProvider, 'transportInfo' => $transportInfo));
	}
	
	public function actionOfficeUser()
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
			
		$this->render('office', array('data' => $dataProvider));
	}
	
	public function actionOfficeUserOption()
	{
	    
	  //  $this->render('options'); //, array('data' => $dataProvider));
	}

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