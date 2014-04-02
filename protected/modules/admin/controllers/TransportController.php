<?php

class TransportController extends Controller
{
    public function actionIndex()
    {
        if(Yii::app()->user->checkAccess('readTransport'))
        {
            $criteriaActive = new CDbCriteria();            
            $criteriaActive->condition = 't.status = :status';
            $criteriaActive->params = array(':status' => 1);
            $criteriaArchive = new CDbCriteria();
            $criteriaArchive->compare('status', 0);
            
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
                't_id' => array(
                    't_id' => 'Id перевозки',
                    'asc' => 't_id ASC',
                    'desc' => 't_id DESC',
                    'default' => 'asc',
                ),
                'date_close' => array(
                    'asc' => 'date_close ASC',
                    'desc' => 'date_close DESC',
                    'default' => 'asc',
                ),
            );
            
            $sortArchive = new CSort();
            $sortArchive->sortVar = 'sort';
            $sortArchive->defaultOrder = 'location_from ASC';
            
            $sortArchive->attributes = array(
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
                't_id' => array(
                    't_id' => 'Id перевозки',
                    'asc' => 't_id ASC',
                    'desc' => 't_id DESC',
                    'default' => 'asc',
                ),
                'date_close' => array(
                    'asc' => 'date_close ASC',
                    'desc' => 'date_close DESC',
                    'default' => 'asc',
                ),
            );
            
            $dataActive = new CActiveDataProvider('Transport', 
                array(
                    'criteria' => $criteriaActive,
                    'sort' => $sort,
                    'pagination' => array(
                        'pageSize' => '10'
                    )
                )
            );
            
            $dataArchive = new CActiveDataProvider('Transport', 
                array(
                    'criteria' => $criteriaArchive,
                    'sort' => $sortArchive,
                    'pagination' => array(
                        'pageSize'=>'10'
                    )
                )
            );

            if ($id = Yii::app()->user->getFlash('saved_id')) {
                $model = Transport::model()->findByPk($id);
                $rates = Rate::model()->findAll(array('order'=>'date desc', 'condition'=>'transport_id='.$id));
                $points = TransportInterPoint::model()->findAll(array('order'=>'sort', 'condition'=>'t_id = ' . $id)); 
                $view = $this->renderPartial('edittransport', array('model'=>$model, 'rates'=>$rates, 'points'=>$points), true, true);
            }
            
            $this->render('transport', array('dataActive'=>$dataActive, 'dataArchive'=>$dataArchive, 'view'=>$view));
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionCreateTransport()
    {
        if(Yii::app()->user->checkAccess('createTransport')) {
            $form = new TransportForm;
            $form->status = 1;
            $form->date_close = date('d-m-Y H:i', strtotime("+" . 2*Yii::app()->params['hoursBefore'] . " hours"));
            $form->date_from = date('d-m-Y H:i', strtotime("+" . 3*Yii::app()->params['hoursBefore'] . " hours"));
            $form->date_to = date('d-m-Y H:i', strtotime("+" . 4*Yii::app()->params['hoursBefore'] . " hours"));
            $form->date_to_customs_clearance_RF = date('d-m-Y H:i', strtotime("+" . 4*Yii::app()->params['hoursBefore'] . " hours"));
            
            if(isset($_POST['TransportForm'])) {
                $model = new Transport;
                $model->attributes = $_POST['TransportForm'];
                $model->auto_info = $_POST['TransportForm']['auto_info'];
                $model->date_from = date('Y-m-d H:i:s', strtotime($model->date_from));
                $model->date_to = date('Y-m-d H:i:s', strtotime($model->date_to));
                $model->date_close = date('Y-m-d H:i:s', strtotime($model->date_close));
                $model->description = $this->formatDescription($model->description);      
                $model->new_transport = 1;
                $model->user_id = Yii::app()->user->_id;
                $model->date_published = date('Y-m-d H:i:s');
                
                if($model->type == 0) { // international
                    if(!empty($_POST['TransportForm']['date_to_customs_clearance_RF']) && !empty($_POST['TransportForm']['customs_clearance_EU']) && !empty($_POST['TransportForm']['customs_clearance_RF'])) {
                        if($model->save()) {
                            if($form->type == 0) {
                                $point = new TransportInterPoint;
                                $point->t_id = $model->id;
                                $point->point = $_POST['TransportForm']['customs_clearance_EU'];
                                $point->sort = 1;
                                $point->save();
                                
                                $point = new TransportInterPoint;
                                $point->t_id = $model->id;
                                $point->point = $_POST['TransportForm']['customs_clearance_RF'];
                                $point->date = date('Y-m-d H:i:s', strtotime($_POST['TransportForm']['date_to_customs_clearance_RF']));
                                $point->sort = 2;
                                $point->save();
                            }
                            
                            $message = 'Создана перевозка "' . $model->location_from . ' — ' . $model->location_to . '" (id = '.$model->id.')';
                            Changes::saveChange($message);
                            Yii::app()->user->setFlash('saved_id', $model->id);
                            Yii::app()->user->setFlash('message', 'Перевозка создана успешно.');
                            
                            $this->redirect(array('/admin/transport/edittransport', 'id'=>$model->id));
                        } else Yii::log($model->getErrors(), 'error');    
                    } else {
                        $message = '';
                        if(empty($_POST['TransportForm']['date_to_customs_clearance_RF'])){
                            $message = '"' . Transport::model()->getAttributeLabel(date_to_customs_clearance_RF) . '"';
                        }
                        if(empty($_POST['TransportForm']['customs_clearance_EU'])){
                            if(!empty($message)) $message .= ', ';
                            $message .= '"' . Transport::model()->getAttributeLabel(customs_clearance_EU) . '"';
                        }
                        if(empty($_POST['TransportForm']['customs_clearance_RF'])) {
                            if(!empty($message)) $message .= ', ';
                            $message .= '"' . Transport::model()->getAttributeLabel(customs_clearance_RF) . '"';
                        }
                        
                        Yii::app()->user->setFlash('error', 'Заполните следующие поля: ' . $message);
                        $form->attributes = $_POST['TransportForm'];
                        $this->render('edittransport', array('model'=>$form), false, true);
                    }
                } else { // regional transport
                    $_POST['TransportForm']['customs_clearance_EU'] = '';
                    $_POST['TransportForm']['customs_clearance_RF'] = '';
                    $_POST['TransportForm']['date_to_customs_clearance_RF'] = '';
                    
                    if(!empty($_POST['TransportForm']['date_to'])){
                        if($model->save()) {
                            $message = 'Создана перевозка "' . $model->location_from . ' — ' . $model->location_to . '"';
                            Changes::saveChange($message);
                            Yii::app()->user->setFlash('saved_id', $model->id);
                            Yii::app()->user->setFlash('message', 'Перевозка создана успешно.');
                            
                            $this->redirect(array('/admin/transport/edittransport', 'id'=>$model->id));
                        } else Yii::log($model->getErrors(), 'error');    
                    } else {
                        Yii::app()->user->setFlash('error', 'Заполните поле: ' . Transport::model()->getAttributeLabel(date_to));
                        $form->attributes = $_POST['TransportForm'];
                        $this->render('edittransport', array('model'=>$form), false, true);
                    }
                }
            } else $this->render('edittransport', array('model'=>$form), false, true);
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionEditTransport($id)
    {
        if(Yii::app()->user->checkAccess('editTransport')){
            $model = Transport::model()->findByPk($id);
            $form = new TransportForm;
            $form->attributes = $model->attributes;
            $form->id = $model->id;
            $form->date_to = $model->date_to;
            $form->auto_info = $model->auto_info;
            if($form->type == 0) {
                $customs_clearance_EU = TransportInterPoint::model()->find(array('order'=>'sort', 'condition'=>'t_id = ' . $id, 'limit'=>1));
                $form->customs_clearance_EU = $customs_clearance_EU->point;
                        
                $customs_clearance_RF = TransportInterPoint::model()->find(array('order'=>'sort desc', 'condition'=>'t_id = ' . $id, 'limit'=>1)); 
                $form->customs_clearance_RF = $customs_clearance_RF->point;
                $form->date_to_customs_clearance_RF = date('d-m-Y H:i', strtotime($customs_clearance_RF->date));
            }
            
            if (isset($_POST['TransportForm'])) {
                $changes = array();
                if($form->type == 0) {
                    $customs_clearance_EU = TransportInterPoint::model()->find(array('order'=>'sort', 'condition'=>'t_id = ' . $id, 'limit'=>1));
                    $customs_clearance_RF = TransportInterPoint::model()->find(array('order'=>'sort desc', 'condition'=>'t_id = ' . $id, 'limit'=>1));
                }
                
                foreach($_POST['TransportForm'] as $key=>$value) {
                    if($key == 'description') {
                        $value = $this->formatDescription($value);
                    } else if($key == 'date_from' || $key == 'date_to' || $key == 'date_close') {
                        $value = date('Y-m-d H:i:s', strtotime($value));
                    } else if($form->type == 0){
                        if($key == 'customs_clearance_EU' && trim($customs_clearance_EU->point) != trim($_POST['TransportForm']['customs_clearance_EU'])) {
                            $changes[$key]['before'] = $customs_clearance_EU->point;
                            $changes[$key]['after']  = trim($_POST['TransportForm']['customs_clearance_EU']);
                        } 
                        if($key == 'customs_clearance_RF' && trim($customs_clearance_RF->point) != trim($_POST['TransportForm']['customs_clearance_RF'])) {
                            $changes[$key]['before'] = $customs_clearance_RF->point;
                            $changes[$key]['after']  = trim($_POST['TransportForm']['customs_clearance_RF']);
                        }
                        if($key == 'date_to_customs_clearance_RF' && date('Y-m-d H:i:s', strtotime($customs_clearance_RF->date)) != date('Y-m-d H:i:s', strtotime($_POST['TransportForm']['date_to_customs_clearance_RF']))) {
                            $changes[$key]['before'] = date('Y-m-d H:i:s', strtotime($customs_clearance_RF->date));
                            $changes[$key]['after']  = date('Y-m-d H:i:s', strtotime(trim($_POST['TransportForm']['date_to_customs_clearance_RF'])));
                        }
                    }
                    if($key != 'customs_clearance_EU' && $key != 'customs_clearance_RF' && $key != 'date_to_customs_clearance_RF'){
                        if(trim($model->$key) != trim($value)) {
                            $changes[$key]['before'] = $model->$key;
                            $changes[$key]['after']  = $value;
                        }
                    }
                }
                
                $model->attributes = $_POST['TransportForm'];
                $form->attributes = $_POST['TransportForm'];
                $form->auto_info = $_POST['TransportForm']['auto_info'];
                $model->date_from = date('Y-m-d H:i:s', strtotime($model->date_from));
                $model->date_to = date('Y-m-d H:i:s', strtotime($model->date_to));
                $model->date_close = date('Y-m-d H:i:s', strtotime($model->date_close));
                
                if(!empty($changes)) {
                    $message = 'В перевозке "'.$model->location_from.' - '.$model->location_to.'" (id = '.$id.') были изменены слудующие поля: ';
                    $k = 0;
                    foreach($changes as $key => $value){
                        $k++;
                        if($key == 'currency') {
                            $changes[$key]['before'] = Transport::$currencyGroup[$changes[$key]['before']];
                            $changes[$key]['after']  = Transport::$currencyGroup[$changes[$key]['after']];
                        }
                        
                        $message .= $k . ') Поле '. $key . ' c "' . $changes[$key]['before'] . '" на "' . $changes[$key]['after'] . '"; ';
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
                
                /* if(!isset($_POST['Points'])) { // if no points
                    $criteria = new CDbCriteria;
                    $criteria->addCondition('t_id = ' . $model['id']);
                    
                    // Delete all points and save changes
                    Changes::saveChangeInPoints($criteria, $model['id']);
                }*/
                
                if($model->save()) {
                    Yii::app()->user->setFlash('saved_id', $model->id);
                    Yii::app()->user->setFlash('message', 'Перевозка сохранена успешно.');
                    if($form->type == 0) {
                        $customs_clearance_EU->point = $_POST['TransportForm']['customs_clearance_EU'];
                        $customs_clearance_EU->save();
                        $form->customs_clearance_EU = $customs_clearance_EU->point;
                        
                        $customs_clearance_RF->point = $_POST['TransportForm']['customs_clearance_RF'];
                        $customs_clearance_RF->date = date('Y-m-d H:i:s', strtotime($_POST['TransportForm']['date_to_customs_clearance_RF']));
                        $customs_clearance_RF->save();
                        $form->customs_clearance_RF = $customs_clearance_RF->point;
                        $form->date_to_customs_clearance_RF = date('d-m-Y H:i', strtotime($customs_clearance_RF->date));
                    }
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
                    
                    /**************************/
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
                } else Yii::log($model->getErrors(), 'error');
            } else {
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
            }
            $this->render('edittransport', array('model'=>$form, 'rates'=>$rates, 'minRateId'=>$minRateId, 'points' => $points), false, true);
        } else {
            throw new CHttpException(403, Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionDeleteTransport($id)
    {
        if(Yii::app()->user->checkAccess('deleteTransport')){
            $model = Transport::model()->findByPk($id);
            $transportName = $model->location_from . ' — ' . $model->location_to;
            if(Transport::model()->deleteByPk($id)){
                $message = 'Удалена перевозка "' . $transportName . '"';
                Changes::saveChange($message);
                Yii::app()->user->setFlash('message', 'Перевозка "' . $transportName . '" удалена успешно.');
                $this->redirect('/admin/transport/');
            }
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
    
    public function actionDuplicateTransport($id)
    {
        $model = Transport::model()->findByPk($id);
        $newModel = new TransportForm;
        $newModel->attributes = $model->attributes;
        $newModel->location_from = 'Копия ' . $newModel->location_from;
        $newModel->status = 1;
        
        if($model->type == 1)  $newModel->date_to = $model->date_to;
        else{
            $customs_clearance_EU = TransportInterPoint::model()->find(array('order'=>'sort', 'condition'=>'t_id = ' . $id, 'limit'=>1));
            $newModel->customs_clearance_EU = $customs_clearance_EU->point;

            $customs_clearance_RF = TransportInterPoint::model()->find(array('order'=>'sort desc', 'condition'=>'t_id = ' . $id, 'limit'=>1)); 
            $newModel->customs_clearance_RF = $customs_clearance_RF->point;
            $newModel->date_to_customs_clearance_RF = date('d-m-Y H:i', strtotime($customs_clearance_RF->date));
            $newModel->date_to = date('d-m-Y H:i', strtotime($customs_clearance_RF->date));
        }
        
        $this->render('edittransport', array('model'=>$newModel), false, true);
    }
    
    public function formatDescription($value)
    {
        $encoding = 'UTF-8';
        $value = mb_ereg_replace('^[\ ]+', '', $value);
        return mb_strtoupper(mb_substr($value, 0, 1, $encoding), $encoding) . mb_strtolower(mb_substr($value, 1, mb_strlen($value), $encoding), $encoding);
    }
}