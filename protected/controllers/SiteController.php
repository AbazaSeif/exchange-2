<?php
class SiteController extends Controller
{
    public function actionIndex($s = null)
    {
        $this->forward('/transport/i/');
    }
    
    public function actionLogin()
    {
        $model = new LoginForm;

        // if it is ajax validation request
        if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if(isset($_POST['LoginForm']))
        {
            $model->attributes=$_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->render('login',array('model'=>$model));
    }

    public function actionDescription($id)
    {
	$transportInfo=Yii::app()->db->createCommand("SELECT * from transport where id='".$id."'")->queryRow();
        $allRatesForTransport = Yii::app()->db->createCommand()
            ->select('r.date, r.price, u.name')
            ->from('rate r')
            ->join('user u', 'r.user_id=u.id')
            ->where('r.transport_id=:id', array(':id'=>$id))
            ->order('r.date desc')
            ->queryAll()
        ;
        
        $this->render('item', array('rateData' => $dataProvider, 'transportInfo' => $transportInfo));
    }
    
    /* check if in table user_field doesn't exists row for user and create it */
    public function actionFields()
    {
        $users = Yii::app()->db->createCommand()
            ->select('id')
            ->from('user')
            ->queryAll()
        ;

        if(!empty($users)) {
            foreach($users as $user) {
                $record = UserField::model()->find(array(
                    'select'=>'id',
                    'condition'=>'user_id=:id',
                    'params'=>array(':id'=>$user['id']))
                );
                if(empty($record)){
                    $newFerrymanFields = new UserField;
                    $newFerrymanFields->user_id = $user['id'];
                    $newFerrymanFields->mail_transport_create_1 = false;
                    $newFerrymanFields->mail_transport_create_2 = false;
                    $newFerrymanFields->mail_kill_rate = false;
                    $newFerrymanFields->mail_before_deadline = false;
                    $newFerrymanFields->mail_deadline = true;
                    $newFerrymanFields->with_nds = false;            
                    $newFerrymanFields->save(); 
                }
            }
        }
    }
    
    public function actionFeedback()
    {
        $this->render('feedback');
    }
    
    public function actionHelp()
    {
        $this->render('help');
    }
    
    public function actionRegistration()
    { 
        $model = new RegistrationForm;

        if(isset($_POST['RegistrationForm'])) {
            $newUser = User::model()->find(array(
                'condition'=>'inn=:inn',
                'params'=>array(':inn'=>$_POST['inn']))
            );

            if($newUser) {
                $userInfo = array();
                $user = new User();
                $user->attributes = $_POST['RegistrationForm'];
                //var_dump($user->attributes);
                $user->status = 0; //User::USER_NOT_CONFIRMED;
                $user->company = $_POST['RegistrationForm']['ownership'] . ' "' . $_POST['RegistrationForm']['company'] . '"';
                $user->password = crypt($_POST['RegistrationForm']['password'], User::model()->blowfishSalt());
                //$user->login = $user->inn;
                if($user->validate() && $user->save()) {
                    $newFerrymanFields = new UserField;
                    $newFerrymanFields->user_id = $user->id;
                    $newFerrymanFields->mail_transport_create_1 = false;
                    $newFerrymanFields->mail_transport_create_2 = false;
                    $newFerrymanFields->mail_kill_rate = false;
                    $newFerrymanFields->mail_before_deadline = false;
                    $newFerrymanFields->mail_deadline = true;
                    $newFerrymanFields->with_nds = (bool)$_POST['RegistrationForm']['nds'];    
                    $newFerrymanFields->save();

                    /*if(!$newFerrymanFields->save()) {
                        var_dump($newFerrymanFields->getErrors()); 
                        exit;
                    };*/

                    $this->sendMail(Yii::app()->params['adminEmail'], 1, $_POST['RegistrationForm']);
                    $this->sendMail($_POST['email'], 0, $_POST['RegistrationForm']);

                    Dialog::message('flash-success', 'Отправлено!', 'Ваша заявка отправлена. Вы получите на почту инструкции по активации, когда ваша заявка будет рассмотрена. Спасибо за интерес, проявленный к нашей компании');
                }
            } else {
                Dialog::message('flash-success', 'Внимание!', 'Пользователь с таким ИНН/УНП уже зарегистрирован в базе, если у Вас возникли проблемы с авторизацией свяжитесь с нашим отделом логистики. ');  
            }
            $this->redirect('/site/login/');
        } else {
            $this->render('registration', array('model' => $model));
        }
    }
    
    public function actionRestore()
    { 
        $model = new RestoreForm;
        //var_dump($_POST['RestoreForm']['inn']);exit;
        if(isset($_POST['RestoreForm'])) {
            $inn = $_POST['RestoreForm']['inn'];
            $user = User::model()->find(array(
                'condition'=>'inn=:inn',
                'params'=>array(':inn'=>$inn))
            );
            
            if($user) {
                if($user->email) {
                    $password = $this->randomPassword();
                    $user->password = crypt($password, User::model()->blowfishSalt());
                    if($user->save()) {
                        // send mail to ferryman with new password
                        $email = new TEmail;
                        $email->from_email = Yii::app()->params['adminEmail'];
                        $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
                        $email->to_email   = $user->email;
                        $email->to_name    = '';
                        $email->subject    = 'Смена пароля';
                        $email->type = 'text/html';
                        $email->body = '<div>'.
                                '<p>Ваш пароль для "Онлайн биржи перевозок ЛБР-АгроМаркет" был изменен:</p>'.
                                '<p>Новый пароль: <b>'.$password.'</b></p>'.
                                '<p>Для смены пароля зайдите в свой аккаунт и воспользуйтесь вкладкой "Настроки->Смена пароля"</p>'.
                            '</div>
                            <hr/><h5>Это уведомление является автоматическим, на него не следует отвечать.</h5>
                        ';
                        $email->sendMail();
                        Dialog::message('flash-success', 'Отправлено!', 'Инструкции к дальнейшим действиям были отправлены на ваш почтовый ящик.');
                    }
                } else {
                    Dialog::message('flash-success', 'Внимание!', 'Ваша заявка на восстановление доступа отправлена, в ближайшее время с вами свяжутся представители нашей компании.');
                    // send mail to logist
                    $email = new TEmail;
                    $email->from_email = Yii::app()->params['adminEmail'];
                    $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
                    $email->to_email   = Yii::app()->params['logistEmail'];
                    $email->to_name    = '';
                    $email->subject    = 'Восстановление доступа';
                    $email->type = 'text/html';
                    $email->body = '<div>'.
                            '<p>Перевозчик "'. $user->company. '" ИНН/УНП = '. $user->inn .' запросил восстановление доступов, однако он не указал email. </p>'.
                            '<p>Контактный телефон: '. $user->phone . '</p>'.
                        '</div>
                        <hr/><h5>Это уведомление является автоматическим, на него не следует отвечать.</h5>
                    ';
                    $email->sendMail();
                }
            } else {
                Dialog::message('flash-error', 'Внимание!', 'Пользователя с таким "ИНН/УНП" не найдено, свяжитесь с отделом логистики.');
            }
            $this->redirect('/user/login/');
            
        } else {
            $this->render('restore', array('model' => $model));
        }
    }
     
    public function sendMail($to, $typeMessage, $post)
    {
        $email = new TEmail;
        $email->from_email = Yii::app()->params['adminEmail'];
        $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
        $email->to_email   = $to;
        $email->to_name    = '';
        $email->subject    = 'Заявка на регистрацию';
        $email->type = 'text/html';
        if(!empty($typeMessage)) {
            $description = (!empty($post['description'])) ? '<p>Примечание:<b>'.$post['description'].'</b></p>' : '' ;
            $email->body = '
              <div>
                  <p>Компания "'.$post['firmName'].'" подала заявку на регистрацию в бирже перевозок ЛБР АгроМаркет.</p>
                  <p>Контактное лицо: <b>'.$post['name']. ' ' .$post['surname'].'</b></p>
                  <p>Телефон: <b>'.$post['phone'].'</b></p>
                  <p>Email: <b>'.$post['email'].'</b></p>'.
                   $description .
              '</div>
              <hr/><h5>Это уведомление является автоматическим, на него не следует отвечать.</h5>
            ';
        } else {
            $email->body = '
                <div> 
                    <p>Ваша регистрация будет рассмотрена и Вам будут высланы инструкции с дальнейшими действиями. </p>
                </div>
                <hr/><h5>Это уведомление является автоматическим, на него не следует отвечать.</h5>
            ';
        }
        $email->sendMail();
    }
     
    public function actionQuick() 
    { 
        $model = new QuickForm;
        $model->attributes = $_POST['QuickForm'];
        if($model->validate()) {
            $user = User::model()->findByPk($model->user); 
            $email = new TEmail;
            $email->from_email = $user->email;
            $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
            $email->to_email   = Yii::app()->params['adminEmail'];
            $email->to_name    = 'Модератору';
            $email->subject    = '';
            $email->type = 'text/html';
            
            $email->body = "<div>
                    <p>
                      Пользоваетель " . $user->name." (" . $user->email . ") 
                      находясь в перевозке с id = ".$model->transport." обратился к модератору Биржи перевозок ЛБР 'АгроМаркет'
                      со следующим обращением:
                    </p>
                    <p>" . $model->message . "</p>
                </div>
            ";
            $email->sendMail();
        }
        
        Dialog::message('flash-success', 'Отправлено!', 'Спасибо, '.$user->name.'! Ваше письмо отправлено!');
        $this->redirect(array('transport/description/id/1'));
    }
    
    private function randomPassword() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 16; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
    
    public function actionError()
    {
        if($error=Yii::app()->errorHandler->error)
        {
            if(Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }
}