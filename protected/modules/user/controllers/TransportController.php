<?php

class TransportController extends Controller
{
	public function actionIndex()
	{
		$lastRates = array();	
		$criteria = new CDbCriteria();
		$dataProvider = new CActiveDataProvider('Transport',
                    array(
                        'criteria' => $criteria,
                        'pagination'=>array(
                           'pageSize' => 2,
                           'pageVar' => 'page',
                        ),
                        'sort'=>array(
                                'attributes'=>array(
                                        'date_from'=>array(
                                                'asc'=>'status ASC',
                                                'desc'=>'status DESC',
                                                'default'=>'desc',
                                        ),
                                        'date_to'=>array(
                                                'asc'=>'status ASC',
                                                'desc'=>'status DESC',
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
		
		$this->render('view', array('data' => $dataProvider, 'title'=>'Все перевозки'));
	}
	
	public function actionAll()
	{
		$transportId = array();
	    $temp = Yii::app()->db->createCommand()
			->selectDistinct('transport_id')
			->from('rate')
			->where('user_id = :id', array(':id' => Yii::app()->user->_id))
			->queryAll()
		;
		
		foreach($temp as $t){
			$transportId[] = $t['transport_id'];
		}
		
		$criteria = new CDbCriteria();
		$criteria->addInCondition('id', $transportId);
		
		$dataProvider = new CActiveDataProvider('Transport',
			array(
				'criteria' => $criteria,
				'pagination'=>array(
				   'pageSize' => 2,
				   'pageVar' => 'page',
				),
				'sort' => array(
					'attributes'=>array(
						'date_from'=>array(
                                                'asc'=>'status ASC',
                                                'desc'=>'status DESC',
                                                'default'=>'desc',
                                                ),
                                                'date_to'=>array(
                                                        'asc'=>'status ASC',
                                                        'desc'=>'status DESC',
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
			
		$this->render('view', array('data' => $dataProvider, 'title'=>'Активные перевозки'));
	}
	
	/* Show all transports where user takes part */
	public function actionActive()
	{
	    $transportId = array();
	    $temp = Yii::app()->db->createCommand()
			->selectDistinct('transport_id')
			->from('rate')
			->where('user_id = :id', array(':id' => Yii::app()->user->_id))
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
				'sort' => array(
					'attributes'=>array(
						'date_from'=>array(
                                                'asc'=>'status ASC',
                                                'desc'=>'status DESC',
                                                'default'=>'desc',
                                                ),
                                                'date_to'=>array(
                                                        'asc'=>'status ASC',
                                                        'desc'=>'status DESC',
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
			
		$this->render('view', array('data' => $dataProvider, 'title'=>'Активные перевозки'));
	}
	
	/* 
	*  Show all transports where user took part 
 	*  parameter $s shows that user won this transport 
	*/
	public function actionArchive($s = null)
	{
	    $userId = Yii::app()->user->_id;
	    $transportId = $rateId = $rateIdWin = array();
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
		$temp = Yii::app()->db->createCommand()
			->select('rate_id')
			->from('transport')
			->where('status = :status', array(':status' => 0))
			->queryAll()
		;
		
		foreach($temp as $t){
			$rateIdWin[] = $t['rate_id'];
		}
		
		$intersectRates = array_intersect($rateId, $rateIdWin);
		// all win rates
		$criteria = new CDbCriteria();
		if(isset($s)) {
		    $criteria->addInCondition('rate_id', $intersectRates);
		} else {
		    $criteria->addInCondition('rate_id', $rateId);
		    $criteria->addNotInCondition('rate_id', $rateIdWin);
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
						'date_from'=>array(
                                                'asc'=>'status ASC',
                                                'desc'=>'status DESC',
                                                'default'=>'desc',
                                                ),
                                                'date_to'=>array(
                                                        'asc'=>'status ASC',
                                                        'desc'=>'status DESC',
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
			
		$this->render('view', array('data' => $dataProvider, 'title'=>'Архивные перевозки'));
	}
	
	/* Ajax update rate for current transport */
	public function actionUpdateRatesPrice()
	{
		$sql = 'select price from rate where transport_id = '.$_POST['id'].' group by transport_id order by date desc limit 1';
		$data = Yii::app()->db->createCommand($sql)->queryScalar();
		//$array = array('price'=>$data, 'all'=>222);
		echo $data;//json_encode($data);
	}
	
	public function actionUpdateRates()
	{
	    $temp = Yii::app()->db->createCommand()
			->select()
			->from('rate')
			->where('transport_id = :id', array(':id' => $_POST['id']))
			->group('transport_id')
			->order('date desc')
			->queryAll()
		;
		//$this->renderPartial('ajaxList', $data, false, true);
	}
	
	public function addFormat($date)
	{
	    if((int)$date < 10) $date = '0' . $date;
		return $date;
	}
	
	/* Get latest price for current transport */
	public function getPrice($id)
	{
	    $row = Yii::app()->db->createCommand()
		    ->select()
			->from('rate')
			->where('id = :id', array(':id' => $id))
			->queryRow()
		;
		return $row['price'];
	}
}