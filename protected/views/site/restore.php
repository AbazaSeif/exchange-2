<noscript>
    <div class="no-script-label">К сожалению данный функционал не доступен для Вас, т.к. у Вас отключен JavaScript.</div>
    <div class="hide">
</noscript>
<div class="form">
<?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'registration-form',
        'enableClientValidation' => true,        
        // 'enableAjaxValidation' => true,        
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange' => true,
            'afterValidate'=>'js:function( form, data, hasError ) 
            {     
                if( hasError ){
                    return false;
                }
                else{
                    return true;
                }
            }'
        ),
    )); ?>
    <h1>Восстановление доступа</h1>
    <div class="row">
        <?php echo $form->labelEx($model,'inn'); ?>
        <?php echo $form->textField($model, 'inn'); ?>
        <?php echo $form->error($model,'inn'); ?>
    </div>
    
    <div class="row capture">
        <?php $this->widget('CCaptcha', array('clickableImage'=>true, 
            'showRefreshButton'=>true, 
            'buttonLabel' => CHtml::image(Yii::app()->baseUrl . '/images/upload.jpg'),
            'imageOptions'=>array('style'=>'border:none;', 
                'height'=>'40px',
                'alt'=>'Обновить', 
                'title'=>'Нажмите чтобы обновить картинку'))); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'verifyCode'); ?>
        <?php echo $form->textField($model,'verifyCode'); ?>
        <?php echo $form->error($model,'verifyCode'); ?>
    </div>
    
    <div class="row buttons">
        <?php echo CHtml::submitButton('Восстановить', array('class'=>'btn')); ?>
    </div>
<?php $this->endWidget(); ?>
</div><!-- form -->
<noscript>
    </div>
</noscript>