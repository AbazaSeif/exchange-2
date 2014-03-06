<?php

/**
 * This is the model class for table "user_field".
 *
 * The followings are the available columns in table 'user_field':
 * @property integer $id
 * @property integer $user_id
 * @property boolean $mail_transport_create_1
 * @property boolean $mail_transport_create_2
 * @property boolean $mail_kill_rate
 * @property boolean $mail_deadline
 * @property boolean $mail_before_deadline
 * @property boolean $site_transport_create_1
 * @property boolean $site_transport_create_2
 * @property boolean $site_kill_rate
 * @property boolean $site_deadline
 * @property boolean $site_before_deadline
 *
 * The followings are the available model relations:
 * @property User $user
 */
class UserField extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_field';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id', 'numerical', 'integerOnly'=>true),
			//array('mail_transport_create_1, mail_transport_create_2, mail_kill_rate, mail_deadline, mail_before_deadline, site_transport_create_1, site_transport_create_2, site_kill_rate, site_deadline, site_before_deadline', 'safe'),
			array('mail_transport_create_1, mail_transport_create_2, mail_kill_rate, mail_deadline, mail_before_deadline', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, mail_transport_create_1, mail_transport_create_2, mail_kill_rate, mail_deadline, mail_before_deadline', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'mail_transport_create_1' => 'Mail Transport Create 1',
			'mail_transport_create_2' => 'Mail Transport Create 2',
			'mail_kill_rate' => 'Mail Kill Rate',
			'mail_deadline' => 'Mail Deadline',
			'mail_before_deadline' => 'Mail Before Deadline',
			
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('mail_transport_create_1',$this->mail_transport_create_1);
		$criteria->compare('mail_transport_create_2',$this->mail_transport_create_2);
		$criteria->compare('mail_kill_rate',$this->mail_kill_rate);
		$criteria->compare('mail_deadline',$this->mail_deadline);
		$criteria->compare('mail_before_deadline',$this->mail_before_deadline);
		/*$criteria->compare('site_transport_create_1',$this->site_transport_create_1);
		$criteria->compare('site_transport_create_2',$this->site_transport_create_2);
		$criteria->compare('site_kill_rate',$this->site_kill_rate);
		$criteria->compare('site_deadline',$this->site_deadline);
		$criteria->compare('site_before_deadline',$this->site_before_deadline);
*/
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserField the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
