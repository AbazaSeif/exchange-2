<?php 
//$lastRate = null;
$minRateValue = null;
$currency = '€';
$defaultRate = false;
$priceStep = Transport::INTER_PRICE_STEP;
$now = date('m/d/Y H:i:s', strtotime('now'));
//$end = date('m/d/Y H:i:s', strtotime($transportInfo['date_from'] . ' -' . Yii::app()->params['hoursBefore'] . ' hours'));
$end = date('m/d/Y H:i:s', strtotime($transportInfo['date_close']));
$winRate = Rate::model()->findByPk($transportInfo['rate_id']);
$winFerryman = User::model()->findByPk($winRate->user_id);
$winFerrymanShowNds = UserField::model()->findByAttributes(array('user_id'=>$winRate->user_id));
$showWithNds = '';

$allPoints = TransportInterPoint::getPoints($transportInfo['id']);

//if($transportInfo['type']==Transport::RUS_TRANSPORT){
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

if($winFerrymanShowNds->with_nds) {
    $showWithNds = ' (с НДС: ' . ceil($winRate->price + $winRate->price * Yii::app()->params['nds']) . ' ' . $currency . ') ' . $winFerryman->company;    
} else if(!$defaultRate){
    $showWithNds = $winFerryman->company;    
}


if (!Yii::app()->user->isGuest) {
    $userId = Yii::app()->user->_id;
    $model = UserField::model()->find('user_id = :id', array('id' => $userId));

    if((bool)$model->with_nds) {
        $minRateValue = floor($minRateValue + $minRateValue * Yii::app()->params['nds']);
    } else $minRateValue = floor($minRateValue);
    
    $userInfo = User::model()->findByPk($userId);
    
    //$userInfo = User::model()->findByPk($userId);

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
}
?>

<div class="transport-one">
    <div class="width-100">
        <h1><?php echo $transportInfo['location_from'] . ' &mdash; ' . $transportInfo['location_to']; ?></h1>
        <span class="t-o-published">Опубликована <?php echo date('d.m.Y H:i', strtotime($transportInfo['date_published'])) ?></span>
        <span class="route">
            <span class="start-point point">
                <?php echo $transportInfo['location_from']; ?>
            </span>
        <?php if($allPoints):?>
            <?php echo $allPoints; ?>
        <?php endif; ?>
            <span class="finish-point point">
                <?php echo $transportInfo['location_to']; ?>
            </span>
        </span>
        <div class="width-100 one-item-content">
            <div class="width-49 t-o-info">
                <label class="r-header">Основная информация</label>
                <div class="r-description"><i><?php echo $transportInfo['description'] ?></i></div>
                <div class="r-params"><span>Пункт отправки: </span><strong><?php echo $transportInfo['location_from'] ?></strong></div>
                <div class="r-params"><span>Пункт назначения: </span> <strong><?php echo $transportInfo['location_to'] ?></strong></div>
                <div class="r-params"><span>Дата загрузки: </span><strong><?php echo date('d.m.Y', strtotime($transportInfo['date_from'])) ?></strong></div>
                <div class="r-params"><span>Дата разгрузки: </span><strong><?php echo date('d.m.Y', strtotime($transportInfo['date_to'])) ?></strong></div>
                <?php if (!empty($transportInfo['auto_info'])):?><div><span>Транспорт: </span><strong><?php echo $transportInfo['auto_info'] ?></strong></div><?php endif; ?>
            </div>
            <?php if (!Yii::app()->user->isGuest && $minRateValue > 0 && Yii::app()->user->isTransport): ?>
            <div class="width-50 timer-wrapper">
                <div class="width-100">
                    <div id="t-container" class="width-40"></div>
                    <?php //if($transportInfo['status']): ?>
                    <div id="t-error"></div>

                    <div class="rate-wrapper width-60">
                        <div class="r-block">
                            <div class="rate-btns-wrapper <?php echo (($now > $end) || !$transportInfo['status'])? 'hide': '' ?>">
                                <div id="rate-up"></div>
                                <div id="rate-down" class="<?php echo ($minRate)?'disabled':''?>"></div>
                            </div>
                            <span class="text"><?php echo $currency ?></span>
                            <input id="rate-price" value="<?php echo ceil($minRateValue) ?>" init="<?php echo $minRateValue?>" type="text" size="<?php echo $inputSize ?>" <?php echo (($now > $end) || !$transportInfo['status'])? 'disabled="hide"': '' ?>/>
                        </div>
                        <div class="r-submit <?php echo (($now > $end) || !$transportInfo['status'])? 'hide': '' ?>"><span>Сделать ставку</span></div>
                    </div>
                </div>
                <?php //endif; ?>
            
            <?php if (!Yii::app()->user->isGuest): ?>
                    <label class="r-header">Текущие ставки</label>
                    <div id="rates">
                        <div id="r-preloader">
                            <img src="/images/loading.gif"/>
                        </div>
                    </div>
            <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php if (Yii::app()->user->isGuest): ?>
                 <div class="width-50 timer-wrapper">
                     <div id="t-container"></div>
                     <div id="last-rate"><span><?php echo '**** ' . $currency?></span></div>
                 </div>
            <?php elseif(!Yii::app()->user->isTransport): ?>
                <div class="width-50 timer-wrapper">
                     <div id="t-container"></div>
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
        <?php echo CHtml::link('Связаться с модератором', '#', array(
                'id' => 'dialog-connect',
                'title'=>'Связаться с модератором',
            ));
        ?>
