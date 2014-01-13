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
	public function actionIndex($s = null)
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
			
            if(isset($s)) {
                $this->render('view_full', array('data' => $dataProvider));
			} else {
			    $this->render('view', array('data' => $dataProvider));
			}
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
	
	public function actionSave()
	{
	    $allModelFields = array('mail_transport_create_1', 'mail_transport_create_2', 'mail_kill_rate', 'mail_deadline', 'mail_before_deadline');
	    $data = $_POST;
		$modelFields = array();
		foreach($allModelFields as $field){
		    if(!array_key_exists($field, $data)){
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
		
		//var_dump($allRatesForTransport);
		//var_dump($transportInfo);
		//echo '=============='; exit;
		//$this->render('chat', array('rateData' => $dataProvider, 'transportData' => $transportInfo));
		$this->render('chat2', array('rateData' => $dataProvider, 'transportInfo' => $transportInfo));
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
		$criteria->addCondition('id', $transportId);
		$criteria->addInCondition('id', $transportId);
		$criteria->compare('status', 0);
		
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
	
	public function realDateDiff($date1, $date2 = NULL){
		$diff = array();
		//Если вторая дата не задана принимаем ее как текущую
		if(!$date2) {
			$cd = getdate();
			$date2 = $cd['year'].'-'.$cd['mon'].'-'.$cd['mday'].' '.$cd['hours'].':'.$cd['minutes'].':'.$cd['seconds'];
		}
		 
		//Преобразуем даты в массив
		$pattern = '/(\d+)-(\d+)-(\d+)(\s+(\d+):(\d+):(\d+))?/';
		preg_match($pattern, $date1, $matches);
		$d1 = array((int)$matches[1], (int)$matches[2], (int)$matches[3], (int)$matches[5], (int)$matches[6], (int)$matches[7]);
		preg_match($pattern, $date2, $matches);
		$d2 = array((int)$matches[1], (int)$matches[2], (int)$matches[3], (int)$matches[5], (int)$matches[6], (int)$matches[7]);
	 
		//Если вторая дата меньше чем первая, меняем их местами
		for($i=0; $i<count($d2); $i++) {
			if($d2[$i]>$d1[$i]) break;
			if($d2[$i]<$d1[$i]) {
				$t = $d1;
				$d1 = $d2;
				$d2 = $t;
				break;
			}
		}
	 
		//Вычисляем разность между датами (как в столбик)
		$md1 = array(31, $d1[0]%4||(!($d1[0]%100)&&$d1[0]%400)?28:29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$md2 = array(31, $d2[0]%4||(!($d2[0]%100)&&$d2[0]%400)?28:29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$min_v = array(NULL, 1, 1, 0, 0, 0);
		$max_v = array(NULL, 12, $d2[1]==1?$md2[11]:$md2[$d2[1]-2], 23, 59, 59);
		for($i=5; $i>=0; $i--) {
			if($d2[$i]<$min_v[$i]) {
				$d2[$i-1]--;
				$d2[$i]=$max_v[$i];
			}
			
			$diff[$i] = $d2[$i]-$d1[$i];
			if($diff[$i]<0) {
				$d2[$i-1]--;
				$i==2 ? $diff[$i] += $md1[$d1[1]-1] : $diff[$i] += $max_v[$i]-$min_v[$i]+1;
			}
		}
		//Возвращаем результат
		return $diff;
	}
	
	public function addFormat($date)
	{
	    if((int)$date < 10) $date = '0' . $date;
		return $date;
	}
}