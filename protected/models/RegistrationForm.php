<?php

class RegistrationForm extends CFormModel
{
    public $company;
    public $country;
    public $region;
    public $city;
    public $district;
    public $inn;
    public $name;
    public $second_name;
    public $surname;
    public $phone;
    public $email;
    public $description;

    public function rules()
    {
        return array(
            array('company, country, region, city, district, inn, name, second_name, surname, phone, email', 'required'),
            array('email', 'email'),
            array('phone', 'numerical')
        );
    }

    public function attributeLabels()
    {
        return array(
            'company' => 'Название комании',
            'inn' => 'ИНН/УНП ',
            'country' => 'Страна',
            'region' => 'Область',
            'city' => 'Город',
            'district' => 'Район',
            'name' => 'Имя',
            'second_name' => 'Отчество',
            'surname' => 'Фамилия',
            'phone'=>'Телефон',
            'email'=>'Электронная почта',
            'description'=>'Примечание',
        );
    }
}
