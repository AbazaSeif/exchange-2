<?php

class TransportController extends Controller
{
    public function actionIndex()
    {
        $lastRates = array();        
        $criteria = new CDbCriteria();
        $dataProvider = new CActiveDataProvider('Transport',
            array(
                'criteria' => $criteria,
                'pagination'=>array(
                   'pageSize' => 8,
                   'pageVar' => 'page',
                ),
                'sort'=>array(
                    'attributes'=>array(
                        'date_from'=>array(
                            'asc'=>'status ASC',
                            'desc'=>'status DESC',
                            'default'=>'desc',
                        ),
                        'date_to'=>array(
                            'asc'=>'status ASC',
                            'desc'=>'status DESC',
                            'default'=>'desc',
                        ),
                        'date_published'=>array(
                            'asc'=>'date_published ASC',
                            'desc'=>'date_published DESC',
                            'default'=>'desc',
                        )
                    ),
                    'defaultOrder'=>array(
                            'date_published' => CSort::SORT_DESC,
                    ),
                ),
            )
        );

        $this->render('view', array('data' => $dataProvider, 'title'=>'Все перевозки'));
    }
    
    public function actionAll()
    {
        $transportId = array();
        $temp = Yii::app()->db->createCommand()
            ->selectDistinct('transport_id')
            ->from('rate')
            ->where('user_id = :id', array(':id' => Yii::app()->user->_id))
            ->queryAll()
        ;

        foreach($temp as $t){
            $transportId[] = $t['transport_id'];
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $transportId);

        $dataProvider = new CActiveDataProvider('Transport',
            array(
                'criteria' => $criteria,
                'pagination'=>array(
                 'pageSize' => 8,
                 'pageVar' => 'page',
                ),
                'sort' => array(
                    'attributes'=>array(
                        'date_from'=>array(
                        'asc'=>'status ASC',
                        'desc'=>'status DESC',
                        'default'=>'desc',
                        ),
                        'date_to'=>array(
                            'asc'=>'status ASC',
                            'desc'=>'status DESC',
                            'default'=>'desc',
                        ),
                        'date_published'=>array(
                            'asc'=>'date_published ASC',
                            'desc'=>'date_published DESC',
                            'default'=>'desc',
                        )
                    ),
                    'defaultOrder'=>array(
                        'date_published' => CSort::SORT_DESC,
                    ),
                ),
            )
        );

        $this->render('view', array('data' => $dataProvider, 'title'=>'Все перевозки'));
    }
    
    /* Show all transports where user takes part */
    public function actionActive()
    {
        $transportId = array();
        $temp = Yii::app()->db->createCommand()
            ->selectDistinct('transport_id')
            ->from('rate')
            ->where('user_id = :id', array(':id' => Yii::app()->user->_id))
            ->queryAll()
        ;

        foreach($temp as $t){
            $transportId[] = $t['transport_id'];
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $transportId);
        $criteria->compare('status', 1);

        $dataProvider = new CActiveDataProvider('Transport',
            array(
                'criteria' => $criteria,
                'pagination'=>array(
                 'pageSize' => 8,
                 'pageVar' => 'page',
                ),
                'sort' => array(
                    'attributes'=>array(
                            'date_from'=>array(
                            'asc'=>'status ASC',
                            'desc'=>'status DESC',
                            'default'=>'desc',
                        ),
                        'date_to'=>array(
                            'asc'=>'status ASC',
                            'desc'=>'status DESC',
                            'default'=>'desc',
                        ),
                        'date_published'=>array(
                            'asc'=>'date_published ASC',
                            'desc'=>'date_published DESC',
                            'default'=>'desc',
                        )
                    ),
                    'defaultOrder'=>array(
                            'date_published' => CSort::SORT_DESC,
                    ),
                ),
            )
        );

        $this->render('view', array('data' => $dataProvider, 'title'=>'Активные перевозки'));
    }
    
