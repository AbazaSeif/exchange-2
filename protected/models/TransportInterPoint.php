<?php

/**
 * This is the model class for table "transport_inter_point".
 *
 * The followings are the available columns in table 'transport_inter_point':
 * @property integer $id
 * @property integer $t_id
 * @property string $point
 * @property string $date
 * @property integer $sort
 *
 * The followings are the available model relations:
 * @property Transport $t
 */
class TransportInterPoint extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'transport_inter_point';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('t_id, sort', 'numerical', 'integerOnly'=>true),
			array('point, date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, t_id, point, date, sort', 'safe', 'on'=>'search'),
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
			't' => array(self::BELONGS_TO, 'Transport', 't_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			't_id' => 'T',
			'point' => 'Point',
			'date' => 'Date',
			'sort' => 'Sort',
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
		$criteria->compare('t_id',$this->t_id);
		$criteria->compare('point',$this->point,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('sort',$this->sort);

		return new CActiveDataProvider($this, array(
		    'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TransportInterPoint the static model class
	 */
	public static function model($className=__CLASS__)
	{
            return parent::model($className);
	}
        
        public static function getPoints($id)
        {
            $points = '';        
            $innerPoints = Yii::app()->db->createCommand()
                ->select('point, date')
                ->from('transport_inter_point')
                ->where('t_id=:id', array(':id'=>$id))
                ->order('date')
                ->queryAll()
            ;

            foreach($innerPoints as $point) {
                if(isset($points)) {
                    $now = date('Y-m-d');
                    if(!empty($point['date']) && strtotime($now) < strtotime($point['date']))
                        $points .= '<span class="point" title="'.date('d.m.Y H:i', strtotime($point['date'])).'"><span class="inner-point">'.$point['point'].'</span></span>';
                    else $points .= '<span class="point" title=""><span class="inner-point">'.$point['point'].'</span></span>';
                }
            }
            
            return $points;
        }
        
        public static function getPointsMin($id)
        {
            $points = '';        
            $innerPoints = Yii::app()->db->createCommand()
                ->select('point')
                ->from('transport_inter_point')
                ->where('t_id=:id', array(':id'=>$id))
                ->order('date')
                ->queryAll()
            ;

            foreach($innerPoints as $point){
                if(isset($points))
                    $points .= ' -> '.$point['point'];
            }
            return $points;
        }
}
