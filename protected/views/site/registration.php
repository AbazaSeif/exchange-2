<div class="form">
<?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'registration-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
        ),
    )); ?>
    <h1>Подать заявку на регистрацию</h1>
        <div class="row">
		<?php echo $form->labelEx($model,'company'); ?>
		<?php echo $form->textField($model,'company'); ?>
		<?php echo $form->error($model,'company'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'country'); ?>
		<?php echo $form->textField($model,'country'); ?>
		<?php echo $form->error($model,'country'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'region'); ?>
		<?php echo $form->textField($model,'region'); ?>
		<?php echo $form->error($model,'region'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'city'); ?>
		<?php echo $form->textField($model,'city'); ?>
		<?php echo $form->error($model,'city'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'district'); ?>
		<?php echo $form->textField($model,'district'); ?>
		<?php echo $form->error($model,'district'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'inn'); ?>
		<?php echo $form->textField($model,'inn'); ?>
		<?php echo $form->error($model,'inn'); ?>
	</div>
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
		<?php echo $form->labelEx($model, 'second_name'); ?>
		<?php echo $form->textField($model, 'second_name'); ?>
		<?php echo $form->error($model, 'second_name'); ?>
	</div>
        
	<div class="row">
		<?php echo $form->labelEx($model,'phone'); ?>
		<?php echo $form->textField($model,'phone'); ?>
		<?php echo $form->error($model,'phone'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email'); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textField($model,'description'); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Подтвердить', array('class'=>'btn')); ?>
	</div>
<?php $this->endWidget(); ?>
</div><!-- form -->