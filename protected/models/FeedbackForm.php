<?php

class FeedbackForm extends CFormModel
{
    public $name;  
    public $surname;  
    public $phone; 
    public $email;
    public $message; 
    public $verifyCode;

    public function rules()
    {
        return array (
            array('name, surname, email, message, verifyCode', 'required'),
            array('phone', 'numerical', 'integerOnly' => true),
            array('email', 'email', 'message'=>'Неправильный Email адрес'), 
            array('verifyCode', 'captcha'),
            array('name, surname, email, message, phone', 'safe'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'name'=>'Имя',
            'surname'=>'Фамилия',
            'email'=>'Email',
            'message'=>'Текст сообщения',
            'phone'=>'Телефон',
            'password'=>'Пароль',
            'verifyCode' => 'Код проверки',
        );
    }
}
