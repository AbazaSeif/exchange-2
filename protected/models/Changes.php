<?php

class Changes extends CActiveRecord
{
    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'changes';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('date', 'safe'),
            array('id, date, user_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'user_id',
			'description' => 'Описание',
			'action_name' => 'Тип изменения',
			'date' => 'Время изменения',
		);
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
