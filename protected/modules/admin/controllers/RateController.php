<?php

class RateController extends Controller
{        
    public function actionCreateRate()
    {
        if(Yii::app()->user->checkAccess('createRate')) {
            $model = new Rate();
            $model['date'] = date('Y-m-d H:i');
            if (isset($_POST['Rate'])) {
                $model->attributes = $_POST['Rate'];
                if(!$model->save()) Yii::log($model->getErrors(), 'error');
            }
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionEditRate()
    {
        $id = $_POST['id'];
        $minRateId = null;
        $value = $_POST['value'];
        $transportId = $_POST['transportId'];
        $model = Rate::model()->findByPk($id);
        if(Yii::app()->user->checkAccess('editRate')) {
            $prevPrice = $model->price;
            $model->price = $value;
            if($model->save()){
                $minPrice = Yii::app()->db->createCommand()
                    ->select('min(price) as price')
                    ->from('rate')
                    ->where('transport_id = :id', array(':id' => $transportId))
                    ->group('transport_id')
                    ->queryScalar()
                ;
                
                $minRatePrice = Yii::app()->db->createCommand()
                    ->select('id, price, user_id')
                    ->from('rate')
                    ->where('transport_id = :id and price = :price', array(':id' => $transportId, ':price' => $minPrice))
                    ->order('date asc')
                    ->queryRow()
                ;
                
                $minRateId = $minRatePrice['id'];
                $transportModel = Transport::model()->findByPk($transportId);
                $transportModel->rate_id = $minRatePrice['id'];
                $transportModel->save();
                $array = array('message'=>'Ставка успешно сохранена', 'minRateId'=>$minRateId);
                echo json_encode($array);
            }
        } else {
             throw new CHttpException(403,Yii::t('yii', 'У Вас недостаточно прав доступа.'));
        }
    }

    public function actionDeleteRate()
    {
        $id = $_POST['id'];
        $minRateId = null;
        $transportId = $_POST['transportId'];
        if(Yii::app()->user->checkAccess('deleteRate') && $id != Yii::app()->user->getState('_id')) {
            $transportModel = Transport::model()->findByPk($transportId);
            Rate::model()->deleteByPk($id);
            if((int)$transportModel->rate_id == (int)$id) {
                $minPrice = Yii::app()->db->createCommand()
                    ->select('min(price) as price, id')
                    ->from('rate')
                    ->where('transport_id = :id', array(':id' => $transportId))
                    ->group('transport_id')
                    ->order('date')
                    ->queryRow()
                ;
                if(!empty($minPrice)) {
                    $transportModel->rate_id = $minPrice['id'];
                    $minRateId = $minPrice['id'];
                } else {
                    $transportModel->rate_id = null;
                    $minRateId = 'close';
                }
                $transportModel->save();
            }
            $array = array('message'=>'Ставка успешно удалена', 'id'=>$id, 'minRateId'=>$minRateId);
            echo json_encode($array);
        } else {
            throw new CHttpException(403,Yii::t('yii', 'У Вас недостаточно прав доступа.'));
        }
    }
}