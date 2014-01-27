<?php
$lastRate = null;
$currency = ' €';
$priceStep = Transport::INTER_PRICE_STEP;
if($transportInfo['type']==Transport::RUS_TRANSPORT){
    $currency = ' руб.';
    $priceStep = Transport::RUS_PRICE_STEP; 
}
$defaultRate = false;
if (!empty($transportInfo['rate_id'])) {
    $lastRate = $this->getPrice($transportInfo['rate_id']);
} else {
    $lastRate = $transportInfo['start_rate'];
    $defaultRate = true;
}
$startValue = ($defaultRate)? $lastRate : ($lastRate - $priceStep);
$minRate = (($startValue - $priceStep)<=0)? 1 : 0;
$now = date('Y m d H:i:s', strtotime('now'));
$end = date('Y m d H:i:s', strtotime($transportInfo['date_to'] . ' -' . Yii::app()->params['hoursBefore'] . ' hours'));
$inputSize = strlen((string)$lastRate)-1;
?>

<div class="transport-one">
    <div class="width-60">
        <h1><?php echo $transportInfo['location_from'] . ' &mdash; ' . $transportInfo['location_to']; ?></h1>
        <span class="t-o-published">Опубликована <?php echo date('d.m.Y H:i', strtotime($transportInfo['date_published'])) ?></span>
        <div class="t-o-info">
            <label class="r-header">Основная информация</label>
            <div class="r-description"><i><?php echo $transportInfo['description'] ?></i></div>
            <div><span>Пункт отправки: </span><strong><?php echo $transportInfo['location_from'] ?></strong></div>
            <div><span>Пункт назначения: </span> <strong><?php echo $transportInfo['location_to'] ?></strong></div>
            <div><span>Дата загрузки: </span><strong><?php echo date('d.m.Y', strtotime($transportInfo['date_from'])) ?></strong></div>
            <div><span>Дата разгрузки: </span><strong><?php echo date('d.m.Y', strtotime($transportInfo['date_to'])) ?></strong></div>
            <?php if (!empty($transportInfo['auto_info'])):?><div><span>Информация о машине: </span><strong><?php echo $transportInfo['auto_info'] ?></strong></div><?php endif; ?>
            <?php if (!Yii::app()->user->isGuest): ?><div><span>Текущая ставка:   <strong id="last-rate"><?php echo $lastRate . $currency?></strong></span></div><?php endif; ?>
        </div>	
    </div>
    <div class="width-30 shadow timer-wrapper">
        <div id="timer"></div>
        <div id="t-error"></div>
        <?php if (!Yii::app()->user->isGuest && $startValue > 0 && Yii::app()->user->checkAccess('transport') && !Yii::app()->user->isRoot): ?>
        <div id="t-error"></div>
        <div class="rate-btns">
            <div class="rate-btns-wrapper">
                <div id="rate-up" class="disabled"></div>
                <div id="rate-down" class="<?php echo ($minRate)?'disabled':''?>"></div>
            </div>
            <input id="rate-price" value="<?php echo $startValue?>" init="<?php echo $startValue?>" type="text" size="<?php echo $inputSize ?>" disabled="disabled"/>
		</div>
		<div id="rate-btn" class="btn-green btn  <?php echo ($startValue <= 0)?'disabled':'' ?>">ОK</div>
        <?php endif; ?>
    </div>
</div>
<?php if (!Yii::app()->user->isGuest): ?>
        <div id="rates" class="shadow"></div>
<?php endif; ?>
<script>
$(document).ready(function(){
    rateList.data = {
        currency : ' <?php echo $currency ?>',
        priceStep : <?php echo $priceStep ?>,
        transportId : <?php echo $transportInfo['id']; ?>,
        status: <?php echo $transportInfo['status'] ?>,
        step: <?php echo $priceStep ?>,
    };
    rateList.init();
    setInterval(function(){rateList.update($('#rates'))}, 15000);
    
    var timer = new Timer();
    timer.init('<?php echo $now ?>', '<?php echo $end ?>', 'timer', rateList.data.status);
});
</script>