    /*
    * Show all transports where user took part
    * parameter $s shows that user won this transport
    */
    public function actionArchive($s = null)
    {
        $userId = Yii::app()->user->_id;
        $transportId = $rateId = $rateIdWin = $rateIdLose = array();
        $temp = Yii::app()->db->createCommand()
            ->selectDistinct('transport_id')
            ->from('rate')
            ->where('user_id = :id', array(':id' => $userId))
            ->queryAll()
        ;
        
        foreach($temp as $t){
            $transportId[] = $t['transport_id'];
        }
        // all rates where user took part
        $temp = Yii::app()->db->createCommand()
            ->select('id')
            ->from('rate')
            ->where('user_id = :id', array(':id' => $userId))
            ->queryAll()
        ;
        
        foreach($temp as $t){
            $rateId[] = $t['id'];
        }

        // all win rates
        $temp = Yii::app()->db->createCommand()
            ->select('rate_id')
            ->from('transport')
            ->where('status = :status', array(':status' => 0))
            ->queryAll()
        ;
        foreach($temp as $t){
            $rateIdWin[] = $t['rate_id'];
        }
        $intersectRates = array_intersect($rateId, $rateIdWin);
        
        $criteria = new CDbCriteria();
        if(isset($s)) {
            $criteria->addInCondition('rate_id', $intersectRates);
        } else {
            // all lose rates
            $temp = Yii::app()->db->createCommand()
                ->selectDistinct('transport_id')
                ->from('rate')
                ->where(array('in', 'id', array_diff($rateId, $rateIdWin)))
                ->queryAll()
            ;
            foreach($temp as $t){
                $rateIdLose[] = $t['transport_id'];
            }
            
            $criteria->addInCondition('id', $rateIdLose); 
            $criteria->addNotInCondition('rate_id', $intersectRates);
        }
        $criteria->compare('status', 0);
        
        $dataProvider = new CActiveDataProvider('Transport',
            array(
                'criteria' => $criteria,
                'pagination'=>array(
                 'pageSize' => 8,
                 'pageVar' => 'page',
                ),

                'sort' => array(
                    'attributes'=>array(
                            'date_from'=>array(
                            'asc'=>'status ASC',
                            'desc'=>'status DESC',
                            'default'=>'desc',
                        ),
                        'date_to'=>array(
                            'asc'=>'status ASC',
                            'desc'=>'status DESC',
                            'default'=>'desc',
                        ),
                        'date_published'=>array(
                            'asc'=>'date_published ASC',
                            'desc'=>'date_published DESC',
                            'default'=>'desc',
                        )
                    ),
                    'defaultOrder'=>array(
                            'date_published' => CSort::SORT_DESC,
                    ),
                ),
            )
        );
                
        $this->render('view', array('data' => $dataProvider, 'title'=>'Архивные перевозки'));
    }
    
    public function actionDescription($id)
    {
        $transportInfo=Yii::app()->db->createCommand("SELECT * from transport where id='".$id."'")->queryRow();
        $dataProvider = Yii::app()->db->createCommand()
            ->select('r.date, r.price, u.name')
            ->from('rate r')
            ->join('user u', 'r.user_id=u.id')
            ->where('r.transport_id=:id', array(':id'=>$id))
            ->order('r.date desc')
            ->queryAll()
        ;
        $this->render('item', array('rateData' => $dataProvider, 'transportInfo' => $transportInfo));
    }
    
    /* Ajax update rate for current transport */
    public function actionUpdateRates()
    {
        $id = $_POST['id'];
        $newPrice = $_POST['newRate'];
        $priceStep = $_POST['step'];
        $error = 0;
        
        if($newPrice) {
            $elementExitsts = Rate::model()->find(array(
                'condition'=>'price = :price AND transport_id = :id',
                'params'=>array(':price' => (int)$newPrice, ':id' => $id),
            ));
            
            if(empty($elementExitsts)) {
                $obj = array(
                    'transport_id'  => $id,
                    'user_id' => Yii::app()->user->_id,
                    'date'    => date("Y-m-d H:i:s"),
                    'price'   => (int)$newPrice
                );

                $modelRate = new Rate;
                $modelRate->attributes = $obj;
                $modelRate->save();

                $model = Transport::model()->findByPk($id);
                $rateId = $model->rate_id;

                // send mail
                if(!empty($rateId)){ // empty when don't have rates
                    $rateModel = Rate::model()->findByPk($rateId);
                    $this->mailKillRate($rateId, $rateModel);
                    $this->siteKillRate($rateId, $rateModel);
                }

                $model->rate_id = $modelRate->id;
                $model->save();
            } else {
                $error = 1;
            }
        }
        
        $sql = 'select price from rate where transport_id = '.$id.' group by transport_id order by date desc limit 1';
        $price = Yii::app()->db->createCommand($sql)->queryScalar();
        if(($price - $priceStep) <= 0) {
            Transport::model()->updateByPk($id, array('status'=>0));
        }
        $data = Yii::app()->db->createCommand()
            ->select('r.*, u.name, u.surname')
            ->from('rate r')
            ->join('user u', 'r.user_id=u.id')
            ->where('r.transport_id=:id', array(':id'=>$id))
            ->order('r.date asc')
            ->queryAll()
        ;
        foreach($data as $k=>$v){
           $data[$k]['time']=date('d.m.Y H:i:s', strtotime($v['date']));
        }

        $array = array('price'=>$price, 'all'=>$data, 'error' => $error);
        echo json_encode($array);
    }
        
    /* Get latest price for current transport */
    public function getPrice($id)
    {
        $row = Yii::app()->db->createCommand()
            ->select('price')
            ->from('rate')
            ->where('id = :id', array(':id' => $id))
            ->queryRow()
        ;
        
        return $row['price'];
    }
}