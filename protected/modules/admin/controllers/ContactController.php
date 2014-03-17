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

    //User block
    public function actionIndex()
    {
        if(Yii::app()->user->checkAccess('readContact')) {
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
                $form->password = '';
                //$group = UserGroup::getUserGroupArray();
                //$view = $this->renderPartial('user/edituser', array('model'=>$model, 'group'=>$group), true, true);
                $view = $this->renderPartial('editcontact', array('model'=>$form), true, true);
            }
            $this->render('contacts', array('data'=>$dataProvider, 'view'=>$view));
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionCreateContact()
    {
        if(Yii::app()->user->checkAccess('createContact')) {
            $form = new UserContactForm;
            if(isset($_POST['UserContactForm'])) {
                //if(isset($_POST['UserContactForm']['password'])) {
                    $model = new UserContact;
                    //$model->attributes = $_POST['UserContactForm'];
                    $model->u_id = $_POST['UserContactForm']['u_id'];
                    $model->name = $_POST['UserContactForm']['name'];
                    $model->surname = $_POST['UserContactForm']['surname'];
                    $model->secondname = $_POST['UserContactForm']['secondname'];
                    $model->phone = $_POST['UserContactForm']['phone'];
                    $model->phone2 = $_POST['UserContactForm']['phone2'];
                    $model->email = $_POST['UserContactForm']['email'];
                    $model->status = $_POST['UserContactForm']['status'];
                    $model->password = crypt($_POST['UserContactForm']['password'], User::model()->blowfishSalt());
                    
                    //$model->password = crypt($_POST['UserContactForm']['password'], User::model()->blowfishSalt());
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
                        Yii::app()->user->setFlash('message', 'Контакт "'.$model->name.' '.$model->surname.'" создан успешно.');
                        $this->redirect('/admin/contact/');
                    }
                /*} else {
                    Yii::app()->user->setFlash('error', 'Внимание!!! Введите пароль');
                }*/
                
                //print_r($model->getErrors());
            }
            $this->renderPartial('editcontact', array('model'=>$form), false, true);
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionEditContact($id) 
    {
        $model = UserContact::model()->findByPk($id);  
        $form = new UserContactForm;
        $form->attributes = $model->attributes;
        $form->id = $id;
        $form->password = '';
        if (Yii::app()->user->checkAccess('editContact')) {
            if (isset($_POST['UserContactForm'])) {
                $changes = array();
                foreach ($_POST['UserContactForm'] as $key => $value) {
                    if (trim($model[$key]) != trim($value) && $key != 'password') {
                        $changes[$key]['before'] = $model[$key];
                        $changes[$key]['after'] = $value;
                        $model[$key] = trim($value);
                    } else if($key == 'password' && $model->password !== crypt(trim($_POST['UserContactForm']['password']), $model->password)) {
                        $model[$key] = crypt($_POST['UserContactForm']['password'], User::model()->blowfishSalt());
                        $changes[$key] = 'Изменен пароль';
                    }
                }
                
                $model->attributes = $_POST['UserContact'];
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
                    }
                    Changes::saveChange($message);
                }
                
                if ($model->save()) {
                    Yii::app()->user->setFlash('saved_id', $model->id);
                    Yii::app()->user->setFlash('message', 'Контактное лицо "' . $model->name . ' ' . $model->surname . '" сохранено успешно.');
                    $this->redirect('/admin/contact/');
                }
            }
            //$this->renderPartial('editcontact', array('model' => $model, 'status' => UserController::$userStatus), false, true); // ?????
            $this->renderPartial('editcontact', array('model' => $form), false, true); // ?????
        } else {
            throw new CHttpException(403, Yii::t('yii', 'У Вас недостаточно прав доступа.'));
        }
    }

    public function actionDeleteContact($id) 
    {
        $model = User::model()->findByPk($id);
        if (Yii::app()->user->checkAccess('deleteContact')) {
            if (User::model()->deleteByPk($id)) {
                $message = 'Удален контакт ' . $model['name'] . ' ' . $model['surname'];
                Changes::saveChange($message);
                Yii::app()->user->setFlash('message', 'Контакт удален успешно.');
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
