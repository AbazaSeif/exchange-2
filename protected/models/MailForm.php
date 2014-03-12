<?php

class MailForm extends CFormModel
{
    //public $id; // для хранения идентификатора пользователя

    // дополнительные поля для новых данных
    public $email; // поле 'Введите текущий email'
    public $new_email; // поле 'Введите новый email

    public function rules()
    {
        return array(
            array('email, new_email', 'required'),    
            array('email, new_email', 'length', 'min'=>3, 'allowEmpty'=>false),
            array('email, new_email', 'email', 'message'=>'Неправильный Email адрес'), 
        );
    }

    public function attributeLabels()
    {
        return array(
            'email'=>'Текущий email',
            'new_email'=>'Новый email',
        );
    }
}
