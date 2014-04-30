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

    public function actionIndex($status = 5) 
    {
        if(Yii::app()->user->checkAccess('trReadUser'))
        {
            $criteria = new CDbCriteria();
            $criteria->condition = 'type_contact = 0';
            
            if($status != 5) {
                $criteria->condition = 't.status = :status';
                $criteria->params = array(':status' => $status);
            }
            
            $sort = new CSort();
            $sort->sortVar = 'sort';
            // сортировка по умолчанию 
            $sort->defaultOrder = 'company ASC';
            $dataProvider = new CActiveDataProvider('User', 
                array(
                    'criteria'=>$criteria,
                    'sort'=>$sort,
                    'pagination'=>array(
                        'pageSize'=>'10'
                    )
                )
            );
            if ($id_item = Yii::app()->user->getFlash('saved_id')){
                $model = User::model()->findByPk($id_item);
                $form  = new UserForm;
                $form->attributes = $model->attributes;
                
                $form->country = $model->country;
                $form->company = $model->company;
                $form->country = $model->country;
                $form->password = $model->password;
                $form->region = $model->region;
                $form->district = $model->district;
                $form->inn = $model->inn;
                $form->name = $model->name;
                $form->surname = $model->surname;
                $form->phone = $model->phone;
                $form->email = $model->email;
                $form->status = $model->status;
                
                $form->id = $id_item;
                $view = $this->renderPartial('user/edituser', array('model'=>$form), true, true);
            }
            //echo '<pre>';var_dump($dataProvider);exit;
            $this->render('user/user', array('data'=>$dataProvider, 'view'=>$view));
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
                            $value = date('Y-m-d', strtotime($value));
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
                        $message = 'У пользователя с id = ' . $id . ' были изменены слудующие поля: ';
                        $k = 0;
                        foreach ($changes as $key => $value) {
                            $k++;
                            if($key == 'password'){
                                $message .= $k . ') ' . $changes[$key];    
                            } else {
                                $message .= $k . ') Поле ' . $key . ' c "' . $changes[$key]['before'] . '" на "' . $changes[$key]['after'] . '"; ';
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
        $message = '';

        if(isset($status)) { // update data
            if($status == User::USER_TEMPORARY_BLOCKED && !empty($date) && strtotime($date) <= strtotime(date('d-m-Y'))) {
                $message = 'date'; //error in date
            } else {
                $changes = array('status'=>'1');
                $user = User::model()->findByPk($id);
                $user->status = $status;
                User::sendAboutChangeStatus($user, $changes);
                
                if($user->status == User::USER_NOT_CONFIRMED || $user->status == User::USER_ACTIVE){
                    $user->reason = null;
                    $user->block_date = null;
                    $message = User::statusLabel($status);
                } else {
                    $user->reason = $reason;
                    $message = User::statusLabel($status);
                    if($user->status == User::USER_TEMPORARY_BLOCKED) $user->block_date = date("Y-m-d", strtotime($date));
                    else $user->block_date = null;
                }
                $user->save();
            }
        } else { // view data
            $user = User::model()->findByPk($id);
            $message = $user->reason;
            $date = date("d-m-Y", strtotime($user->block_date));
        }
        
        $array = array('message'=>$message, 'date' => $date);
        echo json_encode($array);
    }
    
    public function actionSearch($input)
    {
        $criteria = new CDbCriteria();
        //$criteria->condition = 'type_contact = 0 AND company like :input COLLATE cp1251_general_ci';
        //$criteria->condition = 'company like :input';
        //$criteria->params = array(':input' => '%' . trim($input) . '%');
        $str = "Крылова";
        $str = mb_strtoupper($str, 'UTF-8');
        $criteria->condition = "UPPER(company) like '%".$str."%'";
        //echo '<pre>';
        //var_dump($criteria);exit;
        $sort = new CSort();
        $sort->sortVar = 'sort';
        $sort->defaultOrder = 'company ASC';
        $dependecy = new CDbCacheDependency('SELECT MAX(created) FROM user');
        $dataProvider = new CActiveDataProvider(User::model()->cache(1000, $dependecy, 2), array ( 
            'criteria'=>$criteria,
            'sort'=>$sort,
            'pagination' => array ( 
                'pageSize' => 10, 
            ) 
        ));
        //echo '<pre>';
        //var_dump($dataProvider);exit;
        $this->render('user/user', array('data'=>$dataProvider));
    }
}
