<?php

class UserController extends Controller 
{
    protected function beforeAction($action) 
    {
        if (parent::beforeAction($action)) {
            // Добавление CSS файла для пользователей.
        }
        return true;
    }

    public function actionIndex($status = 5, $input = null) 
    {
        if(Yii::app()->user->checkAccess('trReadUser'))
        {
            $criteria = new CDbCriteria();
            $criteria->condition = 'type_contact = 0';
            if($status != 5) {
                $criteria->condition .= ' and t.status = :status';
                $criteria->params = array(':status' => $status);
            }
            
            if(User::prepareSqlite()) {
                if(!empty($input)) {
                    if(is_numeric($input)) $criteria->condition .= ' and (inn like :input or lower(email) like lower(:input))';
                    else $criteria->condition .= ' and (lower(company) like lower(:input) or lower(email) like lower(:input))';
                    $criteria->params = array_merge($criteria->params, array(':input' => '%' . $input . '%'));
                }
            }
            
            $sort = new CSort();
            $sort->sortVar = 'sort';
            $sort->defaultOrder = 'company ASC';
            $dependecy = new CDbCacheDependency('SELECT MAX(created) FROM user');
            $dataProvider = new CActiveDataProvider(User::model()->cache(1000, $dependecy, 2), array ( 
                'criteria'=>$criteria,
                'sort'=>$sort,
                'pagination' => array ( 
                    'pageSize' => 8, 
                ) 
            ));
            
            $this->render('user/user', array('data'=>$dataProvider, 'input'=>$input));
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionCreateUser()
    {
        if(Yii::app()->user->checkAccess('trCreateUser')) {
            $form = new UserForm;
            $form->block_date = date('d-m-Y', strtotime('+5 days'));
            $emailExists = array();
            if(isset($_POST['UserForm'])) {
                if(!empty($_POST['UserForm']['email'])) {
                    $emailExists = User::model()->find(array(
                        'select'    => 'email',
                        'condition' => 'email=:email',
                        'params'    => array(':email'=>$_POST['UserForm']['email']))
                    );
                }
                
                $innExists = User::model()->find(array(
                    'select'    => 'inn',
                    'condition' => 'inn=:inn',
                    'params'    => array(':inn'=>$_POST['UserForm']['inn']))
                );
                
                if(empty($emailExists) && empty($innExists)) {
                    $model = new User;
                    $model->created = date('Y-m-d H:i:s');
                    $model->attributes = $_POST['UserForm'];
                    $model->password = crypt($_POST['UserForm']['password'], User::model()->blowfishSalt());
                    $model->type_contact = 0;
                    
                    if($model->save()) {
                        $message = 'Создан пользователь ' . $model->name . ' ' . $model->surname;
                        Changes::saveChange($message);

                        $newFerrymanFields = new UserField;
                        $newFerrymanFields->user_id = $model->id;
                        $newFerrymanFields->mail_transport_create_1 = false;
                        $newFerrymanFields->mail_transport_create_2 = false;
                        $newFerrymanFields->mail_kill_rate = false;
                        $newFerrymanFields->mail_before_deadline = false;
                        $newFerrymanFields->mail_deadline = true;
                        $newFerrymanFields->with_nds = false;  
                        $newFerrymanFields->show_intl = true;
                        $newFerrymanFields->show_regl = true;
                        $newFerrymanFields->save();

                        Yii::app()->user->setFlash('saved_id', $model->id);
                        Yii::app()->user->setFlash('message', 'Пользователь "'.$model->company.'" создан успешно.');
                        $form->attributes = $model->attributes;
                        $this->render('user/edituser', array('model'=>$form), false, true);
                    } else Yii::log($model->getErrors(), 'error');
                } else {
                    if(!empty($emailExists) && !empty($innExists)) {
                        Yii::app()->user->setFlash('error', 'Указанные email и inn уже используются. ');
                    } else if(!empty($emailExists)) {
                        Yii::app()->user->setFlash('error', 'Указанный email уже используется. ');
                    } else {
                        Yii::app()->user->setFlash('error', 'Указанный inn уже используется. ');
                    }
                    $criteria = new CDbCriteria();
                    $sort = new CSort();
                    $sort->sortVar = 'sort';
                    $sort->defaultOrder = 'surname ASC';
                    $dataProvider = new CActiveDataProvider('User', 
                        array(
                            'criteria'=>$criteria,
                            'sort'=>$sort,
                            'pagination'=>array(
                                'pageSize'=>'13'
                            )
                        )
                    );
                    $form->attributes = $_POST['UserForm'];
                    $this->render('user/edituser', array('model'=>$form), false, true);
               }
            } else $this->render('user/edituser', array('model'=>$form), false, true);
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionEditUser($id)
    {
        $message = '';
        $model = User::model()->findByPk($id);
        $form = new UserForm;
        $form->attributes = $model->attributes;
        $form->id = $id;
        
        if(Yii::app()->user->checkAccess('trEditUser')) {
            $contacts = Yii::app()->db->createCommand()
                ->select('name, secondname, surname, email')
                ->from('user')
                ->where('parent = '. $id)
                ->queryAll()
            ;
            
            if (isset($_POST['UserForm'])) {
                $changes = $innExists = $emailExists = array();
                $flag = false;
                $warringMessage = '';
                
                if($_POST['UserForm']['status'] == User::USER_NOT_CONFIRMED || $_POST['UserForm']['status'] == User::USER_ACTIVE)
                    $flag = true;
                else if(!empty($_POST['UserForm']['reason'])){
                    if($_POST['UserForm']['status'] == User::USER_TEMPORARY_BLOCKED){
                        if(!empty($_POST['UserForm']['block_date'])) {
                            if(strtotime($_POST['UserForm']['block_date']) <= strtotime(date('d-m-Y'))) $warringMessage = 'В поле "Блокировать до" указана неверная дата.';
                            else $flag = true;
                        } else $warringMessage = 'Поле "Блокировать до" не может быть пустым.';
                    } else $flag = true;
                } else $warringMessage = 'Поле "Причина" не может быть пустым.';
                
                if($flag) {
                    if($_POST['UserForm']['status'] == User::USER_NOT_CONFIRMED || $_POST['UserForm']['status'] == User::USER_ACTIVE){
                        $_POST['UserForm']['reason'] = null;
                    }
                    if($_POST['UserForm']['status'] != User::USER_TEMPORARY_BLOCKED)
                        $_POST['UserForm']['block_date'] = null;

                    foreach ($_POST['UserForm'] as $key => $value) {
                        if ($key == 'block_date') {
                            if(!empty($value))$value = date('Y-m-d', strtotime($value));
                        }
                        if (trim($model[$key]) != trim($value) && $key != 'password' && $key != 'password_confirm') {
                            $changes[$key]['before'] = $model[$key];
                            $changes[$key]['after'] = $value;
                            $model[$key] = trim($value);
                            if($key == 'company'){
                                $allContacts = Yii::app()->db->createCommand()
                                    ->select('id')
                                    ->from('user')
                                    ->where('parent = '. $model->id)
                                    ->queryAll()
                                ;
                                if(!empty($allContacts)) {
                                    foreach ($allContacts as $contact) {
                                        $modelContact = User::model()->findByPk($contact['id']);
                                        $contactName = $modelContact->name;
                                        if(!empty($modelContact->surname)) $contactName .= ' '.$modelContact->surname;
                                        $modelContact->company = 'Контактное лицо "' . $model->company . '" ('.$contactName.')';
                                        $modelContact->save();
                                    }
                                }
                            }
                        } else if($key == 'password' && !empty($_POST['UserForm']['password_confirm']) && $model->password !== crypt(trim($_POST['UserForm']['password_confirm']), $model->password)) {
                            $model->password = crypt($_POST['UserForm']['password_confirm'], User::model()->blowfishSalt());
                            $changes[$key] = 'Изменен пароль';
                        }
                    }

                    if (!empty($changes)) {
                        $message = 'У пользователя '.$model->company.' (id='.$id.') были изменены слудующие поля: ';
                        $k = 0;
                        foreach ($changes as $key => $value) {
                            $k++;
                            if($key == 'password'){
                                $message .= $k . ') ' . $changes[$key];    
                            } else if($key == 'status'){
                                $message .= $k . ') Поле "' . $model->getAttributeLabel($key) . '" c "' . User::statusLabel($changes[$key]['before']) . '" на "' . User::statusLabel($changes[$key]['after']) . '"; ';
                            } else {
                                $message .= $k . ') Поле "' . $model->getAttributeLabel($key) . '" c "' . $changes[$key]['before'] . '" на "' . $changes[$key]['after'] . '"; ';
                            }

                            if($key == 'inn' || $key == 'email') {
                                if($key == 'inn') {
                                    $innExists = User::model()->find(array(
                                        'select'    => 'inn',
                                        'condition' => 'inn=:inn',
                                        'params'    => array(':inn'=>$_POST['UserForm']['inn']))
                                    );

                                    if(!empty($innExists)) Yii::app()->user->setFlash('error', 'Указанный inn уже используется. ');
                                } else {
                                    $emailExists = User::model()->find(array(
                                        'select'    => 'email',
                                        'condition' => 'email=:email',
                                        'params'    => array(':email'=>$_POST['UserForm']['email']))
                                    );

                                    if(!empty($emailExists)) Yii::app()->user->setFlash('error', 'Указанный email уже используется. ');
                                }
                            }
                        }
                    }

                    if(!empty($innExists) || !empty($emailExists)) {
                        $form->attributes = $_POST['UserForm'];
                    } else {
                        if(!empty($message)) {
                            Changes::saveChange($message);
                            if (!empty($_POST['UserForm']['password_confirm'])) {
                                $model->password = crypt($_POST['UserForm']['password_confirm'], User::model()->blowfishSalt());
                            }

                            if ($model->save()) {
                                Yii::app()->user->setFlash('saved_id', $model->id);
                                Yii::app()->user->setFlash('message', 'Пользователь "' . $model->company . '" сохранен успешно.');
                                $form->attributes = $model->attributes;
                                User::sendAboutChangeStatus($model, $changes);
                            } else Yii::log($model->getErrors(), 'error');
                        }
                    }
                } else {
                    Yii::app()->user->setFlash('error', $warringMessage);
                    $form->attributes = $_POST['UserForm'];
                }
            } 
            $this->render('user/edituser', array('model' => $form, 'contacts' => $contacts), false, true);
        } else {
            throw new CHttpException(403, Yii::t('yii', 'У Вас недостаточно прав доступа.'));
        }
    }
    
    public function actionDeleteUser($id, $status = 5)
    {
        $model = User::model()->findByPk($id);
        if (Yii::app()->user->checkAccess('trDeleteUser')) {
            $name = $model->company;
            if (User::model()->deleteByPk($id)) {
                $message = 'Удален пользователь "' . $name . '"';
                Changes::saveChange($message);
                User::model()->deleteAllByAttributes(array('parent'=>$id));
                Yii::app()->user->setFlash('message', 'Пользователь удален успешно.');
                if($status == 5) $this->redirect('/admin/user/');
                else $this->redirect('/admin/user/index/status/'.$status);
            }
        } else {
            throw new CHttpException(403, Yii::t('yii', 'У Вас недостаточно прав доступа.'));
        }
    }
    
    /* Ajax update user's status */
    public function actionUpdateStatus()
    {
        $id = $_POST['id'];
        $status = $_POST['status'];
        $reason = $_POST['reason'];
        $date = $_POST['date'];
        $message = $messageAboutChanges = '';
        $changes = array();

        if(isset($status)) { // update data
            if($status == User::USER_TEMPORARY_BLOCKED && !empty($date) && strtotime($date) <= strtotime(date('d-m-Y'))) {
                $message = 'date'; //error in date
            } else {
                $user = User::model()->findByPk($id);
                if($user->status != $status){
                    $changes['status']['before'] = User::statusLabel($user->status);
                    $changes['status']['after'] = User::statusLabel($status);
                    $user->status = $status;
                }
                
                if($user->status == User::USER_NOT_CONFIRMED || $user->status == User::USER_ACTIVE){
                    if(!empty($user->reason)){
                        $changes['reason']['before'] = $user->reason;
                        $changes['reason']['after'] = '';
                        $user->reason = null;
                    }
                    if(!empty($user->block_date)){
                        $changes['block_date']['before'] = $user->block_date;
                        $changes['block_date']['after'] = '';
                        $user->block_date = null;
                    }
                    $message = User::statusLabel($status);
                } else {
                    if($user->reason != $reason) {
                        $changes['reason']['before'] = $user->reason;
                        $changes['reason']['after'] = $reason;
                        $user->reason = $reason;
                    }
                    if($user->status == User::USER_TEMPORARY_BLOCKED) {
                        if(date("Y-m-d", strtotime($date)) != date("Y-m-d", strtotime($user->block_date))){
                            $changes['block_date']['before'] = $user->block_date;
                            $user->block_date = date("Y-m-d", strtotime($date));
                            $changes['block_date']['after'] = $user->block_date;
                        }
                    } else {
                        if(!empty($user->block_date)){
                            $changes['block_date']['before'] = $user->block_date;
                            $changes['block_date']['after'] = '';
                        }
                        $user->block_date = null;
                    }
                    $message = User::statusLabel($status);
                }
                $user->save();
                User::sendAboutChangeStatus($user, $changes);
                /* Save in history */
                if(!empty($changes)){
                    $messageAboutChanges = 'У пользователя '.$user->company.' (id='.$id.') были изменены следующеие поля: ';
                    $k = 0;
                    foreach ($changes as $key => $value) {
                        $k++;
                        $messageAboutChanges .= $k . ') Поле "' . $user->getAttributeLabel($key) . '" c "' . $changes[$key]['before'] . '" на "' . $changes[$key]['after'] . '"; ';
                    }
                    Changes::saveChange($messageAboutChanges);
                }
            }
        } else { // view data
            $user = User::model()->findByPk($id);
            $message = $user->reason;
            $date = date("d-m-Y", strtotime($user->block_date));
        }
        
        $array = array('message'=>$message, 'date' => $date);
        echo json_encode($array);
    }
    
    public function actionSearch()
    {
        $query = trim($_GET['q']);
        $result = array();
        if(User::prepareSqlite()) {
            if (!empty($query)){
                $dependency = new CDbCacheDependency('SELECT MAX(created) FROM user');
                $result = Yii::app()->db->cache(1000, $dependency)->createCommand()
                    ->select('id, company, inn, email')
                    ->from('user')
                    ->where('type_contact = 0 and (lower(company) like lower("%'.$query.'%") or lower(email) like lower("%'.$query.'%") or inn like "%'.$query.'%")')
                    ->limit(7)
                    ->queryAll();
            }
        }
        $this->renderPartial('application.modules.admin.views.default.quickAjaxResult', array('data' =>$result));
    }
}
