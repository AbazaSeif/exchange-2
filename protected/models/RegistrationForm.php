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
    public $ownership;

    public function rules()
    {
        return array(
            array('company, country, city, inn, name, second_name, surname, phone, email', 'required'),
            array('inn', 'length', 'min' => 12, 'max'=>12, 
            'tooShort'=>Yii::t("translation", "{attribute} должен содержать 12 символов."),
            'tooLong'=>Yii::t("translation", "{attribute} должен содержать 12 символов.")),
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
            'ownership'=>'Форма собственности',
        );
    }
}
