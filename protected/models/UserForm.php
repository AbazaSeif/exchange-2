<?php

class UserForm extends CFormModel
{
    public $id;
    public $password;
    public $password_confirm;
    public $status;
    public $company;
    public $inn;
    public $country;
    public $region;
    public $city;
    public $district;
    public $name;
    public $secondname;
    public $surname;
    public $phone;
    public $phone2;
    public $email;
    public $show;

    public function rules()
    {
        return array(
            array('inn, status, phone, phone2', 'numerical', 'integerOnly'=>true),
            //array('company, country, password, region, district, inn, name, surname, phone, email', 'required'),
            array('company, inn, name', 'required'),
            array('password, name, secondname, surname', 'match', 'pattern'=>'/^[\S]*$/', 'message'=>'Поле "{attribute}" не должно содержать пробелы'),
            array('email', 'email', 'message'=>'Неправильный Email адрес'),
            array('inn', 'length', 'max'=>12,
                'tooLong'=>Yii::t("translation", "{attribute} должен содержать максимум 12 символов.")
            ),
            array('password, password_confirm', 'length', 'min'=>6, 'allowEmpty'=>true),
            array('password, password_confirm', 'match', 'pattern'=>'/^([a-zA-Zа-яА-ЯёЁ\d]+)$/i', 'message'=>'Пароль должен содержать только следующие символы: 0-9 a-z A-Z а-я А-Я'),
            array('password, password_confirm', 'match', 'pattern'=>'/([a-zA-Zа-яА-Я]+)/', 'message'=>'Пароль должен содержать минимум одну букву'),
            array('password, password_confirm', 'match', 'pattern'=>'/([0-9]+)/', 'message'=>'Пароль должен содержать минимум одну цифру'),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            'company' => 'Комания',
            'inn' => 'ИНН/УНП ',
            'status' => 'Статус',
            'country' => 'Страна',
            'region' => 'Область',
            'city' => 'Город',
            'district' => 'Район',
            'name' => 'Имя',
            'secondname' => 'Отчество',
            'surname' => 'Фамилия',
            'password' => 'Пароль',
            'password_confirm' => 'Пароль',
            'phone' => 'Телефон',
            'phone2' => 'Телефон №2',
            'email' => 'Email',
            'show'  => 'Показывать перевозки',
        );
    }
}
