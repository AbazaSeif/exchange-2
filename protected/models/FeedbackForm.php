<?php

class FeedbackForm extends CFormModel
{
    public $name;  // поле 'Введите текущий email'
    public $surname;  // поле 'Введите текущий email'
    public $phone; // поле 'Введите новый email
    public $email; // поле 'Введите новый email
    public $message; // поле 'Введите новый email

    public function rules()
    {
        return array (
            array('name, surname, email, message', 'required'),
            array('phone', 'numerical', 'integerOnly' => true),
            array('email', 'email', 'message'=>'Неправильный Email адрес'), 
        );
    }

    public function attributeLabels()
    {
        return array(
            'name'=>'Имя',
            'surname'=>'Фамилия',
            'email'=>'email',
            'message'=>'Текст сообщения',
            'phone'=>'Телефон',
            'password'=>'Пароль',
        );
    }
}
