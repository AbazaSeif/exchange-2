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
            array('new_email', 'safe'),  
            
            //array('email, new_email', 'length', 'min'=>3, 'allowEmpty'=>false),
            array('new_email', 'email', 'message'=>'Неправильный Email адрес'), 
        );
    }

    public function attributeLabels()
    {
        return array(
            'new_email'=>'Новый email',
            'password'=>'Пароль',
        );
    }
}
