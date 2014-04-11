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

    public function actionIndex() 
    {
        if(Yii::app()->user->checkAccess('trReadUser'))
        {
            $criteria = new CDbCriteria();
            $criteria->condition = 'type_contact = 0';
            $sort = new CSort();
            $sort->sortVar = 'sort';
            // сортировка по умолчанию 
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
            if ($id_item = Yii::app()->user->getFlash('saved_id')){
                $model = User::model()->findByPk($id_item);
                $form  = new UserForm;
                $form->attributes = $model->attributes;
                $form->id = $id_item;
                $view = $this->renderPartial('user/edituser', array('model'=>$form), true, true);
            }
            $this->render('user/users', array('data'=>$dataProvider, 'view'=>$view));
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionCreateUser()
    {
        if(Yii::app()->user->checkAccess('trCreateUser')) {
            $form = new UserForm;
            if(isset($_POST['UserForm'])) {
                $emailExists = User::model()->find(array(
                    'select'    => 'email',
                    'condition' => 'email=:email',
                    'params'    => array(':email'=>$_POST['UserForm']['email']))
                );
                
                if(empty($emailExists)){
                    $emailExists = UserContact::model()->find(array(
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
                        $this->redirect('/admin/user/');
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
                    $view = $this->renderPartial('user/edituser', array('model'=>$form), true, true);
                    $this->render('user/users', array('data'=>$dataProvider, 'view'=>$view));
               }
            } else $this->renderPartial('user/edituser', array('model'=>$form), false, true);
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionEditUser($id) 
    {
        $model = User::model()->findByPk($id);   
        $message = '';
        $form = new UserForm;
        //$form->attributes = $model->attributes;
        
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
        
        $form->id = $id;
        if (Yii::app()->user->checkAccess('trEditUser')) {
            if (isset($_POST['UserForm'])) {
                $changes = $innExists = $emailExists = array();
                foreach ($_POST['UserForm'] as $key => $value) {
                    if($key != 'show'){
                        if (trim($model[$key]) != trim($value) && $key != 'password') {
                            $changes[$key]['before'] = $model[$key];
                            $changes[$key]['after'] = $value;
                            $model[$key] = trim($value);
                        } else if($key == 'password' && !empty($_POST['UserForm']['password_confirm'])) {
                            $changes[$key] = 'Изменен пароль';
                        }
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
                                
                                if(empty($emailExists)) {
                                    $emailExists = UserContact::model()->find(array(
                                        'select'    => 'email',
                                        'condition' => 'email=:email',
                                        'params'    => array(':email'=>$_POST['UserForm']['email']))
                                    );
                                }
                                
                                if(!empty($emailExists)) Yii::app()->user->setFlash('error', 'Указанный email уже используется. ');
                            }
                        }
                    }
                }
                
                if(!empty($innExists) || !empty($emailExists)) {
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
                    $view = $this->renderPartial('user/edituser', array('model'=>$form), true, true);
                    $this->render('user/users', array('data'=>$dataProvider, 'view'=>$view));
                } else {
                    if(!empty($message)) {
                        Changes::saveChange($message);
                
                        $model->attributes = $_POST['UserForm'];
                        if (!empty($_POST['UserForm']['password_confirm'])) {
                            $model->password = crypt($_POST['UserForm']['password_confirm'], User::model()->blowfishSalt());
                        }
                    }
                    if ($model->save()) {
                        Yii::app()->user->setFlash('saved_id', $model->id);
                        Yii::app()->user->setFlash('message', 'Пользователь "' . $model->company . '" сохранен успешно.');
                        $this->redirect('/admin/user/');
                    } else Yii::log($model->getErrors(), 'error');
                }
            } else $this->renderPartial('user/edituser', array('model' => $form), false, true);
        } else {
            throw new CHttpException(403, Yii::t('yii', 'У Вас недостаточно прав доступа.'));
        }
    }

    public function actionDeleteUser($id)
    {
        $model = User::model()->findByPk($id);
        if (Yii::app()->user->checkAccess('trDeleteUser')) {
            $name = $model->company;
            if (User::model()->deleteByPk($id)) {
                $message = 'Удален пользователь "' . $name . '"';
                Changes::saveChange($message);
                User::model()->deleteAllByAttributes(array('parent'=>$id));
                Yii::app()->user->setFlash('message', 'Пользователь удален успешно.');
                $this->redirect('/admin/user/');
            }
        } else {
            throw new CHttpException(403, Yii::t('yii', 'У Вас недостаточно прав доступа.'));
        }
    }
}
