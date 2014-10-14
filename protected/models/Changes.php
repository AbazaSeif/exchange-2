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
           //'user' => array(self::BELONGS_TO, 'User', 'user_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
           'id' => 'ID',
           'user_id' => 'user_id',
           'description' => 'Описание изменений',
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
        //AuthUser
        $change['user_id'] = Yii::app()->user->_id;
        $change['date'] = date('Y-m-d H:i:s');
        $change['description'] = $message;
        $change->save();
        return;
    }
    
    // Delete rates and save changes
    public static function saveChangeInRates($criteria, $transportId, $arrayKeys = array(), $priceChanges = array())
    {
        $ratesForDelete = Rate::model()->findAll($criteria);
        // Delete Rates
        $transportModel = Transport::model()->findByPk($transportId);
        if(!empty($ratesForDelete)) {
            
            //Rate::model()->deleteAll($criteria);
            $message = 'В перевозке "' . $transportModel->location_from . ' — ' . $transportModel->location_to . '" были удалены следующие ставки: ';
            $k = 0;
            
            foreach($ratesForDelete as $i=>$rate) {
                $k++;
                $message .= $k . ') Ставка с id = '. $rate->id . ' - цена ' . $rate->price . '; ';
            
                Rate::model()->deleteByPk($rate->id);
            }
            Changes::saveChange($message);
            return;
        }
        if(!in_array($transportModel->rate_id, $arrayKeys) || array_key_exists($transportModel->rate_id, $priceChanges)) {
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
    
    public static function saveChangeInPoints($criteria, $transportId)
    {
        $pointsForDelete = TransportInterPoint::model()->findAll($criteria);
        // Delete Points
        if(!empty($pointsForDelete)){
            $transportModel = Transport::model()->findByPk($transportId);
            //TransportInterPoint::model()->deleteAll($criteria);
            $message = 'В перевозке "' . $transportModel->location_from . ' — ' . $transportModel->location_to . '" были удалены следующие пункты: ';
            $k = 0;
            foreach($pointsForDelete as $i=>$point) {
                $k++;
                $message .= $k . ') Пункт с id = '. $point->id . ' "' . $point->point . '"; ';
            
                TransportInterPoint::model()->deleteByPk($point->id);
            }
            Changes::saveChange($message);
            return;
        }
    }
}
