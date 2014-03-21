<div class="form">
<?php
    $ownership = array(
        'ООО' => 'ООО',
        'ИП'  => 'ИП',
    );
    
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
    <h1>Подать заявку на регистрацию</h1>
        <div class="row">
            <?php echo $form->error($model, 'ownership'); ?>
            <?php echo $form->labelEx($model, 'ownership'); ?>
            <?php echo $form->dropDownList($model, 'ownership', $ownership); ?>
        </div>
        <div class="row">
            <?php echo $form->labelEx($model,'company'); ?>
            <?php echo $form->textField($model,'company', array('placeholder'=>'Заполните поле')); ?>
            <?php echo $form->error($model,'company'); ?>
	</div>
        <div class="row">
            <?php echo $form->labelEx($model,'password'); ?>
            <?php echo $form->passwordField($model,'password', array('placeholder'=>'Заполните поле')); ?>
            <?php echo $form->error($model,'password'); ?>
	</div>
        <div class="row">
            <?php echo $form->labelEx($model,'confirm_password'); ?>
            <?php echo $form->passwordField($model,'confirm_password', array('placeholder'=>'Подтвердите пароль')); ?>
            <?php echo $form->error($model,'confirm_password'); ?>
	</div>
	<div class="row">
            <?php echo $form->labelEx($model,'country'); ?>
            <?php echo $form->textField($model,'country', array('placeholder'=>'Заполните поле')); ?>
            <?php echo $form->error($model,'country'); ?>
	</div>
	<div class="row">
            <?php echo $form->labelEx($model,'region'); ?>
            <?php echo $form->textField($model,'region', array('placeholder'=>'Заполните поле')); ?>
            <?php echo $form->error($model,'region'); ?>
	</div>
	<div class="row">
            <?php echo $form->labelEx($model,'city'); ?>
            <?php echo $form->textField($model,'city'); ?>
            <?php echo $form->error($model,'city'); ?>
	</div>
	<div class="row">
            <?php echo $form->labelEx($model,'district'); ?>
            <?php echo $form->textField($model,'district', array('placeholder'=>'Заполните поле')); ?>
            <?php echo $form->error($model,'district'); ?>
	</div>
	<div class="row">
            <?php echo $form->labelEx($model,'inn'); ?>
            <?php echo $form->textField($model,'inn', array('placeholder'=>'Заполните поле')); ?>
            <?php echo $form->error($model,'inn'); ?>
	</div>
        
        <div class="row">
            <?php echo $form->labelEx($model,'surname'); ?>
            <?php echo $form->textField($model,'surname', array('placeholder'=>'Заполните поле')); ?>
            <?php echo $form->error($model,'surname'); ?>
	</div>
        <div class="row">
            <?php echo $form->labelEx($model, 'name'); ?>
            <?php echo $form->textField($model, 'name', array('placeholder'=>'Заполните поле')); ?>
            <?php echo $form->error($model, 'name'); ?>
	</div>
        <div class="row">
            <?php echo $form->labelEx($model, 'secondname'); ?>
            <?php echo $form->textField($model, 'secondname', array('placeholder'=>'Заполните поле')); ?>
            <?php echo $form->error($model, 'secondname'); ?>
	</div>
	<div class="row">
            <?php echo $form->labelEx($model,'phone'); ?>
            <?php echo $form->textField($model,'phone', array('placeholder'=>'Заполните поле')); ?>
            <?php echo $form->error($model,'phone'); ?>
	</div>
	<div class="row">
            <?php echo $form->labelEx($model,'email'); ?>
            <?php echo $form->textField($model,'email', array('placeholder'=>'Заполните поле')); ?>
            <?php echo $form->error($model,'email'); ?>
	</div>
	<div class="row">
            <?php echo $form->labelEx($model,'description'); ?>
            <?php echo $form->textField($model,'description'); ?>
            <?php echo $form->error($model,'description'); ?>
	</div>
        <div class="row">
            <?php echo $form->labelEx($model,'nds'); ?>
            <?php echo $form->dropDownList($model, 'nds', array('0'=>'Нет', '1' => 'Да')); ?>
            <?php echo $form->error($model,'nds'); ?>
	</div>
        <div class="row">
            <?php echo $form->labelEx($model,'iagree'); ?>
            <?php echo $form->checkBox($model, 'iagree'); ?>
            <?php echo $form->error($model,'iagree'); ?>
	</div>
	<div class="row buttons">
            <?php echo CHtml::submitButton('Подтвердить', array('class'=>'btn')); ?>
	</div>
<?php $this->endWidget(); ?>
</div><!-- form -->