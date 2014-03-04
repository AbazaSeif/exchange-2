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
    public $secondname;
    public $surname;
    public $phone;
    public $email;
    public $description;
    public $ownership;
    public $nds;

    public function rules()
    {
        return array(
            array('company, country, region, district, inn, name, secondname, surname, phone, email', 'required'),
            array('email', 'email'),
            array('phone, inn', 'numerical'),
            array('inn', 'length', 'min' => 12, 'max'=>12, 
                'tooShort'=>Yii::t("translation", "{attribute} должен содержать 12 символов."),
                'tooLong'=>Yii::t("translation", "{attribute} должен содержать 12 символов.")
            )
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
            'secondname' => 'Отчество',
            'surname' => 'Фамилия',
            'phone'=>'Телефон',
            'email'=>'Электронная почта',
            'description'=>'Примечание',
            'ownership'=>'Форма собственности',
            'nds' => 'Отображать цену с НДС'
        );
    }
}