<?php endif; ?>
</div>
<script>
function getTime(){
    return "<?php echo date("Y-m-d H:i:s") ?>";
}

$(document).ready(function(){
    var timer = new Timer();
    timer.init('<?php echo $now ?>', '<?php echo $end ?>', 't-container', <?php echo $transportInfo['status'] ?>);
    rateList.data = {
        currency : ' <?php echo $currency ?>',
        priceStep : <?php echo $priceStep ?>,
        transportId : <?php echo $transportInfo['id'] ?>,
        status: <?php echo $transportInfo['status'] ?>,
        step: <?php echo $priceStep ?>,
        nds: <?php echo ((bool)$model->with_nds) ? Yii::app()->params['nds'] : 0 ?>,
        ndsValue: <?php echo Yii::app()->params['nds'] ?>,
        defaultRate: <?php echo ($defaultRate)? 1 : 0 ?>,
    };
    <?php if (!Yii::app()->user->isGuest): ?>
        <?php if(Yii::app()->user->isTransport): ?>

        var socket = io.connect('http://exchange.lbr.ru:3000/');
        //var socket = io.connect('http://localhost:3000/');
        
        // ??? user_id
        <?php //if(Yii::app()->user->isContactUser): ?>
            //socket.emit('loadRates', <?php //echo $userId ?>, <?php //echo $transportInfo['id'] ?>);
        <?php //else: ?> 
            //socket.emit('loadRates', <?php //echo $userId ?>, <?php //echo $transportInfo['id'] ?>);
        <?php //endif; ?>

        socket.emit('loadRates', <?php echo $userId ?>, <?php echo $transportInfo['id'] ?>);
        /*  
        var newElement = "<div id='" + id + "' class='rate-one'>" + 
            "<div class='r-o-container'>" + 
                time +
                "<div class='r-o-user'>" + rate.name + ' ' + rate.surname + "</div>" +
            "</div>" +
            "<div class='r-o-price'>" + price + rateList.data.currency + "</div>" +
            "</div>"
        ;
    
        $('#test').prepend(newElement);
        */

        /*var k = 0;
        socket.on('init', function (data) {
            var newElement = "<div class='rate-one'>" + 
                "<div class='r-o-container'>" + 
                    //time +
                    "<div class='r-o-user'>" + data.name + "</div>" +
                "</div>" +
                "<div class='r-o-price'>" + data.price + " <?php// echo $currency ?>" + "</div>" +
                "</div>"
            ;
            //$('#rates').append(newElement);
        });
        */
        
        /*
        socket.on('endinit', function () {
            $("#rates").mCustomScrollbar({
                scrollButtons:{
                    enable:true
                }
            });
        });
        */

        /***************************************************/
        
        rateList.data.socket = socket;
        rateList.data.userId   = '<?php echo $userInfo[id] ?>';
        rateList.data.transportId  = '<?php echo $transportInfo[id] ?>';
        rateList.data.company   = '<?php echo $userInfo[company] ?>';
        rateList.data.name   = '<?php echo $userInfo[name] ?>';
        rateList.data.surname = '<?php echo $userInfo[surname] ?>';
        
        //rateList.init();
    //setInterval(function(){rateList.update($('#rates'))}, 15000);
    
    
    $('#dialog-connect').live('click', function() {
        $("#modalDialog").dialog("open");
    });
     
    $('.ui-widget-overlay').live('click', function() {
        $(".ui-dialog-content").dialog( "close" );
    });
    
    $( "#abordRateBtn" ).live('click', function() {
        $(".ui-dialog-content").dialog( "close" );
    });
    
    /*$(".content").mCustomScrollbar({
        scrollButtons:{
            enable:true
        }
    });*/
    
    <?php else: ?> 
        //admin or logist
 
    <?php endif; ?> 
        rateList.init();
    <?php endif; ?> 
    
});
</script>
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
    <div class="button">
    <?php echo CHtml::submitButton('Отправить',array('class' => 'btn')); ?>
    </div>
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
<?php endif; ?>
