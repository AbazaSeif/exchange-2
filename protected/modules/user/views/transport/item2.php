<?php
$showAdditionalTimer = false;
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
/*if((bool)$model->with_nds) {
    $minRateValue = floor($minRateValue + $minRateValue * Yii::app()->params['nds']);
} else $maxRateValue = $transportInfo['start_rate'];
*/
$maxRateValue = $transportInfo['start_rate'];
$minRateValue = null;

$defaultRate = false;

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

$priceStep = Transport::INTER_PRICE_STEP;
if(!$transportInfo['currency']){
    $priceStep = Transport::RUS_PRICE_STEP; 
}

$currency = '€';
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
}


if (!Yii::app()->user->isGuest) {
    $userId = Yii::app()->user->_id;
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
    }
}
?>

<div class="transport-one">
    <div class="notice">
        <span class="attention">Обращаем ваше внимание на то, что при отображении ставок возможна задержка до 3 минут. Однако все ставки принимаются и корректно обрабатываются.</span>
    </div>
    <div class="note">
        <span>Просьба ознакомиться со страницей «<a title="Инструкции" href="<?php echo Yii::app()->getBaseUrl(true).'/help/'?>" class="tr-a">Инструкции</a>»</span>
    </div>
    <!--div style="color: red">
        <span>Биржа временно не работает. Ставки не принимаются.</span>
    </div-->
    
    <div class="width-100">
        <h1><?php echo $transportInfo['location_from'] . ' &mdash; ' . $transportInfo['location_to']; ?></h1>
        <span class="t-o-published">Опубликовано <?php echo date('d.m.Y H:i', strtotime($transportInfo['date_published'])) ?></span>
        <span class="route">
            <span class="start-point point" title="<?php echo date('d.m.Y H:i', strtotime($transportInfo['date_from']))?>">
                <span class="inner-point"><?php echo $transportInfo['location_from']; ?></span>
            </span>
        <?php if($allPoints):?>
            <?php echo $allPoints; ?>
        <?php endif; ?>
            <span class="finish-point point" title="<?php echo ($transportInfo['type'] == 0) ? date('d.m.Y H:i', strtotime($date_to_customs_clearance_RF)) : date('d.m.Y H:i', strtotime($transportInfo['date_to']))?>">
                <span class="inner-point"><?php echo $transportInfo['location_to']; ?></span>
            </span>
        </span>
        <div class="width-100 one-item-content">
            <div class="width-49 t-o-info">
                <label class="r-header">Основная информация</label>
                <div class="r-description"><i><?php echo $transportInfo['description'] ?></i></div>
                <div class="r-params"><span>Пункт отправки: </span><strong><?php echo $transportInfo['location_from'] ?></strong></div>
                <div class="r-params"><span>Пункт назначения: </span> <strong><?php echo $transportInfo['location_to'] ?></strong></div>
                <div class="r-params"><span>Дата загрузки: </span><strong><?php echo date('d.m.Y', strtotime($transportInfo['date_from'])) ?></strong></div>
                <div class="r-params">
                    <?php if($transportInfo['type'] == 0): ?>
                    <span>Дата доставки в пункт таможенной очистки в РФ: </span>
                    <strong>
                    <?php echo $date_to_customs_clearance_RF; ?>
                    </strong>
                    <?php else: ?>
                    <span>Дата разгрузки: </span>
                    <strong>
                    <?php echo date('d.m.Y', strtotime($transportInfo['date_to'])) ?>
                    </strong>
                    <?php endif; ?>
                </div>
                <?php if (!empty($transportInfo['auto_info'])):?><div class="r-params"><span>Транспорт: </span><strong><?php echo $transportInfo['auto_info'] ?></strong></div><?php endif; ?>
                <?php if (!empty($transportInfo['pto'])):?><div class="r-params"><span>Экспорт ПТО: </span><strong><?php echo $transportInfo['pto'] ?></strong></div><?php endif; ?>
            </div>
            
            <?php if (!Yii::app()->user->isGuest && Yii::app()->user->isTransport && $minRateValue > 0): ?>
            <div class="width-50 timer-wrapper">
                <div class="width-100">
                    <div id="counter-<?php echo $transportInfo['id']?>" class="t-container width-40 <?php echo ($showAdditionalTimer)? 'add-t' : '' ?> <?php echo ($transportInfo['status'] && $now < $end)? 'open' : '' ?>">
                        <?php if(!$transportInfo['status']): ?>
                        <span class="t-closed closed">Перевозка закрыта</span>
                        <?php //elseif($now > $end): ?>
                        <!--span class="t-closed closed">Обработка</span-->
                        <?php endif; ?>
                    </div>
                    <?php if($now < $end && $transportInfo['status']):?>
                    <div class="rate-wrapper width-60 <?php echo (!$transportInfo['status'])? 'hide': '' ?>">
                        <div class="r-block">
                            <div class="rate-btns-wrapper">
                                <div id="rate-up" class="<?php echo ($minRateValue == $maxRateValue)?'disabled':''?>"></div>
                                <div id="rate-down" class="<?php echo ($minRate)?'disabled':''?>"></div>
                            </div>
                            <span class="text"><?php echo $currency ?></span>
                            <input id="rate-price" value="<?php echo ceil($minRateValue) ?>" init="<?php echo $maxRateValue?>" type="text" size="<?php echo $inputSize ?>" <?php echo (($now > $end) || !$transportInfo['status'])? 'disabled="hide"': '' ?>/>
                        </div>
                        <div class="r-submit"><span>Сделать ставку</span></div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <label class="r-header">Текущие ставки</label>
                <div id="rates">
                    <div id="r-preloader">
                        <img src="/images/loading.gif"/>
                    </div>
                </div>
            </div>
            <?php elseif (Yii::app()->user->isGuest): ?>
                 <div class="width-50 timer-wrapper">
                     <div id="t-container" class="<?php echo ($showAdditionalTimer)? 'add-t' : '' ?>"></div>
                     <div id="last-rate"><span><?php echo '**** ' . $currency?></span></div>
                 </div>
            <?php elseif(!Yii::app()->user->isGuest && !Yii::app()->user->isTransport): ?>
                <div class="width-50 timer-wrapper">
                    <!--div id="t-container" class="<?php echo ($showAdditionalTimer)? 'add-t' : '' ?>"></div-->
                    <div id="t-container" class="t-container <?php echo ($showAdditionalTimer)? 'add-t' : '' ?>">
                        <?php //if(!$transportInfo['status'] || $end < $now): ?>
                        <?php if(!$transportInfo['status']): ?>
                        <span class="t-closed">Перевозка закрыта</span>
                        <?php endif; ?>
                    </div> 
                    <div id="last-rate">
                         <span><?php echo $minRateValue . ' ' . $currency?></span>
                         <?php if($showWithNds): ?>
                             <div><?php echo $showWithNds ?></div> 
                         <?php endif; ?>
                     </div>
                     <label class="r-header">Текущие ставки</label>
                     <div id="rates">
                     </div>
                </div>  
            <?php endif; ?>
        </div>
    </div>
