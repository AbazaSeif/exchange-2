<?php
    $color = $data->status ? 'open' : 'close';
    $lastRate = $this->getPrice($data->rate_id);
    $now = date('Y m d H:i:s', strtotime('now'));
    $end = date('Y m d H:i:s', strtotime($data->date_to));
    $status = $data->status;
    $_rate = '****';
    if(!Yii::app()->user->isGuest){
        $_rate = (!empty($lastRate))? $lastRate : $data->start_rate;
    }
    $currency = ' €';
    $type = 'международная';
    if($data->type==Transport::RUS_TRANSPORT){
        $currency = ' руб.';
        $type = "российская";
    }
	$action = '/user/transport/description/id/'. $data->id . '/';
?>
<div class="transport <?php echo $color;?>">
    <div class="width-70">
        <a class="t-header" href="<?php echo $action; ?>" >
            <?php echo $data->location_from . ' &mdash; ' . $data->location_to; ?>
        </a>
        <div class="t-date">
            <span class="t-d-type">Тип: <?php echo $type; ?></span>
            <span class="t-d-form-to">Дата: с <?php echo date('d.m.y', strtotime($data->date_from)).' по '.date('d.m.y', strtotime($data->date_to)); ?></span>
            <span class="t-d-published">Опубликовано: <?php echo date('d.m.y', strtotime($data->date_published)); ?></span>
        </div>
    </div>
    <div class="width-15">
        <div class="t-rate">
            <span><?php echo $_rate.$currency;?></span>
        </div>
    </div>
    <div class="width-15"> 
        <div class="t-timer" id="counter-<?php echo $data->id; ?>" now="<?php echo $now ?>" end="<?php echo $end ?>" status="<?php echo $status ?>"></div>
    </div>
</div>
