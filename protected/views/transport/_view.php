<?php
    $lastRate = $this->getPrice($data->rate_id);
    $now = date('Y/m/d H:i:s', strtotime('now'));
    $end = date('Y/m/d H:i:s', strtotime($data->date_from  . ' -' . Yii::app()->params['hoursBefore'] . ' hours'));
    $action = '/transport/description/id/'. $data->id . '/';
    $status = $data->status;
    $rate = '****';
    
    $currency = ' €';
    $type = 'международная';
    
    $allPoints = TransportInterPoint::getPoints($data->id);//$this->getPoints($data->id);

    if(!Yii::app()->user->isGuest){
        if(Yii::app()->user->isTransport){
            $model = UserField::model()->find('user_id = :id', array('id' => Yii::app()->user->_id));
            if((bool)$model->with_nds){
                if(!empty($lastRate)) $rate = $lastRate + $lastRate * Yii::app()->params['nds'];
                else $rate = $data->start_rate + $data->start_rate * Yii::app()->params['nds'];
            } else {
                $rate = (!empty($lastRate))? $lastRate : $data->start_rate;
            }
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
    <?php if($allPoints):?>
        <div class="width-19 description">
    <?php else: ?>
        <div class="width-19 description-50">
    <?php endif; ?>
        <span><?php echo $data->description ?></span>
    </div>
    <div class="width-47">
        <div class="t-wrapper">
            <div class="width-45">
                <a class="t-header" href="<?php echo $action; ?>" >
                    <?php echo $data->location_from ?>
                </a>
                <span class="t-d-form-to">Дата загрузки: <?php echo date('d.m.y', strtotime($data->date_from)) ?></span>
            </div>
            <div class="width-5">
                <img src="/images/arrow.jpg" width="20px">
            </div>
            <div class="width-50">
                <a class="t-header" href="<?php echo $action; ?>" >
                    <?php echo $data->location_to ?>
                </a>
                <span class="t-d-form-to">Дата разгрузки: <?php echo date('d.m.y', strtotime($data->date_to)); ?></span>
            </div>
        </div>
        <?php if($allPoints):?>
            <div class="t-points"><span><?php echo $data->location_from . $allPoints . ' -> ' . $data->location_to ?></span></div>
        <?php endif; ?>
    </div>
    <!--div style="">
        <div style="">
            <div class="width-20">
                <a class="t-header" href="<?php echo $action; ?>" >
                    <?php echo $data->location_from ?>
                </a>
                <span class="t-d-form-to">Дата загрузки: <?php echo date('d.m.y', strtotime($data->date_from)) ?></span>
            </div>
            <div class="width-5">
                <img src="/images/arrow.jpg" width="20px">
            </div>
            <div class="width-22">
                <a class="t-header" href="<?php echo $action; ?>" >
                    <?php echo $data->location_to ?>
                </a>
                <span class="t-d-form-to">Дата разгрузки: <?php echo date('d.m.y', strtotime($data->date_to)); ?></span>
            </div>
        </div>
        <div style="padding-top: 5px; width: 40%; float: left">jjjj
        </div>
    </div-->
    <div class="width-19">
        <div class="t-rate">
            <span><?php echo $rate.$currency;?></span>
        </div>
    </div>    
    <div class="width-15"> 
        <div class="t-timer" id="counter-<?php echo $data->id; ?>" now="<?php echo $now ?>" end="<?php echo $end ?>" status="<?php echo $status ?>"></div>
    </div>
</div>

