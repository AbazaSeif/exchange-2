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
if (!Yii::app()->user->isGuest) $userInfo = User::model()->findByPk(Yii::app()->user->_id);
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
        </div>	
    </div>
    <?php if (!Yii::app()->user->isGuest && $startValue > 0 && Yii::app()->user->checkAccess('transport') && !Yii::app()->user->isRoot): ?>
    <div class="width-30-r timer-wrapper">
        <div id="t-container"></div>
        <?php if($transportInfo['status']): ?>
        <div id="t-error"></div>
        
        <div class="rate-wrapper">
            <div class="r-block">
                <div class="rate-btns-wrapper">
                    <div id="rate-up" class="disabled"></div>
                    <div id="rate-down" class="<?php echo ($minRate)?'disabled':''?>"></div>
                </div>
                <span class="text"><?php echo $currency ?></span>
                <input id="rate-price" value="<?php echo $startValue?>" init="<?php echo $startValue?>" type="text" size="<?php echo $inputSize ?>" disabled="disabled"/>
            </div>
            <div class="r-submit <?php echo ($startValue <= 0)?'disabled':'' ?>"><span>OK</span></div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php if (Yii::app()->user->isGuest): ?>
         <div class="width-30 timer-wrapper">
             <div id="t-container"></div>
             <div id="last-rate"><span><?php echo '****' . $currency?></span></div>
         </div>
    <?php elseif(Yii::app()->user->isRoot): ?>
        <div class="width-30 timer-wrapper">
             <div id="t-container"></div>
             <div id="last-rate"><span><?php echo $startValue . ' ' . $currency?></span></div>
        </div>  
    <?php endif; ?>
</div>
<?php if (!Yii::app()->user->isGuest): ?>
        <div id="rates"></div>
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
    <?php if (!Yii::app()->user->isGuest): ?>
        rateList.data.name = '<?php echo $userInfo[name] ?>',
        rateList.data.surname = '<?php echo $userInfo[surname] ?>',
    <?php endif;?> 
    rateList.init();
    setInterval(function(){rateList.update($('#rates'))}, 15000);
    
    var timer = new Timer();
    timer.init('<?php echo $now ?>', '<?php echo $end ?>', 't-container', rateList.data.status);
});
</script>