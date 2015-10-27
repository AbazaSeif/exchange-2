<?php

class TransportController extends Controller
{
    public function actionIndex($transportType = 2)
    {
        $showAllTransports = 2;
        if(Yii::app()->user->checkAccess('readTransport'))
        {            
            // Active transport
            $dataActive = new Transport;
            $dataActive->unsetAttributes();
            if (!empty($_GET['Transport']))
                $dataActive->attributes = $_GET['Transport'];   
            
            if($transportType != $showAllTransports) 
                $dataActive->type = $transportType;
            
            $dataActive->status = 1;
            
            // Archive transport
            
            $dataArchive = new Transport;
            $dataArchive->unsetAttributes();
            if (!empty($_GET['Transport']))
                $dataArchive->attributes = $_GET['Transport'];   
            
            if($transportType != $showAllTransports) 
                $dataArchive->type = $transportType;
            
            $dataArchive->status = 0;
            
            // Draft transport
            $dataDraft = new Transport;
            $dataDraft->unsetAttributes();
            if (!empty($_GET['Transport']))
                $dataDraft->attributes = $_GET['Transport'];   
            
            if($transportType != $showAllTransports) 
                $dataDraft->type = $transportType;
            
            $dataDraft->status = 2;
            
            // Deleted transport
            $dataDel = new Transport;
            $dataDel->unsetAttributes();
            if (!empty($_GET['Transport']))
                $dataDel->attributes = $_GET['Transport'];   
            
            if($transportType != $showAllTransports) 
                $dataDel->type = $transportType;
            
            $dataDel->status = 3;
            $delProvider = $dataDel->search();
            //$delProvider->sort->defaultOrder = 'del_date';

            if ($id = Yii::app()->user->getFlash('saved_id')) {
                $model = Transport::model()->findByPk($id);
                $rates = Rate::model()->findAll(array('order'=>'date desc', 'condition'=>'transport_id='.$id));
                $points = TransportInterPoint::model()->findAll(array('order'=>'sort', 'condition'=>'t_id = ' . $id)); 
                $view = $this->renderPartial('edittransport', array('model'=>$model, 'rates'=>$rates, 'points'=>$points), true, true);
            }
            
            $this->render('transport', array(
                'dataActive'=>$dataActive, 
                'dataArchive'=>$dataArchive, 
                'dataDraft' =>$dataDraft, 
                'dataDel' =>$dataDel, 
                'delProvider' => $delProvider,
                'view'=>$view, 
                'type' => $transportType
            ));
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionCreateTransport()
    {
        if(Yii::app()->user->checkAccess('createTransport')) {
            $form = new TransportForm;
            $form->status = 1;
            $form->date_close = date('d-m-Y', strtotime("+" . 2*Yii::app()->params['hoursBefore'] . " hours")) . ' 14:00';
            $form->date_from = date('d-m-Y', strtotime("+" . 3*Yii::app()->params['hoursBefore'] . " hours")) . ' 08:00';
            $form->date_to = date('d-m-Y', strtotime("+" . 4*Yii::app()->params['hoursBefore'] . " hours")) . ' 08:00';
            $form->date_to_customs_clearance_RF = date('d-m-Y', strtotime("+" . 4*Yii::app()->params['hoursBefore'] . " hours")) . ' 08:00';
            
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
                                
                                $point2 = new TransportInterPoint;
                                $point2->t_id = $model->id;
                                $point2->point = $_POST['TransportForm']['customs_clearance_RF'];
                                $point2->date = date('Y-m-d H:i:s', strtotime($_POST['TransportForm']['date_to_customs_clearance_RF']));
                                $point2->sort = 2;
                                $point2->save();
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
        if(Yii::app()->user->checkAccess('editTransport')) {
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
                    if($key == 'date_from' || $key == 'date_to' || $key == 'date_close') {
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
                //if($form->type == 1) 
                $model->date_to = date('Y-m-d H:i:s', strtotime($model->date_to));
                //else $model->date_to = date('Y-m-d H:i:s', strtotime($_POST['TransportForm']['date_to_customs_clearance_RF']));
                $model->date_close = date('Y-m-d H:i:s', strtotime($model->date_close));
                
                if(!empty($changes)) {
                    $message = 'В перевозке "'.$model->location_from.' - '.$model->location_to.'" (id='.$id.') были изменены слудующие поля: ';
                    $k = 0;
                    foreach($changes as $key => $value){
                        $k++;
                        if($key == 'currency') {
                            $changes[$key]['before'] = Transport::$currencyGroup[$changes[$key]['before']];
                            $changes[$key]['after']  = Transport::$currencyGroup[$changes[$key]['after']];
                        } else if($key == 'status') {
                            $changes[$key]['before'] = Transport::$status[$changes[$key]['before']];
                            $changes[$key]['after']  = Transport::$status[$changes[$key]['after']];
                        } else if($key == 'type') {
                            $val = $changes[$key]['before'];
                            $changes[$key]['before'] = Transport::$group[$changes[$key]['before']];
                            $changes[$key]['after']  = Transport::$group[$changes[$key]['after']];
                            
                            TransportInterPoint::model()->deleteAll('t_id = :id', array(':id'=>$id));
                            if($val == 1) {
                                $point = new TransportInterPoint;
                                $point->t_id = $id;
                                $point->point = trim($_POST['TransportForm']['customs_clearance_EU']);
                                $point->sort = 1;
                                $point->save();
                                $point2 = new TransportInterPoint;
                                $point2->t_id = $id;
                                $point2->point = trim($_POST['TransportForm']['customs_clearance_RF']);
                                $point2->date = date('Y-m-d H:i:s', strtotime($_POST['TransportForm']['date_to_customs_clearance_RF']));
                                $point2->sort = 2;
                                $point2->save();
                                
                                $form->customs_clearance_EU = $point->point;
                                $form->customs_clearance_RF = $point2->point;
                                $form->date_to_customs_clearance_RF = date('d-m-Y H:i', strtotime($point2->date));
                            }
                        }
                        
                        $message .= $k . ') Поле "'. $model->getAttributeLabel($key) . '" c "' . $changes[$key]['before'] . '" на "' . $changes[$key]['after'] . '"; ';
                    }
                    
                    Changes::saveChange($message);
                    Yii::app()->user->setFlash('message', 'Перевозка сохранена успешно.');
                }

                if($model->save()) {
                    Yii::app()->user->setFlash('saved_id', $model->id);
                    
                    if($form->type == 0) {
                        $rowsInInterPoint = TransportInterPoint::model()->count('t_id = :id', array(':id'=>$id));
                        if($rowsInInterPoint < 2) {
                            TransportInterPoint::model()->deleteAll('t_id = :id', array(':id'=>$id));
                            $point = new TransportInterPoint;
                            $point->t_id = $id;
                            $point->point = trim($_POST['TransportForm']['customs_clearance_EU']);
                            $point->sort = 1;
                            $point->save();
                            $point2 = new TransportInterPoint;
                            $point2->t_id = $id;
                            $point2->point = trim($_POST['TransportForm']['customs_clearance_RF']);
                            $point2->date = date('Y-m-d H:i:s', strtotime($_POST['TransportForm']['date_to_customs_clearance_RF']));
                            $point2->sort = 2;
                            $point2->save();
                        } else {
                            $customs_clearance_EU->point = $_POST['TransportForm']['customs_clearance_EU'];
                            $customs_clearance_EU->save();
                            $form->customs_clearance_EU = $customs_clearance_EU->point;
                            $customs_clearance_RF->point = $_POST['TransportForm']['customs_clearance_RF'];
                            $customs_clearance_RF->date = date('Y-m-d H:i:s', strtotime($_POST['TransportForm']['date_to_customs_clearance_RF']));
                            $customs_clearance_RF->save();
                            $form->customs_clearance_RF = $customs_clearance_RF->point;
                            $form->date_to_customs_clearance_RF = date('d-m-Y H:i', strtotime($customs_clearance_RF->date));
                        }
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
                $minRateId = '';
                $rates = Yii::app()->db->createCommand()
                    ->select('r.id, r.date, r.price, u.company')
                    ->from('rate r')
                    ->join('user u', 'r.user_id=u.id')
                    ->where('r.transport_id=:id', array(':id'=>$id))
                    ->order('r.date desc')
                    ->queryAll()
                ;

                /*$minRateId = Yii::app()->db->createCommand()
                    ->select('rate_id')
                    ->from('transport')
                    ->where('id = :id', array(':id' => $id))
                    ->queryScalar()
                ;*/
                
                $model = new Rate;
                $criteria = new CDbCriteria;
                $criteria->select = 'min(price) AS price, id, user_id';
                $criteria->condition = 'transport_id = :id';
                $criteria->params = array(':id'=>$id);
                $minPrice = $model->model()->find($criteria);
                if(!empty($minPrice['price'])){
                    $criteria->select = 'id, user_id';
                    $criteria->order = 'date';
                    $criteria->condition = 'transport_id = :id and price like :price';
                    $criteria->params = array(':id'=>$id, ':price'=>$minPrice['price']);
                    $row = $model->model()->find($criteria);
                    if(!empty($row['id'])){
                        $minRateId = $row['id'];
                    }
                }

                $points = TransportInterPoint::model()->findAll(array('order'=>'sort', 'condition'=>'t_id = ' . $id));
            }
            $this->render('edittransport', array('model'=>$form, 'rates'=>$rates, 'minRateId'=>$minRateId, 'points' => $points), false, true);
        } else {
            throw new CHttpException(403, Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    /*public function actionDeleteTransport($id)
    {
        if(Yii::app()->user->checkAccess('deleteTransport')){
            $model = Transport::model()->findByPk($id);
            $tId = (!empty($model->t_id))? '('.$model->t_id.') ':'';
            $transportName = $model->location_from . ' — ' . $model->location_to;
            $type = mb_strtolower(Transport::$group[$model->type], 'UTF-8');
            $rates = Rate::model()->findAll('transport_id = :id',array('id'=>$id));
            if(Transport::model()->deleteByPk($id)){
                $message = 'Удалена '.$type.' перевозка "' . $transportName . '" ' . $tId . '(id='.$id.'). ';
                if(!empty($rates)){
                    $message .= 'Также были удалены ставки сделанные в этой перевозке: ';
                    $count = 1;
                    foreach($rates as $rate) {
                        $user = User::model()->findByPk($rate['user_id']);
                        $message .= $count.') '.$rate['price'].' ('.$rate['date'].') - '.$user->company.' (id='.$user->id.'); ';
                        $count++;
                    }
                }
                Changes::saveChange($message);
                Yii::app()->user->setFlash('message', 'Перевозка "' . $transportName . '" удалена успешно.');
                $this->redirect('/admin/transport/');
            }
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }*/
    
    public function actionDeleteTransport()
    {
        $id = $_POST['id'];
        $model = Transport::model()->findByPk($id);
        $rates = Rate::model()->findAll('transport_id = :id',array('id'=>$id));
        
        $tId = (!empty($model->t_id))? '('.$model->t_id.') ':'';
        $transportName = $model->location_from . ' — ' . $model->location_to;
        $type = mb_strtolower(Transport::$group[$model->type], 'UTF-8');
        $userModel = AuthUser::model()->findByPk(Yii::app()->user->_id);
        $transportName = $model->location_from . ' — ' . $model->location_to;
        
        $reason = $_POST['reason'];
        $reason = '('.$userModel->surname.' '.$userModel->name.') : '.$reason;
        
        $model->status = Transport::DEL_TRANSPORT;
        $model->del_reason = $reason;
        $model->del_date = date('Y-m-d H:i:s');
        $model->rate_id = null;
        $model->save();
        
        $message = 'Удалена '.$type.' перевозка "' . $transportName . '" ' . $tId . '(id='.$id.'). ';
        if(!empty($rates)) $message .= 'А также ставки ('.count($rates).' шт.), сделанных в этой перевозке.';
        Rate::model()->deleteAll('transport_id = :id', array('id'=>$id));
        TransportInterPoint::model()->deleteAll('t_id = :id', array('id'=>$id));
        Changes::saveChange($message);
        Yii::app()->user->setFlash('message', 'Перевозка "' . $transportName . '" удалена успешно.');
        $this->redirect('/admin/transport/');
    }
    
    public function actionDuplicateTransport($id)
    {
        $model = Transport::model()->findByPk($id);
        $newModel = new TransportForm;
        $newModel->attributes = $model->attributes;
        $newModel->location_from = 'Копия ' . $newModel->location_from;
        $newModel->status = 1;
        $newModel->new_transport = 1;
        $newModel->date_close = date('d-m-Y', strtotime("+" . 2*Yii::app()->params['hoursBefore'] . " hours")) . ' 08:00';
        $newModel->date_from = date('d-m-Y', strtotime("+" . 3*Yii::app()->params['hoursBefore'] . " hours")) . ' 08:00';
        $newModel->date_to = date('d-m-Y', strtotime("+" . 4*Yii::app()->params['hoursBefore'] . " hours")) . ' 08:00';
        
        $newModel->date_close_new = null;
        $newModel->id = null;
        $newModel->rate_id = null;
        
        //if($model->type == 1)  $newModel->date_to = $model->date_to;
        if($model->type == 0){
            $customs_clearance_EU = TransportInterPoint::model()->find(array('order'=>'sort', 'condition'=>'t_id = ' . $id, 'limit'=>1));
            $newModel->customs_clearance_EU = $customs_clearance_EU->point;

            $customs_clearance_RF = TransportInterPoint::model()->find(array('order'=>'sort desc', 'condition'=>'t_id = ' . $id, 'limit'=>1)); 
            $newModel->customs_clearance_RF = $customs_clearance_RF->point;
            $newModel->date_to_customs_clearance_RF = $newModel->date_to; 
            
            //date('d-m-Y H:i', strtotime($customs_clearance_RF->date));
            //$newModel->date_to = date('d-m-Y H:i', strtotime($customs_clearance_RF->date));
        }
        
        $this->render('edittransport', array('model'=>$newModel), false, true);
    }
    
    public function formatDescription($value)
    {
        $encoding = 'UTF-8';
        $value = mb_ereg_replace('^[\ ]+', '', $value);
        return mb_strtoupper(mb_substr($value, 0, 1, $encoding), $encoding) . mb_strtolower(mb_substr($value, 1, mb_strlen($value), $encoding), $encoding);
    }
    
    public function actionTestUpdate()
    {
        $model = Transport::model()->findByPk(576);
        $model->rate_id = null;
        $model->save();
    }
}