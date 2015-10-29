<?php



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
                     <div><?php echo $showWinner ?></div> 
                 </div>
                 <label class="r-header">Текущие ставки</label>
                 <div id="rates">
                 </div>
            </div>
            
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    rateList.data = {
        currency : ' <?php echo $currency ?>',
        transportId : <?php echo $transport->id ?>,
        status: <?php echo $transport->status ?>,
        priceStep : <?php echo $priceStep ?>,
        nds: 0,
        ndsValue: <?php echo Yii::app()->params['nds'] ?>,
        trType: <?php echo ($transport->type == Transport::RUS_TRANSPORT)? 1 : 0; ?>
    };
    rateList.init();
    
    $('.point[title]').easyTooltip();
});
</script>