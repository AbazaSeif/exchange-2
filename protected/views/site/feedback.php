<h1>Форма обратной связи</h1>
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
    <?php echo $form->textField($model,'message'); ?>
    <?php echo $form->error($model,'message'); ?>
</div>

<div class="row buttons">
    <?php echo CHtml::submitButton('Отправить', array('class'=>'btn')); ?>
</div>
<?php $this->endWidget(); ?>