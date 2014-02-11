<?php $lastRate = null;
$currency = ' €';
$defaultRate = false;
$priceStep = Transport::INTER_PRICE_STEP;
$now = date('Y m d H:i:s', strtotime('now'));
$end = date('Y m d H:i:s', strtotime($transportInfo['date_from'] . ' -' . Yii::app()->params['hoursBefore'] . ' hours'));

//if($transportInfo['type']==Transport::RUS_TRANSPORT){
if(!$transportInfo['currency']){
    $priceStep = Transport::RUS_PRICE_STEP; 
}

if(!$transportInfo['currency']){
   $currency = ' руб.';
} else if($transportInfo['currency'] == 1){
   $currency = ' $';
}

if (!empty($transportInfo['rate_id'])) {
    $lastRate = $this->getPrice($transportInfo['rate_id']);
} else {
    $lastRate = $transportInfo['start_rate'];
    $defaultRate = true;
}

if (!Yii::app()->user->isGuest) {
    $userId = Yii::app()->user->_id;
    $model = UserField::model()->find('user_id = :id', array('id' => $userId));
    //$originalPrice = $lastRate;
    if((bool)$model->with_nds){
        $lastRate = $lastRate + $lastRate * Yii::app()->params['nds'];
    }
    $userInfo = User::model()->findByPk($userId);
}

//$startValue = ($defaultRate)? $lastRate : ($lastRate - $priceStep);
$minRate = (($lastRate - $priceStep)<=0)? 1 : 0;
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
            <?php if (!empty($transportInfo['auto_info'])):?><div><span>Транспорт: </span><strong><?php echo $transportInfo['auto_info'] ?></strong></div><?php endif; ?>
        </div>	
    </div>
    <?php if (!Yii::app()->user->isGuest && $lastRate > 0 && Yii::app()->user->checkAccess('transport') && !Yii::app()->user->isRoot): ?>
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
                <input id="rate-price" value="<?php echo $lastRate?>" init="<?php echo $lastRate?>" type="text" size="<?php echo $inputSize ?>"/>
            </div>
            <div class="r-submit <?php echo ($defaultRate) ? '':disabled ?>"><span>OK</span></div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php if (Yii::app()->user->isGuest): ?>
         <div class="width-30-r timer-wrapper">
             <div id="t-container"></div>
             <div id="last-rate"><span><?php echo '****' . $currency?></span></div>
         </div>
    <?php elseif(Yii::app()->user->isRoot): ?>
        <div class="width-30-r timer-wrapper">
             <div id="t-container"></div>
             <div id="last-rate"><span><?php echo $lastRate . ' ' . $currency?></span></div>
        </div>  
    <?php endif; ?>
</div>
<?php if (!Yii::app()->user->isGuest): ?>
        <div id="rates"></div>
<?php endif; ?>
<div>
    <?php if (!Yii::app()->user->isGuest && !Yii::app()->user->isRoot):
        echo CHtml::link('Связаться с модератором', '#', array(
            'id' => 'dialog-connect',
            'title'=>'Связаться с модератором',
        ));
    endif;?>
</div>
<script>
$(document).ready(function(){
    rateList.data = {
        currency : ' <?php echo $currency ?>',
        priceStep : <?php echo $priceStep ?>,
        transportId : <?php echo $transportInfo['id'] ?>,
        status: <?php echo $transportInfo['status'] ?>,
        step: <?php echo $priceStep ?>,
        nds: <?php echo ((bool)$model->with_nds) ? Yii::app()->params['nds'] : 0 ?>,
        defaultRate: <?php echo ($defaultRate)? 1 : 0 ?>,
    };
    <?php if (!Yii::app()->user->isGuest): ?>
        rateList.data.name = '<?php echo $userInfo[name] ?>',
        rateList.data.surname = '<?php echo $userInfo[surname] ?>',
    <?php endif; ?> 
    rateList.init();
    setInterval(function(){rateList.update($('#rates'))}, 15000);
    
    var timer = new Timer();
    timer.init('<?php echo $now ?>', '<?php echo $end ?>', 't-container', rateList.data.status);
    
    $('#dialog-connect').live('click', function() {
        $("#modalDialog").dialog("open");
    });
     
    $('.ui-widget-overlay').live('click', function() {
        $(".ui-dialog-content").dialog( "close" );
    });
    
    $( "#abordRateBtn" ).live('click', function() {
        $(".ui-dialog-content").dialog( "close" );
    });
});
</script>
<?php if (!Yii::app()->user->isGuest && !Yii::app()->user->isRoot):?>
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