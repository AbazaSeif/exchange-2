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
    
    public static function saveChange($message)
    {
        $change = new Changes();
        $change['user_id'] = Yii::app()->user->_id;
        $change['date'] = date('Y-m-d H:i:s');
        $change['description'] = $message;
        $change->save();
        return;
    }
    
    // Delete rates and save changes
    public static function saveChangeInRates($criteria)
    {
        $ratesForDelete = Rate::model()->findAll($criteria);
        // Delete Rates
        if(!empty($ratesForDelete)){
            Rate::model()->deleteAll($criteria);
            $message = 'В перевозке "' . $transportModel['location_from'] . ' — ' . $transportModel['location_to'] . '" были удалены следующие ставки: ';
            $k = 0;
            foreach($ratesForDelete as $i=>$rate) {
                $k++;
                $message .= $k . ') Ставка с id = '. $rate['id'] . ' - цена ' . $rate['price'] . '; ';
            
                Rate::model()->deleteByPk($rate['id']);
            }
            Changes::saveChange($message);
            return;
        }
    }
}
