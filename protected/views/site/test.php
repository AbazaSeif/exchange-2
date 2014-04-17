<div class="form">
<?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'test-form',
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
    )); ?>
    <h1>Отправить письмо</h1>
	<div class="row">
            <?php echo $form->labelEx($model,'email'); ?>
            <?php echo $form->textField($model,'email'); ?>
            <?php echo $form->error($model,'email'); ?>
	</div>
        
	<div class="row buttons">
            <?php echo CHtml::submitButton('Подтвердить', array('class'=>'btn')); ?>
	</div>
<?php $this->endWidget(); ?>
</div><!-- form -->