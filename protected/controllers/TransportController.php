<?php
class TransportController extends Controller
{
    public function actionI()
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

        $this->render('user.views.transport.view', array('data' => $dataProvider, 'title'=>'Все перевозки'));
    }

    public function actionDescription($id)
    {
        $transportInfo=Yii::app()->db->createCommand("SELECT * from transport where id='".$id."'")->queryRow();
        $allRatesForTransport = Yii::app()->db->createCommand()
            ->select('r.date, r.price, u.name')
            ->from('rate r')
            ->join('user u', 'r.user_id=u.id')
            ->where('r.transport_id=:id', array(':id'=>$id))
            ->order('r.date desc')
            ->queryAll()
        ;
        $this->render('user.views.transport.item', array('rateData' => $dataProvider, 'transportInfo' => $transportInfo));
    }

    /* Ajax update rate for current transport */
    public function actionUpdateRates()
    {
        $id = $_POST['id'];
        $price = '';
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
        
        if(count($data)) {
            $sql = 'select price from rate where transport_id = '.$id.' group by transport_id order by date desc limit 1';
            $price = Yii::app()->db->createCommand($sql)->queryScalar();
            if(($price - $priceStep) <= 0) {
                Transport::model()->updateByPk($id, array('status'=>0));
            }
        }

        $array = array('price'=>$price, 'all'=>$data, 'error' => $error);
        echo json_encode($array);
    }
	
    public function addFormat($date)
    {
        if((int)$date < 10) $date = '0' . $date;
            return $date;
    }

    /* Get latest price for current transport */
    public function getPrice($id)
    {
        $row = Yii::app()->db->createCommand()
            ->select()
            ->from('rate')
            ->where('id = :id', array(':id' => $id))
            ->queryRow()
        ;
        return $row['price'];
    }
	
    // Send mail to user if his rate was killed
    public function mailKillRate($rateId, $rateModel)
    {
        $users = array();
        $temp = Yii::app()->db->createCommand()
                ->select('user_id')
                ->from('user_field')
                ->where('mail_kill_rate = :type', array(':type' => true))
                ->queryAll()
        ;
        foreach($temp as $t){
                $users[] = $t['user_id'];
        }

        if(in_array($rateModel->user_id, $users)){
            $userModel = User::model()->findByPk($rateModel->user_id);
            $transportModel = Transport::model()->findByPk($rateModel->transport_id);
            $email = new TEmail;
            $email->from_email = Yii::app()->params['adminEmail'];
            $email->from_name  = 'Биржа перевозок ЛБР АгроМаркет';
            $email->to_email   = $userModel->email;
            $email->to_name    = '';
            $email->subject    = 'Уведомление';
            $email->type = 'text/html';
            $email->body = '<h1>Уважаемый(ая) ' . $userModel->name . ' ' . $userModel->surname .',</h1>
              <div>
                  <p>Вашу ставку для перевозки "<a href="http://exchange.lbr.ru/transport/description/id/'.$rateModel->transport_id.'">' . $transportModel->location_from . ' &mdash; ' . $transportModel->location_to . '</a>" перебили. </p>
              </div>
              <h5>Это автоматическое уведомление, на него не следует отвечать.</h5>
            ';
            $email->sendMail();
        }
    }
    
    public function siteKillRate($rateId, $rateModel)
    {
        $users = array();		
        $temp = Yii::app()->db->createCommand()
            ->select('user_id')
            ->from('user_field')
            ->where('site_kill_rate = :type', array(':type' => true))
            ->queryAll()
        ;
        foreach($temp as $t){
            $users[] = $t['user_id'];
        }

        if(in_array($rateModel->user_id, $users)){
            $obj = array(
                'user_id' => $rateModel->user_id,
                'transport_id' => $rateModel->transport_id,
                'status' => 1,
                'type' => 1,
                'event_type' => 5,
            );

            Yii::app()->db->createCommand()->insert('user_event',$obj);
        }
    }
}