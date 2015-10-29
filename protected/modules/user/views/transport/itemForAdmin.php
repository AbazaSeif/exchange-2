<?php
/*$showAdditionalTimer = false;
$showDescription = false;
if($transportInfo['status'] || !Yii::app()->user->isTransport) $showDescription = true;
else {
    $allUsers = array();
    $participants = Yii::app()->db->createCommand()
        ->selectDistinct('user_id')
        ->from('rate')
        ->where('transport_id = :id', array(':id' => $transportInfo['id']))
        ->queryAll()
    ;
    foreach($participants as $user){
        $allUsers[] = $user['user_id'];
    }
    if(in_array(Yii::app()->user->_id, $allUsers)) $showDescription = true;
}

if($showDescription):
$maxRateValue = $transportInfo['start_rate'];
$minRateValue = null;
$currency = '€';
$defaultRate = false;
$priceStep = Transport::INTER_PRICE_STEP;
$now = date('m/d/Y H:i:s');
$end = date('m/d/Y H:i:s', strtotime($transportInfo['date_close']));

if($end < $now && $transportInfo['status']) {
    if(!empty($transportInfo['date_close_new'])) {
        $end = date('m/d/Y H:i:s', strtotime($transportInfo['date_close_new']));
        if($end > $now) $showAdditionalTimer = true;
    }    
}

$winRate = Rate::model()->findByPk($transportInfo['rate_id']);                
$winFerryman = User::model()->findByPk($winRate->user_id);
$winFerrymanShowNds = UserField::model()->findByAttributes(array('user_id'=>$winRate->user_id));
$showWithNds = '';

$allPoints = TransportInterPoint::getPoints($transportInfo['id'], $transportInfo['location_to']);

if(!$transportInfo['currency']){
    $priceStep = Transport::RUS_PRICE_STEP; 
}

if(!$transportInfo['currency']){
   $currency = 'руб.';
} else if($transportInfo['currency'] == 1){
   $currency = '$';
}

if (!empty($transportInfo['rate_id'])) {
    $minRateValue = $this->getMinPrice($transportInfo['id']);
} else {
    $minRateValue = $transportInfo['start_rate'];
    $defaultRate = true;
}

if($winFerrymanShowNds->with_nds && $transportInfo['type'] == Transport::RUS_TRANSPORT) {
    $price = ceil($winRate->price + $winRate->price * Yii::app()->params['nds']);
    if($price%10 != 0) $price -= $price%10;
    $showWithNds = ' (с НДС: ' . $price . ' ' . $currency . ') ' . $winFerryman->company;    
} else if(!$defaultRate) {
    $showWithNds = $winFerryman->company;    
}*/


if (!Yii::app()->user->isGuest) {
    /*$userId = Yii::app()->user->_id;
    $model = UserField::model()->find('user_id = :id', array('id' => $userId));
    
    if((bool)$model->with_nds && Yii::app()->user->isTransport && $transportInfo['type'] == Transport::RUS_TRANSPORT) {
        $minRateValue = floor($minRateValue + $minRateValue * Yii::app()->params['nds']);
        $maxRateValue = floor($transportInfo['start_rate'] + $transportInfo['start_rate'] * Yii::app()->params['nds']);
    } else $minRateValue = floor($minRateValue);
    
    $userInfo = User::model()->findByPk($userId);
    if(Yii::app()->user->isTransport) {
        $residue = $minRateValue % $priceStep;
        if($residue != 0) {
            if(($minRateValue - $residue) > 0){
                $minRateValue = $minRateValue  - $residue;
            } else $minRateValue = $priceStep;
        }
    }
    
    $minRate = (($minRateValue - $priceStep)<=0)? 1 : 0;
    $inputSize = strlen((string)$minRateValue)-1;
    if($inputSize < 5 ) $inputSize = 5;
    
    if($transportInfo['type'] == 0) {
        $pointsCustom = TransportInterPoint::model()->findAll(array('order'=>'sort desc', 'condition'=>'t_id = ' . $transportInfo['id'], 'limit'=>1));
        $date_to_customs_clearance_RF = date('d.m.Y H:i', strtotime($pointsCustom[0]['date']));
    }*/
}
?>

