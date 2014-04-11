<?php

class UserContactForm extends CFormModel
{
    public $id;
    public $parent;
    public $password;
    public $password_confirm;
    public $status;
    public $name;
    public $secondname;
    public $surname;
    public $phone;
    public $phone2;
    public $email;
    public $company; // !!!

    public function rules()
    {
        return array(
            //array('name, surname, phone, email, password', 'required'),
            array('name, email', 'required'),
            array('status, phone, phone2', 'numerical', 'integerOnly'=>true),
            array('email', 'email', 'message'=>'Неправильный Email адрес'),
            //array('name, secondname, surname', 'match', 'pattern'=>'/^[\S]*$/', 'message'=>'Поле "{attribute}" не должно содержать пробелы'),
            //array('parent', 'match', 'pattern'=>'/^[0]$/', 'message'=>'Выберите фирму'),
            array('password, password_confirm', 'length', 'min'=>6, 'allowEmpty'=>true),
            array('password, password_confirm', 'match', 'pattern'=>'/^([a-zA-Zа-яА-ЯёЁ\d]+)$/i', 'message'=>'Пароль должен содержать только следующие символы: 0-9 a-z A-Z а-я А-Я'),
            array('password, password_confirm', 'match', 'pattern'=>'/([a-zA-Zа-яА-Я]+)/', 'message'=>'Пароль должен содержать минимум одну букву'),
            array('password, password_confirm', 'match', 'pattern'=>'/([0-9]+)/', 'message'=>'Пароль должен содержать минимум одну цифру'),
        ); 
    }
    
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'parent' => 'Контактное лицо',
            'password' => 'Пароль',
            'password_confirm' => 'Пароль',
            'name' => 'Имя',
            'surname' => 'Фамилия',
            'secondname' => 'Отчество',
            'email'  => 'Email',
            'status' => 'Статус',
            'phone'  => 'Телефон',
            'phone2' => 'Телефон №2',
            'parent' => 'Фирма',
            'company' => 'Надпись', //!!!
        );
    }
}
