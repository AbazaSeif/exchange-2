<?php

/**
 * This is the model class for table "transport".
 *
 * The followings are the available columns in table 'transport':
 * @property integer $id
 * @property string $t_id
 * @property integer $new_transport
 * @property integer $rate_id
 * @property integer $start_rate
 * @property integer $status
 * @property integer $type
 * @property string $user_id
 * @property integer $currency
 * @property string $location_from
 * @property string $location_to
 * @property string $auto_info
 * @property string $description
 * @property string $date_close
 * @property string $date_from
 * @property string $date_to
 * @property string $date_published
 *
 * The followings are the available model relations:
 * @property Rate[] $rates
 * @property TransportFieldEq[] $transportFieldEqs
 * @property TransportInterPoint[] $transportInterPoints
 * @property UserEvent[] $userEvents
 */
class Transport extends CActiveRecord
{
    CONST INTER_TRANSPORT = 0;
    CONST RUS_TRANSPORT = 1;
    CONST INTER_PRICE_STEP = 50;
    CONST RUS_PRICE_STEP = 200;
    
    public $date_to_customs_clearance_RF;
    public $customs_clearance_RF; // Russian Federation
    public $customs_clearance_EU; // Euro Union
    
   /* public $date_to_customs_clearance_RF;
    public $customs_clearance_RF;
    public $customs_clearance_EU;
    */
    public static $group = array(
        0=>'Международная',
        1=>'Региональная',
    );
    
    public static $currencyGroup = array(
        0=>'Рубли (руб.)',
        1=>'Доллары ($)',
        2=>'Евро (€)',
    );
    
    CONST ACTIVE_TRANSPORT = 1;
    CONST DRAFT_TRANSPORT = 2;
    CONST DEL_TRANSPORT = 3;
    
    public static $status = array(
        0=>'Архивная',
        self::ACTIVE_TRANSPORT=>'Активная',
        self::DRAFT_TRANSPORT=>'Черновик',
    );
    
    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
	    return 'transport';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
            return array(
		array('t_id, pto, del_date, del_reason, location_from, new_transport, rate_id, start_rate, status, type, user_id, currency, customs_clearance_RF, customs_clearance_EU, location_to, auto_info, description, date_to_customs_clearance_RF, date_close, date_from, date_to, date_published', 'safe')
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
			'rates' => array(self::HAS_MANY, 'Rate', 'transport_id'),
			'transportFieldEqs' => array(self::HAS_MANY, 'TransportFieldEq', 'transport_id'),
			'transportInterPoints' => array(self::HAS_MANY, 'TransportInterPoint', 't_id'),
			'userEvents' => array(self::HAS_MANY, 'UserEvent', 'transport_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
                return array(
                    'id' => 'ID',
                    't_id' => 'Id 1c',
                    'rate_id' => 'ID Ставки',
                    'start_rate' => 'Ставка',
                    'status' => 'Статус',
                    'type' => 'Тип перевозки',
                    'user_id' => 'ID пользователя',
                    'location_from' => 'Место загрузки',
                    'location_to' => 'Место разгрузки',
                    'auto_info' => 'Транспорт',
                    'description' => 'Описание',
                    'date_close' => 'Время закрытия заявки',
                    'date_from' => 'Дата загрузки',
                    'date_to' => 'Дата разгрузки',
                    'date_published' => 'Дата публикации',
                    'currency' => 'Валюта',
                    'date_to_customs_clearance_RF' => 'Дата доставки в пункт таможенной очистки в РФ',
                    'customs_clearance_RF' => 'Место таможенной очистки в РФ',
                    'customs_clearance_EU' => 'Место таможенного оформления в ЕС',
                    'pto' => 'Экспорт ПТО',
                    'del_reason' => 'Причина удаления',
                    'del_date' => 'Дата удаления',
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

		$criteria = new CDbCriteria;
		$criteria->compare('id',$this->id);
		$criteria->compare('t_id',$this->t_id,true);
		$criteria->compare('new_transport',$this->new_transport);
		$criteria->compare('rate_id',$this->rate_id);
		$criteria->compare('start_rate',$this->start_rate);
		$criteria->compare('status',$this->status);
		$criteria->compare('type',$this->type);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('currency',$this->currency);
		$criteria->compare('auto_info',$this->auto_info,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('date_close',$this->date_close,true);
		$criteria->compare('date_from',$this->date_from,true);
		$criteria->compare('date_to',$this->date_to,true);
		$criteria->compare('del_date',$this->del_date,true);
		$criteria->compare('date_published',$this->date_published,true);
		$criteria->compare('pto',$this->pto,true);
		$criteria->compare('close_reason',$this->pto,true);
                
                if(Yii::app()->search->prepareSqlite()) {
                    if(!empty($this->location_from))$criteria->addCondition('lower(location_from) like lower("%' . $this->location_from . '%")');
                    if(!empty($this->location_to)) $criteria->addCondition('lower(location_to) like lower("%' . $this->location_to . '%")');
                    if(!empty($this->del_reason)) $criteria->addCondition('lower(del_reason) like lower("%' . $this->del_reason . '%")');
                } else {
                    $criteria->compare('location_from',$this->location_from,true);
		    $criteria->compare('location_to',$this->location_to,true);
		    $criteria->compare('del_reason',$this->del_reason,true);
                }

                $criteria->order = 'del_date DESC';
                        
		return new CActiveDataProvider($this, array(
	            'criteria'=>$criteria,
                    'sort'=>array(
                        'defaultOrder' => 't.del_date',
                        'attributes'=>array('*')
                    )
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Transport the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
