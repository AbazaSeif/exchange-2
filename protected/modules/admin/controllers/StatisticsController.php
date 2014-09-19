<?php
class StatisticsController extends Controller
{
    public function actionIndex($transportType = 2)
    {
        $model = new StatisticsForm;
        
        $this->render('statistics', array('model'=>$model));
    }
    
    public function actionGetExcel($from, $to, $type)
    {
        $model = Transport::model()->findAll(array('order'=>'date_close desc', 'condition'=>'status=0 and date_close between "'.date('Y-m-d', strtotime($from)).'" and "'.date('Y-m-d', strtotime($to.' +1 days')).'"'));
        
        Yii::app()->request->sendFile('Статистика биржи перевозок на '.date('Y-m-d H-i').'.xls', 
            $this->renderPartial('excel', array(
                'model'=>$model,
                'dateFrom' => $from,
                'dateTo' => $to,
            ), true)      
        );
    }
}

