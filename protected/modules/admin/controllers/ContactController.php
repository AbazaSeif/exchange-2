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

    public function actionIndex()
    {
        if(Yii::app()->user->checkAccess('trReadUserContact')) {
            $criteria = new CDbCriteria();
            $sort = new CSort();
            $sort->sortVar = 'sort';
            // сортировка по умолчанию 
            $sort->defaultOrder = 'surname ASC';
            $dataProvider = new CActiveDataProvider('UserContact', 
                array(
                    'criteria'=>$criteria,
                    'sort'=>$sort,
                    'pagination'=>array(
                        'pageSize'=>'13'
                    )
                )
            );
            if ($id_item = Yii::app()->user->getFlash('saved_id')) {
                $model = UserContact::model()->findByPk($id_item);
                $form = new UserContactForm;
                $form->attributes = $model->attributes;
                $form->id = $id_item;
                $view = $this->renderPartial('editcontact', array('model'=>$form), true, true);
            }
            $this->render('contacts', array('data'=>$dataProvider, 'view'=>$view));
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionCreateContact()
    {
        if(Yii::app()->user->checkAccess('trCreateUserContact')) {
            $form = new UserContactForm;
            if(isset($_POST['UserContactForm'])) {
                $emailExists=UserContact::model()->find(array(
                    'select'=>'email',
                    'condition'=>'email=:email',
                    'params'=>array(':email'=>$_POST['UserContactForm']['email']))
                );
                
                if(empty($emailExists)){
                    $emailExists=User::model()->find(array(
                        'select'=>'email',
                        'condition'=>'email=:email',
                        'params'=>array(':email'=>$_POST['UserContactForm']['email']))
                    );
                }
                
                if(empty($emailExists)){
                    $model = new UserContact;
                    $model->attributes = $_POST['UserContactForm'];
                    $model->password = crypt($_POST['UserContactForm']['password'], User::model()->blowfishSalt());

                    if($model->save()) {
                        $model->c_id = 'contact_' . $model->id;
                        $model->save();

                        $newFerrymanFields = new UserFieldContact;
                        $newFerrymanFields->user_id = $model->id;
                        $newFerrymanFields->mail_transport_create_1 = false;
                        $newFerrymanFields->mail_transport_create_2 = false;
                        $newFerrymanFields->mail_kill_rate = false;
                        $newFerrymanFields->mail_before_deadline = false;
                        $newFerrymanFields->mail_deadline = true;
                        $newFerrymanFields->with_nds = false;         
                        $newFerrymanFields->save();

                        $message = 'Создан контакт ' . $model->name . ' ' . $model->surname;
                        Changes::saveChange($message);

                        Yii::app()->user->setFlash('saved_id', $model->id);
                        Yii::app()->user->setFlash('message', 'Контакт "'.$model->surname.' '.$model->name.'" создан успешно.');
                        $this->redirect('/admin/contact/');
                    }
                } else {
                    $criteria = new CDbCriteria();
                    $sort = new CSort();
                    $sort->sortVar = 'sort';
                    $sort->defaultOrder = 'surname ASC';
                    $dataProvider = new CActiveDataProvider('UserContact', 
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
                    
                    Yii::app()->user->setFlash('error', 'Указанный email уже используется. ');
                    $this->render('contacts', array('data'=>$dataProvider, 'view'=>$view));
                }
            } else $this->renderPartial('editcontact', array('model'=>$form), false, true);
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionEditContact($id) 
    {
        $model = UserContact::model()->findByPk($id);  
        $message = '';
        $form = new UserContactForm;
        $form->attributes = $model->attributes;
        $form->id = $id;
        if (Yii::app()->user->checkAccess('trEditUserContact')) {
            if (isset($_POST['UserContactForm'])) {
                $changes = $emailExists = array();
                foreach ($_POST['UserContactForm'] as $key => $value) {
                    if (trim($model[$key]) != trim($value) && $key != 'password') {
                        $changes[$key]['before'] = $model[$key];
                        $changes[$key]['after'] = $value;
                        $model[$key] = trim($value);
                    } else if($key == 'password' && $model->password !== crypt(trim($_POST['UserContactForm']['password_confirm']), $model->password)) {
                        $changes[$key] = 'Изменен пароль';
                    }
                }
                
                $model->attributes = $_POST['UserContact'];
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

                            if(empty($emailExists)) {
                                $emailExists = UserContact::model()->find(array(
                                    'select'    => 'email',
                                    'condition' => 'email=:email',
                                    'params'    => array(':email'=>$_POST['UserContactForm']['email']))
                                );
                            }

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
                    $this->render('contacts', array('data'=>$dataProvider, 'view'=>$view));
                } else {
                    if(!empty($message)) {
                        Changes::saveChange($message);
                
                        $model->attributes = $_POST['UserContactForm'];
                        if (!empty($_POST['UserContactForm']['password_confirm'])) {
                            $model->password = crypt($_POST['UserContactForm']['password_confirm'], User::model()->blowfishSalt());
                        }
                    }
                    if ($model->save()) {
                        Yii::app()->user->setFlash('saved_id', $model->id);
                        Yii::app()->user->setFlash('message', 'Контактное лицо "' . $model->surname . ' ' . $model->name . '" сохранено успешно.');
                        $this->redirect('/admin/contact/');
                    }
                }
            } else $this->renderPartial('editcontact', array('model' => $form), false, true);
        } else {
            throw new CHttpException(403, Yii::t('yii', 'У Вас недостаточно прав доступа.'));
        }
    }

    public function actionDeleteContact($id)
    {
        $model = UserContact::model()->findByPk($id);
        if (Yii::app()->user->checkAccess('trDeleteUserContact')) {
            if (UserContact::model()->deleteByPk($id)) {
                $message = 'Удален контакт ' . $model['name'] . ' ' . $model['surname'];
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
            ->order('company')
            ->queryAll()
        ;
    }
}
