<?php

class TransportController extends Controller
{
    public function actionIndex()
    {
        if(Yii::app()->user->checkAccess('readTransport'))
        {
            $criteria = new CDbCriteria();
            $sort = new CSort();
            $sort->sortVar = 'sort';
            $sort->defaultOrder = 'location_from ASC';
            $sort->attributes = array(
                'location_from' => array(
                    'location_from' => 'Место разгрузки',
                    'asc' => 'location_from ASC',
                    'desc' => 'location_from DESC',
                    'default' => 'asc',
                ),
                'location_to' => array(
                    'location_to' => 'Место загрузки',
                    'asc' => 'location_to ASC',
                    'desc' => 'location_to DESC',
                    'default' => 'asc',
                ),
            );
            $dataProvider = new CActiveDataProvider('Transport', 
                array(
                    'criteria'=>$criteria,
                    'sort'=>$sort,
                    'pagination'=>array(
                        'pageSize'=>'10'
                    )
                )
            );

            if ($id = Yii::app()->user->getFlash('saved_id')){
                $model = Transport::model()->findByPk($id);
                $rates = Rate::model()->findAll(array('order'=>'date desc', 'condition'=>'transport_id='.$id));
                $points = TransportInterPoint::model()->findAll(array('order'=>'sort', 'condition'=>'t_id = ' . $id)); 
                $view = $this->renderPartial('edittransport', array('model'=>$model, 'rates'=>$rates, 'points'=>$points), true, true);
            }
            $this->render('transport', array('data'=>$dataProvider, 'view'=>$view));
        }else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionCreateTransport()
    {
        if(Yii::app()->user->checkAccess('createTransport')){
            $model = new Transport;
            //$this->performAjaxValidation($model);
            $model->date_from = date('d-m-Y');
            $model->date_to = date('d-m-Y');
            if(isset($_POST['Transport'])) {
                $model->attributes = $_POST['Transport'];
                //$model->date_from = date('Y-m-d H:i:s', strtotime($model->date_from . ' 08:00:00'));
                $model->date_from = date('Y-m-d H:i:s', strtotime($model->date_from));
                //$model->date_to = date('Y-m-d H:i:s', strtotime($model->date_to . ' 08:00:00'));
                $model->date_to = date('Y-m-d H:i:s', strtotime($model->date_to));
                $model->description = $this->formatDescription($model->description);      
                $model->new_transport = 1;
                $model->status  = 1;
                $model->user_id = Yii::app()->user->_id;
                $model->currency = $_POST['Transport']['currency'];
                $model->date_published = date('Y-m-d H:i:s');
                if($model->save()){
                    $message = 'Создана перевозка ' . $model->location_from . ' — ' . $model->location_to;
                    Changes::saveChange($message);
                    
                    Yii::app()->user->setFlash('saved_id', $model->id);
                    Yii::app()->user->setFlash('message', 'Перевозка создана успешно.');
                    
                    $this->redirect('/admin/');
                    //$this->redirect('/admin/transport/');
                }
            }
            $this->renderPartial('edittransport', array('model'=>$model), false, true);
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionEditTransport($id)
    {
        if(Yii::app()->user->checkAccess('editTransport')){
            $model = Transport::model()->findByPk($id);
            
            $rates = Yii::app()->db->createCommand()
                ->select('r.id, r.date, r.price, u.company')
                ->from('rate r')
                ->join('user u', 'r.user_id=u.id')
                ->where('r.transport_id=:id', array(':id'=>$id))
                ->order('r.date desc')
                ->queryAll()
            ;
            
            $minRateId = Yii::app()->db->createCommand()
                ->select('rate_id')
                ->from('transport')
                ->where('id = :id', array(':id' => $id))
                ->queryScalar()
            ;
            
            $points = TransportInterPoint::model()->findAll(array('order'=>'sort', 'condition'=>'t_id = ' . $id)); 
            //echo '<pre>';
            //var_dump($points);
            if (isset($_POST['Transport'])){
                $changes = array();
                foreach($_POST['Transport'] as $key=>$value) {
                    if($key == 'description') {
                        $value = $this->formatDescription($value);
                    } else if($key == 'date_from' || $key == 'date_to') {
                        $value = date('Y-m-d H:i:s', strtotime($value));
                        //$value = date('Y-m-d H:i:s', strtotime($value . ' 08:00:00');
                    }
                    
                    if(trim($model->$key) != trim($value)) {
                        $changes[$key]['before'] = $model[$key];
                        $changes[$key]['after'] = $value;
                        if($key == 'date_from' || $key == 'date_to') {
                            $model->$key = $value; //date('Y-m-d H:i:s', strtotime($value));
                        }
                    }    
                }
                if(!empty($changes)){
                    $message = 'В перевозке с id = '.$id.' были изменены слудующие поля: ';
                    $k = 0;
                    foreach($changes as $key => $value){
                        $k++;
                        if($key == 'currency'){
                            $changes[$key]['before'] = Transport::$currencyGroup[$changes[$key]['before']];
                            $changes[$key]['after'] = Transport::$currencyGroup[$changes[$key]['after']];
                        }
                        
                        $message .= $k . ') Поле '. $key . ' c ' . $changes[$key]['before'] . ' на ' . $changes[$key]['after'] . '; ';
                    }
                    
                    Changes::saveChange($message);
                }
                
                if(!isset($_POST['Rates'])) { // if no rates
                    $model['rate_id'] = NULL;
                    $criteria = new CDbCriteria;
                    $criteria->addCondition('transport_id = ' . $model['id']);
                    // Delete all rates and save changes
                    Changes::saveChangeInRates($criteria, $model['id']);
                }
                
                if(!isset($_POST['Points'])) { // if no points
                    $criteria = new CDbCriteria;
                    $criteria->addCondition('t_id = ' . $model['id']);
                    // Delete all points and save changes
                    Changes::saveChangeInPoints($criteria, $model['id']);
                }

                if($model->save()){
                    Yii::app()->user->setFlash('saved_id', $model->id);
                    Yii::app()->user->setFlash('message', 'Перевозка сохранена успешно.');
                    $this->redirect('/admin/transport/');
                }
                //var_dump($model->getErrors());
            }
            
            $this->renderPartial('edittransport', array('model'=>$model, 'rates'=>$rates, 'minRateId'=>$minRateId, 'points' => $points), false, true);
            //$this->renderPartial('edittransport', array('model'=>$model, 'rates'=>$rates), false, true);
        }else{
            throw new CHttpException(403, Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionDeleteTransport($id)
    {
        if(Yii::app()->user->checkAccess('deleteTransport')){
            $model = Transport::model()->findByPk($id);
            if(Transport::model()->deleteByPk($id)){
                $message = 'Удалена перевозка ' . $model['location_from'] . ' — ' . $model['location_to'];
                Changes::saveChange($message);
                Yii::app()->user->setFlash('message', 'Перевозка "' . $model->location_from . ' &mdash; ' . $model->location_to . '" удалена успешно.');
                $this->redirect('/admin/transport/');
            }
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
    
    public function formatDescription($value)
    {
        $encoding = 'UTF-8';
        $value = mb_ereg_replace('^[\ ]+', '', $value);
        return mb_strtoupper(mb_substr($value, 0, 1, $encoding), $encoding) . mb_strtolower(mb_substr($value, 1, mb_strlen($value), $encoding), $encoding);
    }
}