<div class="transport-one">
    <div class="notice">
        <span class="attention">Обращаем ваше внимание на то, что при отображении ставок возможна задержка до 3 минут. Однако все ставки принимаются и корректно обрабатываются.</span>
    </div>
    <div class="note">
        <span>Просьба ознакомиться со страницей «<a title="Инструкции" href="<?php echo Yii::app()->getBaseUrl(true).'/help/'?>" class="tr-a">Инструкции</a>»</span>
    </div>
    <div class="width-100">
        <h1><?php echo $transport->location_from . ' &mdash; ' . $transport->location_to; ?></h1>
        
        <span class="t-o-published">Опубликовано <?php echo date('d.m.Y H:i', strtotime($transport->date_published)) ?></span>
        <span class="route">
            <span class="start-point point" title="<?php echo date('d.m.Y H:i', strtotime($transport->date_from))?>">
                <span class="inner-point"><?php echo $transport->location_from; ?></span>
            </span>
        <?php if($allPoints):?>
            <?php echo $allPoints; ?>
        <?php endif; ?>
            <span class="finish-point point" title="<?php echo ($transport->type == 0) ? date('d.m.Y H:i', strtotime($date_to_customs_clearance_RF)) : date('d.m.Y H:i', strtotime($transport->date_to))?>">
                <span class="inner-point"><?php echo $transport->location_to; ?></span>
            </span>
        </span>
        <div class="width-100 one-item-content">
            <div class="width-49 t-o-info">
                <label class="r-header">Основная информация</label>
                <div class="r-description"><i><?php echo $transport->description ?></i></div>
                <div class="r-params"><span>Пункт отправки: </span><strong><?php echo $transport->location_from ?></strong></div>
                <div class="r-params"><span>Пункт назначения: </span> <strong><?php echo $transport->location_to ?></strong></div>
                <div class="r-params"><span>Дата загрузки: </span><strong><?php echo date('d.m.Y', strtotime($transport->date_from)) ?></strong></div>
                <div class="r-params">
                    <?php if($transport->type == 0): ?>
                    <span>Дата доставки в пункт таможенной очистки в РФ: </span>
                    <strong>
                    <?php echo $date_to_customs_clearance_RF; ?>
                    </strong>
                    <?php else: ?>
                    <span>Дата разгрузки: </span>
                    <strong>
                    <?php echo date('d.m.Y', strtotime($transport->date_to)) ?>
                    </strong>
                    <?php endif; ?>
                </div>
                <?php if (!empty($transport->auto_info)):?><div class="r-params"><span>Транспорт: </span><strong><?php echo $transport->auto_info ?></strong></div><?php endif; ?>
                <?php if (!empty($transport->pto)):?><div class="r-params"><span>Экспорт ПТО: </span><strong><?php echo $transport->pto ?></strong></div><?php endif; ?>
            </div>
            
            <!--  rates -->
            <div class="width-50 timer-wrapper">
                <!--div id="t-container" class="t-container <?php echo ($showAdditionalTimer)? 'add-t' : '' ?>">
                    <?php if(!$transportInfo['status']): ?>
                    <span class="t-closed">Перевозка закрыта</span>
                    <?php endif; ?>
                </div--> 
                <div id="last-rate">
                     <span><?php echo $minRateValue . ' ' . $currency?></span>
                     <?php if($showWithNds): ?>
                         <div><?php echo $showWithNds ?></div> 
                     <?php endif; ?>
                 </div>
                 <label class="r-header">Текущие ставки</label>
                 <?php if($allRates): ?>
                 <div id="rates">
                     <?php foreach($allRates as $rate): ?>
                     
                     <?php endforeach; ?>
                 </div>
                 <?php endif; ?>
            </div>
            
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
   $('.point[title]').easyTooltip();
});
</script>