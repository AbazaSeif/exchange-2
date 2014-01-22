<?php
$lastRate = '';
$currency = ' €';
$priceStep = Transport::INTER_PRICE_STEP;
if($transportInfo['type']==Transport::RUS_TRANSPORT){
    $currency = ' руб.';
    $priceStep = Transport::RUS_PRICE_STEP; 
}
if (!empty($transportInfo['rate_id'])) $lastRate = $this->getPrice($transportInfo['rate_id']);
else $lastRate = $transportInfo['start_rate'];
//if (!$lastRate) $lastRate = $transportInfo['start_rate'];
//else $lastRate -= $priceStep;

$now = date('Y m d H:i:s', strtotime('now'));
$end = date('Y m d H:i:s', strtotime($transportInfo['date_to'] . ' -' . Yii::app()->params['hoursBefore'] . ' hours'));

?>

<div class="transport-one">
    <div class="width-70">
        <h1><?php echo $transportInfo['location_from'] . ' &mdash; ' . $transportInfo['location_to']; ?></h1>
        <span class="t-o-published">Опубликована <?php echo date('d.m.Y H:i', strtotime($transportInfo['date_published'])) ?></span>
        <div class="t-o-info">
            <label class="r-header">Основная информация</label>
            <span><i class="r-description"><?php echo $transportInfo['description'] ?></i></span>
            <div><span>Пункт отправки: </span><strong><?php echo $transportInfo['location_from'] ?></strong></div>
            <span>Пункт назначения: </span> <strong><?php echo $transportInfo['location_to'] ?></strong>
            <span>Дата загрузки: </span><strong><?php echo date('d.m.Y', strtotime($transportInfo['date_from'])) ?></strong>
            <span>Дата разгрузки: </span><strong><?php echo date('d.m.Y', strtotime($transportInfo['date_to'])) ?></strong>
            <?php if (!Yii::app()->user->isGuest): ?><span>Текущая ставка:</span><strong id="last-rate"><?php echo $lastRate ?></strong><?php endif; ?>
        </div>	
    </div>
    <div class="width-30" style="height: 100%">
        <div id="timer"></div>
        <div class="rate-btns">
			<input id="rate-price" init="<?php echo ($lastRate<0)? ($lastRate + $priceStep) : $lastRate; ?>" type="text" size="1" value="<?php echo ($lastRate<0)? ($lastRate + $priceStep) : $lastRate; ?>" disabled="<?php echo ($lastRate<0)? 'disabled' : '' ?>"/>
			<div id="rate-up" class="disabled"></div>
			<div id="rate-down" class="<?php echo (($lastRate - $priceStep)<0)?'disabled':''?>"></div>
		</div>
		<div id="rate-btn" class="btn-green btn big  <?php echo (($lastRate - $priceStep)<0)?'disabled':'' ?>">ОK</div>
    </div>
    <div class="clear"></div>
    
    <?php if (!Yii::app()->user->isGuest): ?>
        <div id="rates"></div>
    <?php endif; ?>
</div>
<script>
$(document).ready(function(){
    var timer = new Timer();
    timer.init('<?php echo $now ?>', '<?php echo $end ?>', 'timer');
    
    rateList.data = {
        currency : ' <?php echo $currency ?>',
        priceStep : <?php echo $priceStep ?>,
        transportId : <?php echo $transportInfo['id']; ?>
    };
	rateList.init();
	setInterval(function(){rateList.update($('#rates'))}, 15000);
});
</script>