<?php

class RegistrationForm extends CFormModel
{
    public $company;
    public $password;
    public $confirm_password;
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
    public $show;
    public $iagree;

    public function rules()
    {//Yii::log("------------------------------------",'info', 'application');
        return array(
            array('company, iagree, password, confirm_password, country, region, district, inn, name, secondname, surname, phone, email', 'required'),
            array('iagree', 'compare', 'compareValue' => true, 'message' => 'Для отправки формы на обработку требуется Ваше согласие' ),
            array('email', 'email'),
            array('company','match', 'pattern'=>'/^([\sa-zA-Zа-яА-ЯёЁ\d]+)$/i', 'message'=>'Поле "{attribute}" должно содержать только следующие символы: 0-9,a-z,A-Z,а-я,А-Я и пробел'),
            array('phone, inn', 'numerical'),
            array('password', 'length', 'min'=>6, 'allowEmpty'=>false),
            array('password','match', 'pattern'=>'/^([a-zA-Zа-яА-ЯёЁ\d]+)$/i', 'message'=>'Пароль должен содержать только следующие символы: 0-9 a-z A-Z а-я А-Я'),
            array('password', 'match', 'pattern'=>'/([a-zA-Zа-яА-Я]+)/', 'message'=>'Пароль должен содержать минимум одну букву'),
            array('password', 'match', 'pattern'=>'/([0-9]+)/', 'message'=>'Пароль должен содержать минимум одну цифру'),
            array('confirm_password', 'compare', 'compareAttribute'=>'password', 'message'=>'Пароли не совпадают'),
            
            //array('password', 'passwordStrength'),
            //array('inn', 'unique', 'attributeName'=>'inn', 'className'=>'User', 'allowEmpty'=>false, 'skipOnError'=>true),
            //array('inn', 'unique', 'message' => 'Такой ИНН/УНП уже зарегистрирован'),
            //array('inn', 'unique', 'className'=>'User'),
            //array('inn', 'checkMyUniqunessInBrand'),
            //array('inn', 'unique', 'attributeName'=>'User.inn'),
            
            array('inn', 'length', 'max'=>12, 
                //'tooShort'=>Yii::t("translation", "{attribute} должен содержать 12 символов."),
                'tooLong'=>Yii::t("translation", "{attribute} должен содержать максимум 12 символов.")
            )
        );
    }
/*
    public function checkMyUniqunessInBrand($attribute,$params) {
        //if($this->getIsNewRecord()  &&  Realisation:model()->count('name=:name AND brand_id=:brand_id',
           // array(':name'=>$this->name,':brand_id'=>$this->brand_id)) > 0) {
            $this->addError( $attribute, "$attribute must be unique in user scope!" );
       // }
    }
    */
    public function attributeLabels()
    {
        return array(
            'company' => 'Название комании',
            'inn' => 'ИНН/УНП ',
            'country' => 'Страна',
            'region' => 'Область',
            'password' => 'Пароль',
            'confirm_password' => 'Подтверждение пароля',
            'city' => 'Город',
            'district' => 'Район',
            'name' => 'Имя',
            'secondname' => 'Отчество',
            'surname' => 'Фамилия',
            'phone'=>'Телефон',
            'email'=>'Электронная почта',
            'description'=>'Примечание',
            'ownership'=>'Форма собственности',
            'nds' => 'Отображать цену с НДС',
            'show' => 'Показывать перевозки',
            'iagree' => 'Согласен на обработку персональных данных, введенных в форму'
        );
    }
}
