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
        $sql = '';
        $label = 'Все перевозки';
        
        if($type) {
            if($type == 1){
                $label = 'Международные перевозки';
                $sql = ' and type=0';
            } else {
                $label = 'Региональные перевозки';
                $sql = ' and type=1';
            }
        }

        if(empty($from)) $from = '2014-01-01';
        if(empty($to)) $to = date('Y-m-d');

        $model = Transport::model()->findAll(array('order'=>'date_close desc', 'condition'=>'status=0'.$sql.' and date_close between "'.date('Y-m-d', strtotime($from)).'" and "'.date('Y-m-d', strtotime($to.' +1 days')).'"'));
        
        Yii::app()->request->sendFile('Статистика биржи перевозок на '.date('Y-m-d H-i-s').'.xls',
            $this->renderPartial('excel', array(
                'model'=>$model,
                'dateFrom' => $from,
                'dateTo' => $to,
                'label' => $label,
            ), true)      
        );
    }
}