<?php if(!Yii::app()->user->isGuest && Yii::app()->user->isTransport): ?>
        <div>
        <?php echo CHtml::link('Связаться с модератором', '#', array(
                'id' => 'dialog-connect',
                'title'=>'Связаться с модератором',
            ));
        ?>
            </div>
<?php endif; ?>

<script>
function getTime(){
    return "<?php echo date("Y-m-d H:i:s") ?>";
}

$(document).ready(function(){
    rateList.data = {
        currency : ' <?php echo $currency ?>',
        priceStep : <?php echo $priceStep ?>,
        transportId : <?php echo $transportInfo['id'] ?>,
        status: <?php echo $transportInfo['status'] ?>,
        step: <?php echo $priceStep ?>,
        nds: <?php echo ((bool)$model->with_nds && Yii::app()->user->isTransport && $transportInfo['type'] == Transport::RUS_TRANSPORT) ? Yii::app()->params['nds'] : 0 ?>,
        ndsValue: <?php echo Yii::app()->params['nds'] ?>,
        defaultRate: <?php echo ($defaultRate)? 1 : 0 ?>,
        trType: <?php echo ($transportInfo['type'] == Transport::RUS_TRANSPORT)? 1 : 0; ?>
    };
    
    <?php if (!Yii::app()->user->isGuest): ?>
        <?php if(Yii::app()->user->isTransport): ?>
        //var socket = io.connect('http://exchange.lbr.ru:3001/');
        //var socket = io.connect('http://localhost:3000/');
        
        socket.emit('loadRates', <?php echo $userId ?>, <?php echo $transportInfo['id'] ?>, <?php echo 0 ?>);
        <?php //if($transportInfo['status'] == Transport::ACTIVE_TRANSPORT): ?>
//        socket.on('timer', function(data) {
//            var container = $('#t-container');
//            if(data.transportId == <?php echo $transportInfo['id'] ?>) {
//                if(data.access) {
//                   container.html(data.time);
//                } else {
//                   $(".ui-dialog-content").dialog( "close" );
//                   $('.r-submit').addClass('disabled');
//                   $('.rate-wrapper').slideUp("slow");
//                   container.removeClass('open');
//                   container.html('<span class="t-closed"><img class="small-loading" src="/images/loading-small.gif"/>Обработка результатов</span>'); 
//                   setTimeout(function(){ container.html('<span class="t-closed closed">Перевозка закрыта</span>') }, 180000);
//                }
//            }
//        });
        <?php //endif; ?>
        
        rateList.data.socket = socket;
        rateList.data.containerElements = '';
        rateList.data.userId = '<?php echo $userInfo[id] ?>';
        rateList.data.transportId = '<?php echo $transportInfo[id] ?>';
        rateList.data.transportType = '<?php echo $transportInfo[type] ?>';
        rateList.data.company = '<?php echo $userInfo[company] ?>';
        rateList.data.name = '<?php echo $userInfo[name] ?>';
        rateList.data.surname = '<?php echo $userInfo[surname] ?>';
        rateList.data.dateClose = '<?php echo $transportInfo[date_close] ?>';
        rateList.data.dateCloseNew = '<?php echo $transportInfo[date_close_new] ?>';
        
        $('#dialog-connect').live('click', function() {
            $("#modalDialog").dialog("open");
        });

        $('.ui-widget-overlay').live('click', function() {
            $(".ui-dialog-content").dialog( "close" );
        });

        $( "#abordRateBtn" ).live('click', function() {
            $(".ui-dialog-content").dialog( "close" );
        });
        
        $( "#errorRate .btn" ).live('click', function() {
            $(".ui-dialog-content").dialog( "close" );
        });
        
        $( "#errorStatus .btn" ).live('click', function() {
            $(".ui-dialog-content").dialog( "close" );
        });
        $( "#closeRate .btn" ).live('click', function() {
            $(".ui-dialog-content").dialog( "close" );
        });
        <?php endif; ?> 
            rateList.init();
   <?php endif; ?>
   $('.point[title]').easyTooltip();
});

