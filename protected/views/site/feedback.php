<?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'feedback-form',
        'enableClientValidation' => true,    
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
    )); 
?>
<div class="form-label">Форма обратной связи</div>
<div class="row">
    <?php echo $form->labelEx($model,'surname'); ?>
    <?php echo $form->textField($model,'surname'); ?>
    <?php echo $form->error($model,'surname'); ?>
</div>
<div class="row">
    <?php echo $form->labelEx($model, 'name'); ?>
    <?php echo $form->textField($model, 'name'); ?>
    <?php echo $form->error($model, 'name'); ?>
</div>
<div class="row">
    <?php echo $form->labelEx($model,'email'); ?>
    <?php echo $form->textField($model,'email'); ?>
    <?php echo $form->error($model,'email'); ?>
</div>
<div class="row">
    <?php echo $form->labelEx($model,'phone'); ?>
    <?php echo $form->textField($model,'phone'); ?>
    <?php echo $form->error($model,'phone'); ?>
</div>
<div class="row">
    <?php echo $form->labelEx($model,'message'); ?>
    <?php echo $form->textArea($model,'message'); ?>
    <?php echo $form->error($model,'message'); ?>
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
    <?php echo CHtml::submitButton('Отправить', array('class'=>'btn')); ?>
</div>
<?php $this->endWidget(); ?>