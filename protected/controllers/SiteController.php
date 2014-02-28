<?php
class SiteController extends Controller
{
    public function actionIndex($s = null)
    {
        $this->forward('/transport/i/');
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
    
    public function actionRegistration()
    {
        
        $model = new RegistrationForm;
        
        if(isset($_POST['RegistrationForm'])) {
            
            //$this->password = crypt($_POST['User_password'], User::model()->blowfishSalt());
            // Save in database
            $userInfo = array();
            
            $user = new User();
            $user->attributes = $_POST['RegistrationForm'];
            //var_dump($user->attributes);
            $user->status = 0; //User::USER_NOT_CONFIRMED;
            $user->company = $_POST['RegistrationForm']['ownership'] . ' "' . $_POST['RegistrationForm']['company'] . '"';
            //$password = $this->randomPassword();
            //$user->password = crypt($password, User::model()->blowfishSalt(16));
            
            if($user->save()){
                $newFerrymanFields = new UserField;
                $newFerrymanFields->user_id = $user->id;
                $newFerrymanFields->mail_transport_create_1 = false;
                $newFerrymanFields->mail_transport_create_2 = false;
                $newFerrymanFields->mail_kill_rate = false;
                $newFerrymanFields->mail_before_deadline = false;
                $newFerrymanFields->mail_deadline = true;
                $newFerrymanFields->site_transport_create_1 = true;
                $newFerrymanFields->site_transport_create_2 = true;
                $newFerrymanFields->site_kill_rate = true;
                $newFerrymanFields->site_deadline = true;
                $newFerrymanFields->site_before_deadline = true;            
                $newFerrymanFields->with_nds = (bool)$_POST['RegistrationForm']['nds'];            
                $newFerrymanFields->save();
            }
            $this->sendMail(Yii::app()->params['adminEmail'], 1, $_POST['RegistrationForm']);

            Dialog::message('flash-success', 'Отправлено!', 'Ваша заявка отправлена. Спасибо за интерес, проявленный к нашей компании.');
            $this->redirect('/user/login/');
        } else {
            $this->render('registration', array('model' => $model));
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
        if(!empty($typeMessage)){
            $description = (!empty($post['description'])) ? '<p>Примечание:<b>'.$post['description'].'</b></p>' : '' ;
            $email->body = '
              <div>
                  <p>Компания "'.$post['firmName'].'" подала заявку на регистрацию в бирже перевозок ЛБР АгроМаркет.</p>
                  <p>Контактное лицо: <b>'.$post['name']. ' ' .$post['surname'].'</b></p>
                  <p>Телефон: <b>'.$post['phone'].'</b></p>
                  <p>Email: <b>'.$post['email'].'</b></p>'.
                   $description .
              '</div>
              <hr/><h5>Это автоматическое уведомление, на него не следует отвечать.</h5>
            ';
        } else {
            /*$email->body = '
                <div> 
                    <p>Ваши логин и пароль:</p>
                    <p>Логин: '.$post['login'].'</p>
                    <p>Пароль:'.$post['password'].'</p>
                </div>
            ';*/
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
}