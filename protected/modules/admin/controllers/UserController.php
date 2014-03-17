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

    //User block
    public function actionIndex() 
    {
        if(Yii::app()->user->checkAccess('readUser'))
        {
            $criteria = new CDbCriteria();
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
                $form = new UserForm;
                $form->attributes = $model->attributes;
                $form->id = $id_item;
                $form->password = '';
                //$group = UserGroup::getUserGroupArray();
                //$view = $this->renderPartial('user/edituser', array('model'=>$model, 'group'=>$group), true, true);
                $view = $this->renderPartial('user/edituser', array('model'=>$form), true, true);
            }
            $this->render('user/users', array('data'=>$dataProvider, 'view'=>$view));
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionCreateUser()
    {
        if(Yii::app()->user->checkAccess('createUser')) {
            $form = new UserForm;
            if(isset($_POST['UserForm'])) {
                $model = new User;
                $model->attributes = $_POST['UserForm'];
                 if($model->save()){
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
                    $newFerrymanFields->save();
                    
                    Yii::app()->user->setFlash('saved_id', $model->id);
                    Yii::app()->user->setFlash('message', 'Пользователь "'.$model->company.'" создан успешно.');
                    $this->redirect('/admin/user/');
                }
            }
            $this->renderPartial('user/edituser', array('model'=>$form), false, true);
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionEditUser($id) 
    {
        $model = User::model()->findByPk($id);   
        $form = new UserForm;
        $form->attributes = $model->attributes;
        $form->id = $id;
        $form->password = '';
        if (Yii::app()->user->checkAccess('editUser')) {
            if (isset($_POST['UserForm'])) {
                $changes = array();
                foreach ($_POST['UserForm'] as $key => $value) {
                    if (trim($model[$key]) != trim($value) && $key != 'password') {
                        $changes[$key]['before'] = $model[$key];
                        $changes[$key]['after'] = $value;
                        $model[$key] = trim($value);
                    } else if($key == 'password' && $model->password !== crypt(trim($_POST['UserForm']['password']), $model->password)) {
                        $model[$key] = crypt($_POST['UserForm']['password'], User::model()->blowfishSalt());
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
                        }else {
                            $message .= $k . ') Поле ' . $key . ' c "' . $changes[$key]['before'] . '" на "' . $changes[$key]['after'] . '"; ';
                        }    
                    }
                    Changes::saveChange($message);
                }
                $model->attributes = $_POST['UserForm'];
                if (isset($_POST['UserForm_password']) && $_POST['UserForm_password']!='') {
                    $model->password = crypt($_POST['UserForm_password'], User::model()->blowfishSalt());
                }
                if ($model->save()) {
                    Yii::app()->user->setFlash('saved_id', $model->id);
                    Yii::app()->user->setFlash('message', 'Пользователь "' . $model->company . '" сохранен успешно.');
                    $this->redirect('/admin/user/');
                }
            }
           // $this->renderPartial('user/edituser', array('model' => $model, 'status' => self::$userStatus), false, true);
            $this->renderPartial('user/edituser', array('model' => $form), false, true);
        } else {
            throw new CHttpException(403, Yii::t('yii', 'У Вас недостаточно прав доступа.'));
        }
    }

    public function actionDeleteUser($id) 
    {
        $model = User::model()->findByPk($id);
        if (Yii::app()->user->checkAccess('deleteUser')) {
            if (User::model()->deleteByPk($id)) {
                $message = 'Удален пользователь ' . $model['name'] . ' ' . $model['surname'];
                Changes::saveChange($message);
                Yii::app()->user->setFlash('message', 'Пользователь удален успешно.');
                $this->redirect('/admin/user/');
            }
        } else {
            throw new CHttpException(403, Yii::t('yii', 'У Вас недостаточно прав доступа.'));
        }
    }
}
