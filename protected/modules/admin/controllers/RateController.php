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
        $newPrice = $_POST['value'];
        $transportId = $_POST['transportId'];
        $model = Rate::model()->findByPk($id);
        if(Yii::app()->user->checkAccess('editRate')) {
            $prevPrice = $model->price;
            $oldPrice = $model->price;
            $model->price = $newPrice;
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
                
                $message = 'Изменена ставка с id = '. $id . ' в перевозке "' . $transportModel->location_from . ' — ' . $transportModel->location_to . '" (id = '.$transportModel->id.')'.
                    ' - цена "' . $oldPrice . '" на "' . $newPrice . '"'
                ;
                
                $transportModel->rate_id = $minRatePrice['id'];
                $transportModel->save();
                Changes::saveChange($message);
                $array = array('message'=>'Ставка успешно сохранена', 'minRateId'=>$minRateId);
                echo json_encode($array);
            }
        } else {
             throw new CHttpException(403,Yii::t('yii', 'У Вас недостаточно прав доступа.'));
        }
    }

    public function actionDeleteRate()
    {
        $transportId = $_POST['transportId'];
        $id = (int)$_POST['id'];
        $minRateId = null;
        
        if(Yii::app()->user->checkAccess('deleteRate') && $id != Yii::app()->user->getState('_id')) {
            $transportModel = Transport::model()->findByPk($transportId);
            $rate = Rate::model()->findByPk($id);
            $currency = '€';
            if(!$transportModel->currency) $currency = 'руб.';
            else if($transportModel->currency == 1) $currency = '$';
            $userName = User::model()->findByPk($rate->user_id);
            $message = 'Удалена ставка (id = '.$id.') пользователя '.$userName->company.' (id = '.$rate->user_id.') от '.date("d.m.Y H:i:s", strtotime($rate->date)).' на сумму '.$rate->price.' '.$currency.' в перевозке "' . $transportModel->location_from . ' — ' . $transportModel->location_to . '" (id = '.$transportModel->id.')';
            Changes::saveChange($message);
            Rate::model()->deleteByPk($id);
            
            if($transportModel->rate_id == $id) {
                $minPrice = Yii::app()->db->createCommand()
                    ->select('min(price) as price')
                    ->from('rate')
                    ->where('transport_id = :id', array(':id' => $transportId))
                    ->group('transport_id')
                    ->order('date')
                    ->queryScalar()
                ;

                if(!empty($minPrice)) {
                    $minRateId = Yii::app()->db->createCommand()
                        ->select('id')
                        ->from('rate')
                        ->where('transport_id = :transport_id', array(':transport_id' => $transportId))
                        ->andWhere(array('like', 'price', $minPrice))
                        ->order('date')
                        ->queryScalar()
                    ;
                    $transportModel->rate_id = $minRateId;
                    $minRateId = $minRateId;
                } else {
                    $transportModel->rate_id = null;
                    $minRateId = 'empty';
                }
                $transportModel->save();
            }
            
            $array = array('message'=>'Ставка успешно удалена', 'id'=>$id, 'minRateId'=>$minRateId, 'minPrice'=>$minPrice);
            echo json_encode($array);
        } else {
            throw new CHttpException(403,Yii::t('yii', 'У Вас недостаточно прав доступа.'));
        }
    }
}