<?php
class Changes extends CActiveRecord
{
    public $user;
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
           array('id, date, description, user', 'safe'),
           //array('id, date, user_id', 'safe', 'on'=>'search'),
           array('id, date, description, user', 'safe', 'on'=>'search'),
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
    
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;
        $criteria->compare('id',$this->id);
        if(!empty($this->user)) {
            $user = Yii::app()->db_auth->createCommand()
                ->select('login')
                ->from('user')
                ->where('id = '.trim($this->user))
                ->queryRow()
            ;
            $criteria->compare('user', $this->user);
            $criteria->addCondition('user like "'.$user['login'].'%"', 'OR');
        } else $criteria->compare('user_id', $this->user);
        
        if(Yii::app()->search->prepareSqlite()) {
            if(!empty($this->description))$criteria->addCondition('lower(description) like lower("%' . $this->description . '%")');
            if(!empty($this->date))$criteria->addCondition('lower(date) like lower("%' . $this->date . '%")');
        } else {
            $criteria->compare('description',$this->description,true);
            $criteria->compare('date',$this->date,true);
        }

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>12
            ),
            'sort' => array(
                'defaultOrder' => 'date DESC',
            ),
        ));
    }
    
    public static function getAuthUser($id){
        if (isset($id)) {

            if(is_numeric($id)) {
                $sql = "SELECT surname, name, secondname FROM user WHERE id=".$id.";";   
            } else {
                $sql = "SELECT surname, name, secondname FROM user WHERE login = '".trim($id)."';";
            }

            $result = Yii::app()->db_auth->createCommand($sql)->queryRow();
            if(!$result) $userName = $id;
            else $userName = $result['surname'].' '.$result['name'].' '.$result['secondname'];

            return $userName;
        } else {
            return false;
        }
    }
}
