<?php
class DubinnController extends Controller
{
    public function actionIndex()
    {
        $emails = array();
        $duplicateData = Yii::app()->db->createCommand()
            ->select('inn, COUNT(inn) as cnt')
            ->from('user')
            ->group('inn')
            ->having('cnt > 1')
            ->queryAll()
        ;

        foreach($duplicateData as $dublicateEmail) {
            $emails[] = array('inn'=>$dublicateEmail['inn'], 'count' => $dublicateEmail['cnt']);
        }
        
        $dataProvider = new CArrayDataProvider($emails, 
            array(
                'pagination'=>array(
                    'pageSize'=>'20'
                )
            )
        );

        $this->render('inn', array('data'=>$dataProvider));
    }
}
