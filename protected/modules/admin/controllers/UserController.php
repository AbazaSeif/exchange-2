<?php

class UserController extends Controller {
    /* @const */

    private static $userStatus = array(
        User::USER_NOT_CONFIRMED => 'Не подтвежден',
        '1' => 'Активен',
        '2' => 'Предупрежден',
        User::USER_TEMPORARY_BLOCKED => 'Временно заблокирован',
        User::USER_BLOCKED => 'Заблокирован',
    );

    protected function beforeAction($action) {
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
                    $group = UserGroup::getUserGroupArray();
                    $view = $this->renderPartial('user/edituser', array('model'=>$model, 'group'=>$group), true, true);
                }
                $this->render('user/users', array('data'=>$dataProvider, 'view'=>$view));
            }else{
                throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
            }
    }

    public function actionCreateUser() 
    {
        /*if (Yii::app()->user->checkAccess('createUser')) {
            $model = new User();
            $group = UserGroup::getUserGroupArray();
            if (isset($_POST['User'])) {
                $model->attributes = $_POST['User'];
                $model->status = 1;

                
                if ($model->save()) {
                    if (Yii::app()->params['ferrymanGroup'] == $model->group_id) {
                        $modelUserField = new UserField;
                        $data = array('mail_deadline' => true, 'site_transport_create_1' => true, 'site_transport_create_2' => true, 'site_kill_rate' => true, 'site_deadline' => true, 'site_before_deadline' => true);
                        $modelUserField->attributes = $data;
                        $modelUserField->user_id = Yii::app()->user->_id;
                        $modelUserField->save();
                    }

                    Yii::app()->user->setFlash('saved_id', $model->id);
                    Yii::app()->user->setFlash('message', 'Пользователь ' . $model->login . ' создан успешно.');
                    
                    Changes::saveChange('Создан пользователь ' . $_POST['User']['name'] . ' ' . $_POST['User']['surname']);
                    
                    $this->redirect('/admin/user/');
                }
            }
            $this->renderPartial('user/edituser', array('model' => $model, 'group' => $group, 'status' => self::$userStatus), false, true);
        } else {
            throw new CHttpException(403, Yii::t('yii', 'У Вас недостаточно прав доступа.'));
        }*/
        
            /*if(Yii::app()->user->checkAccess('createUser'))
            {
                $model = new User();
                $group = UserGroup::getUserGroupArray();
                if (isset($_POST['User'])){
                    $model->attributes = $_POST['User'];
                    if($model->save()){
                        Yii::app()->user->setFlash('saved_id', $model->id);
                        Yii::app()->user->setFlash('message', 'Пользователь '.$model->login.' создан успешно.');
                        $this->redirect('/admin/user/');
                    }
                }
                $this->renderPartial('user/edituser', array('model'=>$model, 'group'=>$group, 'status' => self::$userStatus), false, true);
            }else{
                throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
            }
            */
            /**********/
            if(Yii::app()->user->checkAccess('createUser'))
            {
                $model = new User();
                $group = UserGroup::getUserGroupArray();
                if (isset($_POST['User'])){
                    $model->attributes = $_POST['User'];
                    if($model->save()){
                        Yii::app()->user->setFlash('saved_id', $model->id);
                        Yii::app()->user->setFlash('message', 'Пользователь '.$model->login.' создан успешно.');
                        $this->redirect('/admin/user/');
                    }
                }
                $this->renderPartial('user/edituser', array('model'=>$model, 'group'=>$group), false, true);
            }else{
                throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
            }
    }

    public function actionEditUser($id) 
    {
       
        $model = User::model()->findByPk($id);
        
        if (Yii::app()->user->checkAccess('editUser')) {
            
            if (isset($_POST['User'])) {
                $changes = array();
                 //echo 112;exit;
                foreach ($_POST['User'] as $key => $value) {
                    if (trim($model[$key]) != trim($value)) {
                        $changes[$key]['before'] = $model[$key];
                        $changes[$key]['after'] = $value;
                        $model[$key] = trim($value);
                    }
                }
                if (!empty($changes)) {
                    $message = 'У пользователя с id = ' . $id . ' были изменены слудующие поля: ';
                    $k = 0;
                    foreach ($changes as $key => $value) {
                        $k++;
                        $message .= $k . ') Поле ' . $key . ' c ' . $changes[$key]['before'] . ' на ' . $changes[$key]['after'] . '; ';
                    }
                    Changes::saveChange($message);
                }
                //$model->attributes = $_POST['User'];
                if ($model->save()) {
                    Yii::app()->user->setFlash('saved_id', $model->id);
                    Yii::app()->user->setFlash('message', 'Пользователь ' . $model->login . ' сохранен успешно.');
                    $this->redirect('/admin/user/');
                }
            }
            $this->renderPartial('user/edituser', array('model' => $model, 'status' => self::$userStatus), false, true);
        } else {
            throw new CHttpException(403, Yii::t('yii', 'У Вас недостаточно прав доступа.'));
        }
    }

    public function actionDeleteUser($id) {
        $model = User::model()->findByPk($id);
        
        if (Yii::app()->user->checkAccess('deleteUser') && $id != Yii::app()->user->_id) {
            if (User::model()->deleteByPk($id)) {
                Changes::saveChange('Удален пользователь ' . $model['name'] . ' ' . $model['surname']);
                Yii::app()->user->setFlash('message', 'Пользователь удален успешно.');
                $this->redirect('/admin/user/');
            }
        } else {
            throw new CHttpException(403, Yii::t('yii', 'У Вас недостаточно прав доступа.'));
        }
    }
}