</script>
<!-- Dialog windows -->
<?php if (!Yii::app()->user->isGuest && Yii::app()->user->isTransport):?>
<div>
    <?php
    $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id' => 'modalDialog',
        'options' => array(
            'title' => 'Отправить сообщение',
            'autoOpen' => false,
            'modal' => true,
            'resizable'=> false,
        ),
    ));
    $qForm = new QuickForm; 
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'quick-form',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
        'htmlOptions'=>array(
            'class'=>'form',
        ),
        'action' => array('site/quick'),
    ));
    ?>
    <?php echo $form->errorSummary($qForm); ?>
    <div class="row">
    <?php echo $form->labelEx($qForm,'message'); ?>
    <?php echo $form->textArea($qForm,'message',array('rows'=>6, 'cols'=>31)); ?>
    <?php echo $form->error($qForm,'message'); ?>
    </div>
    <div class="row">
    <?php echo $form->hiddenField($qForm, 'user', array('value'=>Yii::app()->user->_id));?>
    <?php echo $form->hiddenField($qForm, 'transport', array('value'=>$transportInfo['id']));?>
    </div>
    <?php echo CHtml::submitButton('Отправить',array('class' => 'btn')); ?>
    <?php 
        $this->endWidget();
        $this->endWidget('zii.widgets.jui.CJuiDialog');
    ?>
</div>
<div>
    <?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id' => 'addRate',
        'options' => array(
            'title' => 'Подтверждение',
            'autoOpen' => false,
            'modal' => true,
            'resizable'=> false,
        ),
    ));
    ?>
    <div class="row">
        <span>Вы уверены что хотите сделать ставку в размере <span id='setPriceVal'></span><?php echo $currency ?> ?</span> 
    </div>
    <div class="rate-button">
    <?php echo CHtml::button('Подтвердить',array('id' => 'setRateBtn','class' => 'btn')); ?>
    </div>
    <div class="rate-button">
    <?php echo CHtml::button('Отказаться',array('id' => 'abordRateBtn','class' => 'btn')); ?>
    </div>
    <?php 
        $this->endWidget('zii.widgets.jui.CJuiDialog');
    ?>
</div>
<div>
    <?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id' => 'errorStatus',
        'options' => array(
            'title' => 'Подтверждение',
            'autoOpen' => false,
            'modal' => true,
            'resizable'=> false,
        ),
    ));
    ?>
    <div class="row">
        <span>К сожалению, Вы не можете сделать ставку, т.к. <span id='curStatus'></span></span> 
    </div>
    <?php echo CHtml::submitButton('ОК',array('class' => 'btn')); ?>
    <?php 
        $this->endWidget('zii.widgets.jui.CJuiDialog');
    ?>
</div>
<div>
    <?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id' => 'errorRate',
        'options' => array(
            'title' => 'Ошибка',
            'autoOpen' => false,
            'modal' => true,
            'resizable'=> false,
        ),
    ));
    ?>
    <div class="row">
        <span>Ставка не может быть больше <span id="maxRateVal"></span><?php echo $currency ?></span> 
    </div>
    <?php echo CHtml::submitButton('ОК',array('class' => 'btn')); ?>
    <?php 
        $this->endWidget('zii.widgets.jui.CJuiDialog');
    ?>
</div>
<div>
    <?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id' => 'closeRate',
        'options' => array(
            'title' => 'Ошибка',
            'autoOpen' => false,
            'modal' => true,
            'resizable'=> false,
        ),
    ));
    ?>
    <div class="row">
        <span id="closeTr"></span>
    </div>
    <?php echo CHtml::submitButton('ОК',array('class' => 'btn')); ?>
    <?php
        $this->endWidget('zii.widgets.jui.CJuiDialog');
    ?>
</div>
<?php endif; ?>
<?php else: $this->redirect('/'); ?>
<?php endif; ?>
</div>

