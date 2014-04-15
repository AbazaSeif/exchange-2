<?php

class ContactController extends Controller 
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
        if(Yii::app()->user->checkAccess('trReadUserContact')) {
            $criteria = new CDbCriteria();
            $criteria->condition = 'type_contact = 1';
            
            if($status != 5) {
                $criteria->condition = 't.status = :status';
                $criteria->params = array(':status' => $status);
            }
            
            $sort = new CSort();
            $sort->sortVar = 'sort';
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

            if ($id_item = Yii::app()->user->getFlash('saved_id')) {
                $model = User::model()->findByPk($id_item);
                $form = new UserContactForm;
                $form->attributes = $model->attributes;
                $form->company = $model->company;
                $form->id = $id_item;
                $form->parent = $model->parent;
                
                $view = $this->renderPartial('editcontact', array('model'=>$form), true, true);
            }
            $this->render('contact', array('data'=>$dataProvider, 'view'=>$view));
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionCreateContact()
    {
        if(Yii::app()->user->checkAccess('trCreateUserContact')) {
            $form = new UserContactForm;
            if(isset($_POST['UserContactForm'])) {
                $emailExists=User::model()->find(array(
                    'select'=>'email',
                    'condition'=>'email=:email',
                    'params'=>array(':email'=>$_POST['UserContactForm']['email']))
                );
                if(empty($emailExists)) {
                    $curUser = User::model()->findByPk($_POST['UserContactForm']['parent']);
                    $model = new User;
                    $model->attributes = $_POST['UserContactForm'];
                    $model->password = crypt($_POST['UserContactForm']['password'], User::model()->blowfishSalt());
                    $model->type_contact = 1;
                    $model->company = 'Контактное лицо "' . $curUser->company . '" ('.$model->name.' '.$model->surname.')';
                    if($model->save()) {
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

                        $message = 'Создан контакт ' . $model->name . ' ' . $model->surname;
                        Changes::saveChange($message);

                        Yii::app()->user->setFlash('saved_id', $model->id);
                        Yii::app()->user->setFlash('message', 'Контакт "'.$model->surname.' '.$model->name.'" создан успешно.');
                        $form->attributes = $model->attributes;
                    } else Yii::log($model->getErrors(), 'error');
                } else {
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
                    
                    $form->attributes = $_POST['UserContactForm'];
                    Yii::app()->user->setFlash('error', 'Указанный email уже используется. ');
                }
            } 
            $this->render('editcontact', array('model'=>$form), false, true);
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionEditContact($id)
    {
        $model = User::model()->findByPk($id);
        $message = '';
        $form = new UserContactForm;
        $form->attributes = $model->attributes;
        $form->company = $model->company;
        $form->id = $id;
        $form->parent = $model->parent;
        
        if (Yii::app()->user->checkAccess('trEditUserContact')) {
            if (isset($_POST['UserContactForm'])) {
                $curUser = User::model()->findByPk($_POST['UserContactForm']['parent']);
                $changes = $emailExists = array();
                if($_POST['UserContactForm']['status'] == User::USER_NOT_CONFIRMED || $_POST['UserContactForm']['status'] == User::USER_ACTIVE){
                    $_POST['UserContactForm']['reason'] = null;
                }
                foreach ($_POST['UserContactForm'] as $key => $value) {
                    if (trim($model[$key]) != trim($value) && $key != 'password' && $key != 'password_confirm') {
                        $changes[$key]['before'] = $model[$key];
                        $changes[$key]['after'] = $value;
                        $model[$key] = trim($value);
                    } else if($key == 'password' && !empty($_POST['UserContactForm']['password_confirm']) && $model->password !== crypt(trim($_POST['UserContactForm']['password_confirm']), $model->password)) {
                        $changes[$key] = 'Изменен пароль';
                    }
                }
                
                $model->attributes = $_POST['UserContactForm'];
                
                $contactName = $model->name;
                if(!empty($model->surname)) $contactName .= ' '.$model->surname;
                $model->company = 'Контактное лицо "' . $curUser->company . '" ('.$contactName.')';
                
                if(!empty($_POST['UserContactForm']['password_confirm'])){
                    $model->password = crypt($_POST['UserContactForm']['password_confirm'], User::model()->blowfishSalt());
                }
                if (!empty($changes)) {
                    $message = 'У контактного лица с id = ' . $id . ' были изменены слудующие поля: ';
                    $k = 0;
                    foreach ($changes as $key => $value) {
                        $k++;
                        if($key == 'password'){
                            $message .= $k . ') ' . $changes[$key];    
                        }else {
                            $message .= $k . ') Поле ' . $key . ' c ' . $changes[$key]['before'] . ' на ' . $changes[$key]['after'] . '; ';
                        }
                        
                        if($key == 'email') {
                            $emailExists = User::model()->find(array(
                                'select'    => 'email',
                                'condition' => 'email=:email',
                                'params'    => array(':email'=>$_POST['UserContactForm']['email']))
                            );

                            if(!empty($emailExists)) Yii::app()->user->setFlash('error', 'Указанный email уже используется. '); 
                        }
                    }
                }
                
                if(!empty($emailExists)) {
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

                    $form->attributes = $_POST['UserContactForm'];
                    $view = $this->renderPartial('editcontact', array('model'=>$form), true, true);
                    $this->render('contact', array('data'=>$dataProvider, 'view'=>$view));
                } else {
                    if(!empty($message)) {
                        Changes::saveChange($message);
                        if (!empty($_POST['UserContactForm']['password_confirm'])) {
                            $model->password = crypt($_POST['UserContactForm']['password_confirm'], User::model()->blowfishSalt());
                        }
                    }
                    
                    if ($model->save()) {
                        Yii::app()->user->setFlash('saved_id', $model->id);
                        Yii::app()->user->setFlash('message', 'Контактное лицо "' . $model->surname . ' ' . $model->name . '" сохранено успешно.');
                        //$this->redirect('/admin/contact/');
                        $form->attributes = $_POST['UserContactForm'];
                    } else Yii::log($model->getErrors(), 'error');
                }
            } //else {
                //$this->renderPartial('editcontact', array('model' => $form), false, true);
            //}
            $this->render('editcontact', array('model' => $form), false, true);
        } else {
            throw new CHttpException(403, Yii::t('yii', 'У Вас недостаточно прав доступа.'));
        }
    }

    public function actionDeleteContact($id)
    {
        $model = User::model()->findByPk($id);
        $name = $model['name'] . ' ' . $model['surname'];
        
        if (Yii::app()->user->checkAccess('trDeleteUserContact')) {
            if (User::model()->deleteByPk($id)) {
                $message = 'Удален контакт ' . $name;
                Changes::saveChange($message);
                Yii::app()->user->setFlash('message', 'Контактное лицо удалено успешно.');
                $this->redirect('/admin/contact/');
            }
        } else {
            throw new CHttpException(403, Yii::t('yii', 'У Вас недостаточно прав доступа.'));
        }
    }
    
    public function getCompanies()
    {
        return $allCompanies = Yii::app()->db->createCommand()
            ->select('id, company')
            ->from('user')
            ->where('type_contact = 0')
            ->order('company')
            ->queryAll()
        ;
    }
}
