<?php
class SiteController extends Controller
{
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex($s = null)
	{
		$this->forward('user/transport/index');
		//$this->forward('/transport/index');
	}
	
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
		
		$this->render('item', array('rateData' => $dataProvider, 'transportInfo' => $transportInfo));
	}
}