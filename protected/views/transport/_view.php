<?php
    $lastRate = $this->getPrice($data->rate_id);
    $now = date('Y m d H:i:s', strtotime('now'));
    $end = date('Y m d H:i:s', strtotime($data->date_from  . ' -' . Yii::app()->params['hoursBefore'] . ' hours'));
    $action = '/transport/description/id/'. $data->id . '/';
    $status = $data->status;
    $rate = '****';
    
    $currency = ' €';
    $type = 'международная';
    
    if(!Yii::app()->user->isGuest){
        $model = UserField::model()->find('user_id = :id', array('id' => Yii::app()->user->_id));   
        
        if((bool)$model->with_nds){
            if(!empty($lastRate)) $rate = $lastRate + $lastRate * Yii::app()->params['nds'];
            else $rate = $data->start_rate + $data->start_rate * Yii::app()->params['nds'];
        } else {
            $rate = (!empty($lastRate))? $lastRate : $data->start_rate;
        }
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
    <div class="width-25 description">
        <span><?php echo $data->description ?></span>
    </div>
    <div class="width-20">
        <a class="t-header" href="<?php echo $action; ?>" >
            <?php echo $data->location_from ?>
        </a>
        <span class="t-d-form-to">Дата загрузки: <?php echo date('d.m.y', strtotime($data->date_from)) ?></span>
    </div>
    <div class="width-5">
        <img src="/images/arrow.jpg" width="20px">
    </div>
    <div class="width-20">
        <a class="t-header" href="<?php echo $action; ?>" >
            <?php echo $data->location_to ?>
        </a>
        <span class="t-d-form-to">Дата разгрузки: <?php echo date('d.m.y', strtotime($data->date_to)); ?></span>
    </div>
    <div class="width-15">
        <div class="t-rate">
            <span><?php echo $rate.$currency;?></span>
        </div>
    </div>    
    <div class="width-15"> 
        <div class="t-timer" id="counter-<?php echo $data->id; ?>" now="<?php echo $now ?>" end="<?php echo $end ?>" status="<?php echo $status ?>"></div>
    </div>
</div>

