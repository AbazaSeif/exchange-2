<?php

/**
 * This is the model class for table "User".
 *
 * The followings are the available columns in table 'User':
 * @property integer $id
 * @property string $company
 * @property integer $inn
 * @property integer $status
 * @property string $country
 * @property string $region
 * @property string $city
 * @property string $district
 * @property string $name
 * @property string $secondname
 * @property string $surname
 * @property string $login
 * @property string $password
 * @property integer $phone
 * @property string $email
 *
 * The followings are the available model relations:
 * @property Changes[] $changes
 * @property Message[] $messages
 * @property NfyMessages[] $nfyMessages
 * @property NfySubscriptions[] $nfySubscriptions
 * @property Rate[] $rates
 * @property UserEvent[] $userEvents
 * @property UserField[] $userFields
 */
class User extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
            return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
            // NOTE: you should only define rules for those attributes that
            // will receive user inputs.
            return array(
                array('inn, status, phone', 'numerical', 'integerOnly'=>true),
                array('login', 'length', 'max'=>64),
                array('email', 'email', 'message'=>'Неправильный Email адрес'),
                array('company, country, region, city, district, name, secondname, surname, password, email', 'safe'),
                // The following rule is used by search().
                // @todo Please remove those attributes that should not be searched.
                array('id, company, inn, status, country, region, city, district, name, secondname, surname, login, password, phone, email', 'safe', 'on'=>'search'),
            );
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
            // NOTE: you may need to adjust the relation name and the related
            // class name for the relations automatically generated below.
            return array(
                'changes' => array(self::HAS_MANY, 'Changes', 'user_id'),
                'messages' => array(self::HAS_MANY, 'Message', 'user_id'),
                'nfyMessages' => array(self::HAS_MANY, 'NfyMessages', 'user_id'),
                'nfySubscriptions' => array(self::HAS_MANY, 'NfySubscriptions', 'user_id'),
                'rates' => array(self::HAS_MANY, 'Rate', 'user_id'),
                'userEvents' => array(self::HAS_MANY, 'UserEvent', 'user_id'),
                'userFields' => array(self::HAS_MANY, 'UserField', 'user_id'),
            );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
            return array(
                'id' => 'ID',
                'company' => 'Название комании',
                'inn' => 'ИНН/УНП ',
                'status' => 'Статус',
                'country' => 'Страна',
                'region' => 'Область',
                'city' => 'Город',
                'district' => 'Район',
                'name' => 'Имя',
                'secondname' => 'Отчество',
                'surname' => 'Фамилия',
                'login' => 'Логин',
                'password' => 'Пароль',
                'phone' => 'Телефон',
                'email' => 'Электронная почта',
            );
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
            // @todo Please modify the following code to remove attributes that should not be searched.

            $criteria=new CDbCriteria;

            $criteria->compare('id',$this->id);
            $criteria->compare('company',$this->company,true);
            $criteria->compare('inn',$this->inn);
            $criteria->compare('status',$this->status);
            $criteria->compare('country',$this->country,true);
            $criteria->compare('region',$this->region,true);
            $criteria->compare('city',$this->city,true);
            $criteria->compare('district',$this->district,true);
            $criteria->compare('name',$this->name,true);
            $criteria->compare('secondname',$this->secondname,true);
            $criteria->compare('surname',$this->surname,true);
            $criteria->compare('login',$this->login,true);
            $criteria->compare('password',$this->password,true);
            $criteria->compare('phone',$this->phone);
            $criteria->compare('email',$this->email,true);

            return new CActiveDataProvider($this, array(
                    'criteria'=>$criteria,
            ));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
            return parent::model($className);
	}
}
