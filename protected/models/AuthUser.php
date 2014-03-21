<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $u_id
 * @property integer $g_id
 * @property string $login
 * @property string $password
 * @property string $email
 * @property string $name
 * @property string $surname
 * @property string $secondname
 * @property boolean $gender
 * @property string $dob
 * @property string $date_hire
 * @property string $phone_in
 * @property string $phone_mb
 * @property string $phone_mr
 * @property string $photo
 * @property string $skype
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property AuthGroup $g
 */
class AuthUser extends CActiveRecord
{

    public function getDbConnection()
    {
        return Yii::app()->db_auth;
    }

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
                    array('g_id, status', 'numerical', 'integerOnly'=>true),
                    array('f_id, login, password, email, name, surname, secondname, gender, dob, date_hire, phone_in, phone_mb, phone_mr, photo, skype', 'safe'),
                    // The following rule is used by search().
                    // @todo Please remove those attributes that should not be searched.
                    array('id, g_id, login, password, email, name, surname, secondname, gender, dob, date_hire, phone_in, phone_mb, phone_mr, photo, skype, status, f_id', 'safe', 'on'=>'search'),
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
            );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
            return array(
                    'id' => 'ID',
                    'g_id' => 'G',
                    'login' => 'Login',
                    'password' => 'Password',
                    'email' => 'Email',
                    'name' => 'Name',
                    'surname' => 'Surname',
                    'secondname' => 'Secondname',
                    'gender' => 'Gender',
                    'dob' => 'Dob',
                    'date_hire' => 'Date Hire',
                    'phone_in' => 'Phone In',
                    'phone_mb' => 'Phone Mb',
                    'phone_mr' => 'Phone Mr',
                    'photo' => 'Photo',
                    'skype' => 'Skype',
                    'status' => 'Status',
                    'f_id' => 'Филиал',
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
            $criteria->compare('g_id',$this->g_id);
            $criteria->compare('login',$this->login,true);
            $criteria->compare('password',$this->password,true);
            $criteria->compare('email',$this->email,true);
            $criteria->compare('name',$this->name,true);
            $criteria->compare('surname',$this->surname,true);
            $criteria->compare('secondname',$this->secondname,true);
            $criteria->compare('gender',$this->gender);
            $criteria->compare('dob',$this->dob,true);
            $criteria->compare('date_hire',$this->date_hire,true);
            $criteria->compare('phone_in',$this->phone_in,true);
            $criteria->compare('phone_mb',$this->phone_mb,true);
            $criteria->compare('phone_mr',$this->phone_mr,true);
            $criteria->compare('photo',$this->photo,true);
            $criteria->compare('skype',$this->skype,true);
            $criteria->compare('status',$this->status);
            $criteria->compare('f_id',$this->f_id);

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

    //  Метод проверяет доступ к пользователю, где:
    //  $params - массив с двумя значениями:
    //  1. group - id группы изменяемого пользователя
    //  2. userid - id изменяемого пользователя
    static function userAccess($params)
    {
        if ($params) {
            $group = Group::model()->findByPk($params['group']);
            if ($group->level > Yii::app()->user->getState('level') || $params['userid'] == Yii::app()->user->getState('_id'))
                return true;
        }
        return false;
    }
}
