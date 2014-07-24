<?php
class DubemailController extends Controller
{
    public function actionIndex()
    {
        $emails = array();
        $duplicateData = Yii::app()->db->createCommand()
            ->select('email, COUNT(email) as cnt')
            ->from('user')
            ->group('email')
            ->having('cnt > 1')
            ->queryAll()
        ;
        //var_dump($duplicateData);exit;
        foreach($duplicateData as $dublicateEmail) {
            $emails[] = array('email'=>$dublicateEmail['email'], 'count' => $dublicateEmail['cnt']);
        }
        
        $dataProvider = new CArrayDataProvider($emails, 
            array(
                'pagination'=>array(
                    'pageSize'=>'20'
                )
            )
        );

        $this->render('email', array('data'=>$dataProvider));
    }
}

