<?php

class MailForm extends CFormModel
{
    // дополнительные поля для новых данных
    public $password; // поле 'Введите текущий email'
    public $new_email; // поле 'Введите новый email

    public function rules()
    {
        return array(
            array('new_email, password', 'required'),  
            //array('password, new_email', 'safe'),  
            array('password, new_email', 'safe'),  
            array('password', 'checkPass'),
            //array('email, new_email', 'length', 'min'=>3, 'allowEmpty'=>false),
            array('new_email', 'email', 'message'=>'Неправильный Email адрес'), 
            array('new_email', 'checkUniqueEmail'),
            array('new_email', 'length', 'min'=>6, 'allowEmpty'=>false),
        );
    }
    
    public function checkUniqueEmail()
    {
       $user = User::model()->findByAttributes(array('email'=>$this->new_email));
       $authUser = AuthUser::model()->findByAttributes(array('email'=>$this->new_email));
       if(!empty($user) || !empty($authUser)){
           $this->addError($this->new_email, 'Такой email уже зарегистрирован');
       }
    }
    
    public function checkPass()
    {
       $user = User::model()->findByPk(Yii::app()->user->_id);
       if ($user->password!==crypt($this->password, $user->password)){
          $this->addError($this->password, 'Введен неверный текущий пароль');
       }
    }

    public function attributeLabels()
    {
        return array(
            'new_email'=>'Новый email',
            'password'=>'Пароль',
        );
    }
}
