<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $login
 * @property string $password
 * @property string $name
 * @property string $surname
 * @property string $email
 * @property integer $group_id
 * @property integer $type
 *
 * The followings are the available model relations:
 * @property Message[] $messages
 * @property Rate[] $rates
 * @property UserGroup $group
 * @property UserEvent[] $userEvents
 * @property UserField[] $userFields
 */
class User extends CActiveRecord
{
	const USER_NOT_CONFIRMED = 0;
    const USER_TEMPORARY_BLOCKED = 3;
    const USER_BLOCKED = 4;
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
			array('group_id, type', 'numerical', 'integerOnly'=>true),
			array('login', 'length', 'max'=>64),
			array('password, name, surname, email', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, login, password, name, surname, email, group_id, type', 'safe', 'on'=>'search'),
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
			'messages' => array(self::HAS_MANY, 'Message', 'user_id'),
			'rates' => array(self::HAS_MANY, 'Rate', 'user_id'),
			'userGroup' => array(self::BELONGS_TO, 'UserGroup', 'group_id'),
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
			'login' => 'Логин',
			'password' => 'Пароль',
			'name' => 'Имя',
			'surname' => 'Фамилия',
			'email' => 'e-mail',
			'group_id' => 'Группа',
			'type' => 'Тип',
			'status' => 'Статус',
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
		$criteria->compare('login',$this->login,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('surname',$this->surname,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('group_id',$this->group_id);
		$criteria->compare('type',$this->type);

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
        
        //  Метод проверяет изменен ли пароль
        protected function beforeSave() {
            parent::beforeSave();
            if (isset($_POST['User_password']) && $_POST['User_password']!=''){
                $this->password = crypt($_POST['User_password'], User::model()->blowfishSalt());
            }
            return true;
        }
        
        //  Метод возвращет $cost значное число для хэширования пароля, где: 
        //  $cost - количество возвразаемых знаков
        protected function blowfishSalt($cost = 13)
        {
            if (!is_numeric($cost) || $cost < 4 || $cost > 31) {
                throw new Exception("cost parameter must be between 4 and 31");
            }
            $rand = array();
            for ($i = 0; $i < 8; $i += 1) {
                $rand[] = pack('S', mt_rand(0, 0xffff));
            }
            $rand[] = substr(microtime(), 2, 6);
            $rand = sha1(implode('', $rand), true);
            $salt = '$2a$' . sprintf('%02d', $cost) . '$';
            $salt .= strtr(substr(base64_encode($rand), 0, 22), array('+' => '.'));
            return $salt;
        }
        
        //  Метод проверяет доступ к пользователю, где:
        //  $params - массив с двумя значениями:
        //  1. group - id группы изменяемого пользователя
        //  2. userid - id изменяемого пользователя
        static function usersAccess($params){
            if ($params){
                $group = UserGroup::model()->findByPk($params['group']);
                if ($group->level > Yii::app()->user->_level || $params['userid']==Yii::app()->user->_id)
                    return true;
            }
            return false;
        }
}
