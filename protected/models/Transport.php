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
        CONST RUS_PRICE_STEP = 500;

        public static $group = array(
            0=>'Международная',
            1=>'Региональная',
        );

        public static $currencyGroup = array(
            0=>'Рубли (руб.)',
            1=>'Доллары ($)',
            2=>'Евро (€)',
        );

        public static $status = array(
            0=>'Архивная',
            1=>'Активная',
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
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
                        array('location_from, location_to, description, date_close, date_from, date_to, start_rate', 'required', 'message'=>'Заполните поле "{attribute}"'),
                        array('start_rate', 'numerical', 'integerOnly'=>true, 'min'=>0, 'message'=>'Поле "{attribute}" должно содержать число', 'tooSmall'=>'Значение поля "{attribute}" не может быть меньше нуля !'),
			array('new_transport, rate_id, start_rate, status, type, currency', 'numerical', 'integerOnly'=>true),
			array('t_id, user_id', 'length', 'max'=>64),
			array('location_from, location_to, auto_info, description, date_close, date_from, date_to, date_published', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, t_id, new_transport, rate_id, start_rate, status, type, user_id, currency, location_from, location_to, auto_info, description, date_close, date_from, date_to, date_published', 'safe', 'on'=>'search'),
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
		$criteria->compare('t_id',$this->t_id,true);
		$criteria->compare('new_transport',$this->new_transport);
		$criteria->compare('rate_id',$this->rate_id);
		$criteria->compare('start_rate',$this->start_rate);
		$criteria->compare('status',$this->status);
		$criteria->compare('type',$this->type);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('currency',$this->currency);
		$criteria->compare('location_from',$this->location_from,true);
		$criteria->compare('location_to',$this->location_to,true);
		$criteria->compare('auto_info',$this->auto_info,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('date_close',$this->date_close,true);
		$criteria->compare('date_from',$this->date_from,true);
		$criteria->compare('date_to',$this->date_to,true);
		$criteria->compare('date_published',$this->date_published,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
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
        
        protected function afterSave()
        {
            parent::afterSave();
            $inputArray = $_POST['Rates'];
            $pointsArray = $_POST['Points'];
            $transportId = $_POST['Transport']['id'];
            $transportModel = Transport::model()->findByPk($transportId);

            if (isset($inputArray)) {
                $arrayKeys = array();
                $priceChanges = array();

                // Edit Rates
                foreach($inputArray as $id=>$price) {
                    $arrayKeys[] = $id;
                    $model = Rate::model()->findByPk($id);
                    if(trim($model['price']) != trim($price)) {
                        $priceChanges[$id]['before'] = $model['price'];
                        $priceChanges[$id]['after'] = $price;
                        $model['price'] = $price;
                        $model->save();
                    }
                }

                if(!empty($priceChanges)) {
                    $minRate = Rate::model()->findByPk($transportModel->rate_id);
                    $minPrice = $minRate->price;
                    $message = 'В перевозке "' . $transportModel['location_from'] . ' — ' . $transportModel['location_to'] . '" были изменены следующие ставки: ';
                    $k = 0;

                    foreach($priceChanges as $key => $value){
                        $k++;
                        $message .= $k . ') Ставка с id = '. $key . ' - цена ' . $priceChanges[$key]['before'] . ' на ' . $priceChanges[$key]['after'] . '; ';

                        if($minPrice > $priceChanges[$key]['after']){
                            $transportModel->rate_id = $minRateId;
                            $transportModel->save();
                        }
                    }

                    Changes::saveChange($message);

                }

                $criteria = new CDbCriteria;
                $criteria->addCondition('transport_id = ' . $transportId);
                $criteria->addNotInCondition('id', $arrayKeys);

                // Delete rates and save changes
                Changes::saveChangeInRates($criteria, $transportId);
                // if min rate was delete
                if(!in_array($transportModel['rate_id'], $arrayKeys) || array_key_exists($transportModel->rate_id, $priceChanges)) {
                    $minPrice = Yii::app()->db->createCommand()
                        ->select('min(price) as price')
                        ->from('rate')
                        ->where('transport_id = :id', array(':id' => $transportId))
                        ->group('transport_id')
                        ->queryRow()
                    ;

                    $model = Yii::app()->db->createCommand()
                        ->select('id')
                        ->from('rate')
                        ->where('transport_id = :id and price = :price', array(':id' => $transportId, ':price' => $minPrice['price']))
                        ->queryRow()
                    ;
                    $transportModel['rate_id'] = $model['id'];
                    $transportModel->save();
                }
            }

            if (isset($pointsArray)){
                $arrayKeys = array();
                $pointChanges = array();
                // Edit Rates
                foreach($pointsArray as $id=>$point) {
                    $arrayKeys[] = $id;
                    $model = TransportInterPoint::model()->findByPk($id);
                    if(trim($model['point']) != trim($point)) {
                        $pointChanges[$id]['before'] = $model['point'];
                        $pointChanges[$id]['after'] = $point;
                        $model['point'] = $point;
                        $model->save();
                    }
                }

                if(!empty($pointChanges)){
                    $message = 'В перевозке "' . $transportModel['location_from'] . ' — ' . $transportModel['location_to'] . '" были изменены следующие промежуточные пункты: ';
                    $k = 0;
                    foreach($pointChanges as $key => $value){
                        $k++;
                        $message .= $k . ') Пункт с id = '. $key . ' - пункт "' . $pointChanges[$key]['before'] . '" на "' . $pointChanges[$key]['after'] . '"; ';
                    }
                    Changes::saveChange($message);
                }

                $criteria = new CDbCriteria;
                $criteria->addCondition('t_id = ' . $transportId);
                $criteria->addNotInCondition('id', $arrayKeys);

                // Delete points and save changes
                Changes::saveChangeInPoints($criteria, $transportId);
            }
            return true;
        }
}
