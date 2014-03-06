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
    <div class="row buttons">
        <?php echo CHtml::submitButton('Восстановить', array('class'=>'btn')); ?>
    </div>
<?php $this->endWidget(); ?>
</div><!-- form -->