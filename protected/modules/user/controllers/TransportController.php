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
						'status'=>array(
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
		
		$this->render('view', array('data' => $dataProvider));
	}
	
	/* Show information about selected transport */
	public function actionDescription($id)
	{
		$transportInfo=Yii::app()->db->createCommand("SELECT * from transport where id='".$id."'")->queryRow();
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
	
	/* 
	*  Show all transports where user took part 
 	*  parameter $s shows that user won this transport 
	*/
	public function actionArchive($s = null)
	{
	    $userId = Yii::app()->user->_id;
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
			
		$this->render('view', array('data' => $dataProvider));
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