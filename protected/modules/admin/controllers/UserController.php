<?php

class UserController extends Controller
{
    /* @const */
    private static $userStatus = array(
         User::USER_NOT_CONFIRMED => 'Не подтвежден',
         '1' => 'Активен',
         '2' => 'Предупрежден',
         User::USER_TEMPORARY_BLOCKED => 'Временно заблокирован',
         User::USER_BLOCKED => 'Заблокирован',
    );
    protected function beforeAction($action)
    {
        if(parent::beforeAction($action))
        {
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
            $sort->attributes = array(
                            'group_id'=>array(
                                'group_id'=>'Группа',
                                'asc'=>'group_id ASC',
                                'desc'=>'group_id DESC',
                                'default'=>'asc',
                            ),
                            'surname'=>array(
                                'surname'=>'Фамилии',
                                'asc'=>'surname ASC',
                                'desc'=>'surname DESC',
                                'default'=>'asc',
                            ),
                            'name'=>array(
                                'surname'=>'Имя',
                                'asc'=>'name ASC',
                                'desc'=>'name DESC',
                                'default'=>'asc',
                            )
                        );
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
                $view = $this->renderPartial('user/edituser', array('model'=>$model, 'group'=>$group, 'status' => self::$userStatus), true, true);
            }
            $this->render('user/users', array('data'=>$dataProvider, 'view'=>$view));
        }else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
    public function actionCreateUser()
    {
        if(Yii::app()->user->checkAccess('createUser'))
        {
            $model = new User();
            $group = UserGroup::getUserGroupArray();
            if (isset($_POST['User'])){
                $model->attributes = $_POST['User'];
                $model->status = 1;
                //var_dump($_POST['User']);exit;
                if($model->save()){
                    if(Yii::app()->params['ferrymanGroup'] == $model->group_id){
                        $modelUserField = new UserField;
                        $data = array('mail_deadline' => true, 'site_transport_create_1' => true, 'site_transport_create_2' => true, 'site_kill_rate' => true, 'site_deadline' => true, 'site_before_deadline' => true);
                        $modelUserField->attributes = $data;
                        $modelUserField->user_id = Yii::app()->user->_id;
                        $modelUserField->save();
                    }

                    Yii::app()->user->setFlash('saved_id', $model->id);
                    Yii::app()->user->setFlash('message', 'Пользователь '.$model->login.' создан успешно.');
                    $this->redirect('/admin/user/');
                }
            }
            $this->renderPartial('user/edituser', array('model'=>$model, 'group'=>$group, 'status'=>self::$userStatus), false, true);
        }else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
    public function actionEditUser($id)
    {
        $model = User::model()->findByPk($id);
        $params = array('group'=>$model->group_id, 'userid'=>$id);
        if(Yii::app()->user->checkAccess('editUser', $params))
        {
            $group = UserGroup::getUserGroupArray();
            if (isset($_POST['User'])){
                $model->attributes = $_POST['User'];
                //$model['status'] = $_POST['User']['status'];
                
                if($model->save()){
                    Yii::app()->user->setFlash('saved_id', $model->id);
                    Yii::app()->user->setFlash('message', 'Пользователь '.$model->login.' сохранен успешно.');
                    $this->redirect('/admin/user/');
                }
            }
            $this->renderPartial('user/edituser', array('model'=>$model, 'group'=>$group, 'status' => self::$userStatus), false, true);
        }else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
    public function actionDeleteUser($id)
    {
        $model = User::model()->findByPk($id);
        $params = array('group'=>$model->group_id, 'userid'=>$id);
        if(Yii::app()->user->checkAccess('deleteUser', $params) && $id != Yii::app()->user->_id)
        {
            if(User::model()->deleteByPk($id)){
                Yii::app()->user->setFlash('message', 'Пользователь удален успешно.');
                $this->redirect('/admin/user/');
            }
        }else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    //Group block
    public function actionGroup()
    {
        if(Yii::app()->user->checkAccess('readUserGroup'))
        {
            $criteria = new CDbCriteria();
            $sort = new CSort();
            $sort->sortVar = 'sort';
            // сортировка по умолчанию
            $sort->defaultOrder = 'level ASC';
            $sort->attributes = array(
                            'id'=>array(
                                'id'=>'ID',
                                'asc'=>'id ASC',
                                'desc'=>'id DESC',
                                'default'=>'desc',
                            ),
                            'name'=>array(
                                'name'=>'Названию',
                                'asc'=>'name ASC',
                                'desc'=>'name DESC',
                                'default'=>'desc',
                            ),
                        );
            $dataProvider = new CActiveDataProvider('UserGroup',
                    array(
                        'criteria'=>$criteria,
                        'sort'=>$sort,
                        'pagination'=>false
                    )
            );
            $this->render('group/groups', array('data'=>$dataProvider, 'view'=>$view));
        }else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
    public function actionCreateGroup()
    {
        if(Yii::app()->user->checkAccess('createUserGroup'))
        {
            $model = new UserGroup();
            $role = AuthItem::model()->findAll('type=2');
            if (isset($_POST['UserGroup'])){
                $model->attributes = $_POST['UserGroup'];
                if($model->save()){
                    Yii::app()->user->setFlash('message', 'Группа '.$model->name.' создана успешно.');
                    $this->redirect('/admin/user/group/');
                }
            }
            $this->renderPartial('group/editgroup', array('model'=>$model, 'role'=>$role), false, true);
        }else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
    public function actionEditGroup($id)
    {
        $model = UserGroup::model()->findByPk($id);
        if(Yii::app()->user->checkAccess('editUserGroup', array('level'=>$model->level)))
        {
            $role = AuthItem::model()->findAll('type=2');
            $checkbox = AuthAssignment::model()->findAll('userid='.$id);
            if (isset($_POST['UserGroup'])){
                $_POST['UserGroup']['bizrule'] = 'return Yii::app()->user->getState("level")<$params["level"];';
                $model->attributes = $_POST['UserGroup'];
                if($model->save()){
                    Yii::app()->user->setFlash('message', 'Группа '.$model->name.' сохранена успешно.');
                    $this->redirect('/admin/user/group/');
                }
            }
            $this->renderPartial('group/editgroup', array('model'=>$model, 'role'=>$role,'checkbox'=>$checkbox), false, true);
        }else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
    public function actionDeleteGroup($id)
    {
        $model = UserGroup::model()->findByPk($id);
        if(Yii::app()->user->checkAccess('deleteUserGroup', array('level'=>$model->level)))
        {
            Yii::app()->user->setFlash('message', 'Группа удалена успешно.');
            UserGroup::model()->deleteByPk($id);
            $this->redirect('/admin/user/group/');
        }else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    // Roles block
    public function actionRole()
    {
        if(Yii::app()->user->checkAccess('readRole'))
        {
            $criteria = new CDbCriteria();
            $criteria->addCondition('type=2');
            $sort = new CSort();
            $sort->sortVar = 'sort';
            // сортировка по умолчанию
            $sort->defaultOrder = 'name ASC';
            $sort->attributes = array(
                            'name'=>array(
                                'name'=>'Названию',
                                'asc'=>'name ASC',
                                'desc'=>'name DESC',
                                'default'=>'desc',
                            ),
                        );
            $dataProvider = new CActiveDataProvider('AuthItem',
                    array(
                        'criteria'=>$criteria,
                        'sort'=>$sort,
                        'pagination'=>false
                    )
            );
            if ($id_item = Yii::app()->user->getFlash('saved_id')){
                $model = AuthItem::model()->findByPk($id_item);
                $operation = AuthItem::model()->findAll('type=0');
                $view = $this->renderPartial('role/editrole', array('model'=>$model, 'operation'=>$operation), true, true);
            }
            $this->render('role/roles', array('data'=>$dataProvider, 'view'=>$view));
        }else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
    public function actionCreateRole()
    {
        if(Yii::app()->user->isRoot)
        {
            $model = new AuthItem();
            $operation = AuthItem::model()->findAll('type=0');
            if (isset($_POST['AuthItem'])){
                $_POST['AuthItem']['type'] = 2;
                $model->attributes = $_POST['AuthItem'];
                if($model->save()){
                    Yii::app()->user->setFlash('saved_id', $model->name);
                    Yii::app()->user->setFlash('message', 'Роль '.$model->name.' создана успешно.');
                    $this->redirect('/admin/user/role/');
                }
            }
            $this->renderPartial('role/editrole', array('model'=>$model, 'operation'=>$operation), false, true);
        }else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
    public function actionEditRole($name)
    {
        if(Yii::app()->user->isRoot)
        {
            $model = AuthItem::model()->findByPk($name);
            $operation = AuthItem::model()->findAll('type=0');
            if (isset($_POST['AuthItem'])){

                //echo '<pre>';
                //var_dump($_POST['AuthItem']); exit;

                $model->attributes = $_POST['AuthItem'];
                if($model->save()){
                    Yii::app()->user->setFlash('saved_id', $model->name);
                    Yii::app()->user->setFlash('message', 'Роль '.$model->name.' сохранена успешно.');
                    $this->redirect('/admin/user/role/');
                }
            }
            $this->renderPartial('role/editrole', array('model'=>$model, 'operation'=>$operation), false, true);
        }else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
    public function actionDeleteRole($name)
    {
        if(Yii::app()->user->isRoot)
        {
            Yii::app()->authManager->removeAuthItem($name);
            Yii::app()->user->setFlash('message', 'Роль удалена успешно.');
            $this->redirect('/admin/user/role/');
        }else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    // Operation block
    public function actionOperation()
    {
        if(Yii::app()->user->checkAccess('readOperation'))
        {
            $criteria = new CDbCriteria();
            $criteria->addCondition('type=0');
            $sort = new CSort();
            $sort->sortVar = 'sort';
            // сортировка по умолчанию
            $sort->defaultOrder = 'name ASC';
            $sort->attributes = array(
                            'name'=>array(
                                'name'=>'Названию',
                                'asc'=>'name ASC',
                                'desc'=>'name DESC',
                                'default'=>'desc',
                            ),
                        );
            $dataProvider = new CActiveDataProvider('AuthItem',
                    array(
                        'criteria'=>$criteria,
                        'sort'=>$sort,
                        'pagination'=>array(
                            'pageSize'=>'13'
                        )
                    )
            );
            if ($id_item = Yii::app()->user->getFlash('saved_id')){
                $model = AuthItem::model()->findByPk($id_item);
                $view = $this->renderPartial('operation/editoperation', array('model'=>$model), true, true);
            }
            $this->render('operation/operations', array('data'=>$dataProvider, 'view'=>$view));
        }else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
    public function actionCreateOperation()
    {
        if(Yii::app()->user->isRoot)
        {
            $model = new AuthItem();
            if (isset($_POST['AuthItem'])){
                $_POST['AuthItem']['type'] = 0;
                $model->attributes = $_POST['AuthItem'];
                if($model->save()){
                    Yii::app()->user->setFlash('saved_id', $model->name);
                    Yii::app()->user->setFlash('message', 'Операция '.$model->name.' создана успешно.');
                    $this->redirect('/admin/user/operation/');
                }
            }
            $this->renderPartial('operation/editoperation', array('model'=>$model), false, true);
        }else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
    public function actionEditOperation($name)
    {
        if(Yii::app()->user->isRoot)
        {
            $model = AuthItem::model()->findByPk($name);
            if (isset($_POST['AuthItem'])){
                $model->attributes = $_POST['AuthItem'];
                if($model->save()){
                    Yii::app()->user->setFlash('saved_id', $model->name);
                    Yii::app()->user->setFlash('message', 'Операция '.$model->name.' сохранена успешно.');
                    $this->redirect('/admin/user/operation/');
                }
            }
            $this->renderPartial('operation/editoperation', array('model'=>$model), false, true);
        }else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
    public function actionDeleteOperation($name)
    {
        if(Yii::app()->user->isRoot)
        {
            Yii::app()->authManager->removeAuthItem($name);
            Yii::app()->user->setFlash('message', 'Операция удалена успешно.');
            $this->redirect('/admin/user/operation/');
        }else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
}