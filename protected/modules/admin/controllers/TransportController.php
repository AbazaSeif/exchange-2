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
                    'location_from' => 'Место загрузки111',
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
                $view = $this->renderPartial('edittransport', array('model'=>$model, 'rates'=>$rates), true, true);
            }
            $this->render('transport', array('data'=>$dataProvider, 'view'=>$view));
        }else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionCreateTransport()
    {
        if(Yii::app()->user->checkAccess('createTransport')){
            $model = new Transport();
            $model->date_from = date('Y-m-d H:i');
            $model->date_to = date('Y-m-d H:i');
            if(isset($_POST['Transport'])){
                $model->attributes = $_POST['Transport'];
                $model['new_transport'] = 1;
                $model['status']  = 1;
                $model['user_id'] = Yii::app()->user->_id;
                $model['currency'] = $_POST['Transport']['currency'];
                $model['date_published'] = date('Y-m-d H:i:s');
                $message = 'Создана перевозка ' . $model['location_from'] . ' — ' . $model['location_to'];
                Changes::saveChange($message);
                if($model->save()){
                    Yii::app()->user->setFlash('saved_id', $model->id);
                    Yii::app()->user->setFlash('message', 'Перевозка создана успешно.');
                    $this->redirect('/admin/transport/');
                }
            }
            $this->renderPartial('edittransport', array('model'=>$model));//, 'group'=>$group), false, true);
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionEditTransport($id)
    {
        if(Yii::app()->user->checkAccess('editTransport')){
            $model = Transport::model()->findByPk($id);
            $rates = Rate::model()->findAll(array('order'=>'date desc', 'condition'=>'transport_id='.$id));
            if (isset($_POST['Transport'])){
                $changes = array();
                foreach($_POST['Transport'] as $key=>$value){
                    if(trim($model[$key]) != trim($value)){
                        $changes[$key]['before'] = $model[$key];
                        $changes[$key]['after'] = $value;
                        $model[$key] = trim($value);
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
                
                if(!isset($_POST['Rates'])){
                    $model['rate_id'] = NULL;
                    $criteria = new CDbCriteria;
                    $criteria->addCondition('transport_id = ' . $model['id']);
                    // Delete rates and save changes
                    Changes::saveChangeInRates($criteria);
                }
                if($model->save()){
                    Yii::app()->user->setFlash('saved_id', $model->id);
                    Yii::app()->user->setFlash('message', 'Перевозка сохранена успешно.');
                    $this->redirect('/admin/transport/');
                }
            }
            
            $this->renderPartial('edittransport', array('model'=>$model, 'rates'=>$rates)); //, false, true);
        }else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
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
}