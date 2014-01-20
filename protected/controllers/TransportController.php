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
}