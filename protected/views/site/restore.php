<noscript>
    <div class="info error">К сожалению данный функционал не доступен для Вас, т.к. у Вас отключен JavaScript.</div>
    <div class="hide">
</noscript>
<?php if(Yii::app()->user->hasFlash('success')):?>
    <div class="info">
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
<?php endif; ?>
<?php if(Yii::app()->user->hasFlash('error')):?>
    <div class="info error">
        <?php echo Yii::app()->user->getFlash('error'); ?>
    </div>
<?php endif; ?>
<div class="form">
    <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id'=>'registration-form',
            'enableAjaxValidation' => false,
            'enableClientValidation' => true,      
            'clientOptions' => array(
                'validateOnSubmit' => true
            )
        )); 
    ?>
    <div class="form-label">Восстановление доступа</div>
    
    <?php echo $form->errorSummary($model); ?>
    
    <div class="row">
        <?php echo $form->labelEx($model, 'inn'); ?>
        <?php echo $form->textField($model, 'inn'); ?>
        <?php echo $form->error($model, 'inn'); ?>
    </div>
    
    <div class="row capture">
        <?php $this->widget('CCaptcha', array('clickableImage'=>true, 
            'showRefreshButton'=>true, 
            'buttonLabel' => CHtml::image(Yii::app()->baseUrl.'/images/upload.jpg'),
            'imageOptions'=>array('style'=>'border:none;', 
                'height'=>'40px',
                'alt'=>'Обновить', 
                'title'=>'Нажмите чтобы обновить картинку'
            ))); 
        ?>
    </div>
    
    <div class="row">
        <?php echo $form->labelEx($model,'verifyCode'); ?>
        <?php echo $form->textField($model,'verifyCode'); ?>
        <?php echo $form->error($model,'verifyCode'); ?>
    </div>
    
    <?php echo CHtml::submitButton('Отправить', array('class'=>'btn')); ?>
    <?php $this->endWidget(); ?>
</div>

<noscript>
    </div>
</noscript>

<?php
   Yii::app()->clientScript->registerScript('refresh-captcha', '$(document).ready(function(){$("#yw0").click();});'); 