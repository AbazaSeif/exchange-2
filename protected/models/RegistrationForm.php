<?php

class RegistrationForm extends CFormModel
{
    public $firmName;
    public $name;
    public $surname;
    public $phone;
    public $email;
    public $description;

    public function rules()
    {
        return array(
            array('firmName, name, surname, phone, email', 'required'),
            array('email', 'email'),
            array('phone', 'numerical')
        );
    }

    public function attributeLabels()
    {
        return array(
            'firmName'=>'Название организации',
            'name'=>'Имя контактного лица',
            'surname'=>'Фамилия контактного лица',
            'phone'=>'Телефон',
            'email'=>'Email',
            'description'=>'Примечание',
        );
    }
}
