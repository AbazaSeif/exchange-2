<?php

class RegistrationForm extends CFormModel
{
    public $firmName;
    public $name;
    public $phone;
    public $email;
    public $description;

    public function rules()
    {
        return array(
            array('firmName, name, phone', 'required'),
            array('email', 'email'),
            array('phone', 'numerical')
        );
    }

    public function attributeLabels()
    {
        return array(
            'firmName'=>'Название организации',
            'name'=>'Контактное лицо',
            'phone'=>'Телефон',
            'email'=>'Email',
            'description'=>'Примечание',
        );
    }
}
