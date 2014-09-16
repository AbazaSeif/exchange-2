<?php
class StatisticsController extends Controller
{
    public function actionIndex($transportType = 2)
    {
        $this->render('statistics', array());
    }
    
    public function actionGetExcel()
    {
        $model = Transport::model()->findAll(array('order'=>'date_close desc', 'condition'=>'status=0'));
        
        Yii::app()->request->sendFile('excel.xls', 
            $this->renderPartial('excel', array(
                'model'=>$model,
            ), true)      
        );
        //Yii::app()->request->redirect('/admin/statistics/');
    }
}

