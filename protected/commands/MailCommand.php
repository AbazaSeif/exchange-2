<?php
class MailCommand extends CConsoleCommand 
{
    public function run($args)
    {
        $this->mailToAllUsers();
    }
    
    public function mailToAllUsers() {
        $users = Yii::app()->db->createCommand()
            ->select('id')
            ->from('user')
            ->queryAll()
        ;

        if(!empty($users)) {
            foreach($users as $user) {
               $this->sendMail($user['id'], 'user');
            }
        }
        
        /*
        $contactUsers = Yii::app()->db->createCommand()
            ->select('id')
            ->from('user_contact')
            ->queryAll()
        ;
        
        if(!empty($contactUsers)) {
            foreach($contactUsers as $contact) {
               $this->sendMail($contact['id'], 'user_contact');
            }
        }
        */
    }

    public function sendMail($userId, $table)
    {
        $user = Yii::app()->db->createCommand()
            ->select('name, secondname, email')
            ->from($table)
            ->where('id = :id', array(':id' => $userId))
            ->queryRow()
        ;

        if(isset($user['email'])) {
            $password = $this->randomPassword();
            $curUser = User::model()->findByPK($userId);
            $curUser->password = crypt($password, User::model()->blowfishSalt());
            $curUser->save();
            
            $email = new TEmail;
            $email->from_email = Yii::app()->params['adminEmail'];
            $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
            $email->to_email   = $user['email'];
            $email->to_name    = '';
            $email->subject    = "Приглашение";
            $email->type = 'text/html';
            $email->body = "<h1>Уважаемый(ая) " . $user['name'] . ' ' . $user['secondname'] . ", </h1>" . 
                "Приглашаем Вас воспользоваться биржей перевозок <a href='http://exchange.lbr.ru'>ЛБР АгроМаркет</a>" . "<br>" .
                "Ваш логин: " . $user['email'] . "<br>" .
                "Ваш пароль: " . $password . "<br>" .
                "Изменить пароль Вы можете зайдя в кабинет пользователя с помощью указанных логина и пароля. " . 
                "<hr><h5>Это сообщение является автоматическим, на него не следует отвечать</h5>"
            ;
            $email->sendMail();
        }
    }
    
    public function randomPassword() {
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
