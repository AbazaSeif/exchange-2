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
        
        $contactUsers = Yii::app()->db->createCommand()
            ->select('id')
            ->from('user_contact')
            ->queryAll()
        ;

        if(!empty($users)) {
            foreach($users as $user) {
               $this->sendMail($user['id'], 'user');
            }
        }
        
        if(!empty($contactUsers)) {
            foreach($contactUsers as $contact) {
               $this->sendMail($contact['id'], 'user_contact');
            }
        }
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
            $email = new TEmail;
            $email->from_email = Yii::app()->params['adminEmail'];
            $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
            $email->to_email   = $user['email'];
            $email->to_name    = '';
            $email->subject    = "Приглашение";
            $email->type = 'text/html';
            $email->body = "<h1>Уважаемый(ая) " . $user['name'] . ' ' . $user['secondname'] . ", </h1>" . 
                "Приглашаем Вас воспользоваться биржей перевозок <a href='http://exchange.lbr.ru'>ЛБР АгроМаркет по ссылке</a>" .
                "<hr><h5>Это сообщение является автоматическим, на него не следует отвечать</h5>"
            ;
            $email->sendMail();
        }
    }
}
