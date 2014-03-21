<?php
    //$lastRate = $this->getPrice($data->rate_id);
    $minPriceVal = $this->getMinPrice($data->id);
    $now = date('m/d/Y H:i:s', strtotime('now'));
    //$end = date('m/d/Y H:i:s', strtotime($data->date_from  . ' -' . Yii::app()->params['hoursBefore'] . ' hours'));
    $end = date('m/d/Y H:i:s', strtotime($data->date_close));
    $action = '/transport/description/id/'. $data->id . '/';
    $status = $data->status;
    $rate = '****';
    
    $currency = ' €';
    $type = 'международная';
    
    $allPoints = TransportInterPoint::getPointsMin($data->id);

    if(!Yii::app()->user->isGuest){
        if(Yii::app()->user->isTransport){
            $model = UserField::model()->find('user_id = :id', array('id' => Yii::app()->user->_id));
            if((bool)$model->with_nds){
                if(!empty($minPriceVal)) $rate = $minPriceVal + $minPriceVal * Yii::app()->params['nds'];
                else $rate = $data->start_rate + $data->start_rate * Yii::app()->params['nds'];
            } else {
                $rate = (!empty($minPriceVal))? $minPriceVal : $data->start_rate;
            }
        } else {
            $rate = (!empty($minPriceVal))? $minPriceVal : $data->start_rate;
        }
        $rate = floor($rate);
    }
    if($data->type==Transport::RUS_TRANSPORT){
        $type = "российская";
    }
    if(!$data->currency){
       $currency = ' руб.';
    } else if($data->currency == 1){
       $currency = ' $';
    }
?>
<div class="transport">
    <div class="width-50">
        <div class="width-100">
            <div class="width-49">
                <a class="t-header" href="<?php echo $action; ?>" >
                    <?php echo $data->location_from ?>
                </a>
            </div>
            <div class="width-49">
                <a class="t-header" href="<?php echo $action; ?>" >
                    <?php echo $data->location_to ?>
                </a>
            </div>
        </div>
        <div class="width-100">
            <div class="width-49">
                <span class="t-d-form-to">Дата загрузки: <?php echo date('d.m.y', strtotime($data->date_from)) ?></span>
            </div>
            <div class="width-49">
                <span class="t-d-form-to">Дата разгрузки: <?php echo date('d.m.y', strtotime($data->date_to)); ?></span>
            </div>
        </div>
        <div class="width-100">
            <div class="t-points"><span><?php echo $data->location_from . $allPoints . ' -> ' . $data->location_to ?></span></div>
        </div>
    </div>
    <div class="width-50">
        <div class="width-40">
            <span><?php echo (!empty($data->description))? $data->description : 'Описание отсутствует' ?></span>
        </div>
        <div class="width-30 v-center">
            <div class="t-rate">
                <span><?php echo $rate.$currency;?></span>
            </div>
        </div>
        <div class="width-30 v-center"> 
            <div class="t-timer" id="counter-<?php echo $data->id; ?>" now="<?php echo $now ?>" end="<?php echo $end ?>" status="<?php echo $status ?>"></div>
        </div>
    </div>
</div>
