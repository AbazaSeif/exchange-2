<?php

class RegistrationForm extends CFormModel
{
    public $company;
    public $password;
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
    
    const WEAK = 0;
    const STRONG = 1;

    public function rules()
    {//Yii::log("------------------------------------",'info', 'application');
        return array(
            array('company, country, region, district, inn, name, secondname, surname, phone, email', 'required'),
            array('email', 'email'),
            array('phone, inn', 'numerical'),
            //array('password', 'passwordStrength'),
            //array('inn', 'unique', 'attributeName'=>'inn', 'className'=>'User', 'allowEmpty'=>false, 'skipOnError'=>true),
            //array('inn', 'unique', 'message' => 'Такой ИНН/УНП уже зарегистрирован'),
            //array('inn', 'unique', 'className'=>'User'),
            //array('inn', 'checkMyUniqunessInBrand'),
            array('inn', 'unique', 'attributeName'=>'User.inn'),
            array('inn', 'length', 'min' => 12, 'max'=>12, 
                'tooShort'=>Yii::t("translation", "{attribute} должен содержать 12 символов."),
                'tooLong'=>Yii::t("translation", "{attribute} должен содержать 12 символов.")
            )
        );
    }

    public function checkMyUniqunessInBrand($attribute,$params) {
        //if($this->getIsNewRecord()  &&  Realisation:model()->count('name=:name AND brand_id=:brand_id',
           // array(':name'=>$this->name,':brand_id'=>$this->brand_id)) > 0) {
            $this->addError( $attribute, "$attribute must be unique in user scope!" );
       // }
    }
    
    public function attributeLabels()
    {
        return array(
            'company' => 'Название комании',
            'inn' => 'ИНН/УНП ',
            'country' => 'Страна',
            'region' => 'Область',
            'password' => 'Пароль',
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
