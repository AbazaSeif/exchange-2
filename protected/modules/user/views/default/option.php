<div class="form">
<?php echo CHtml::beginForm('/user/saveOption/', 'POST', array('id'=>'options')); ?>
	<fieldset>
		<legend>Настройки для оповещения по почте</legend>
		<?php //echo CHtml::errorSummary($model); ?>
		
		<div class="row">
		<?php echo CHtml::checkBox('mail_transport_create_1', (bool)$model['mail_transport_create_1']); ?>
		<?php echo CHtml::label('При создании международной перевозки', 'mail_transport_create_1'); ?>
		</div>
		<div class="row">
		<?php echo CHtml::checkBox('mail_transport_create_2', (bool)$model['mail_transport_create_2']); ?>
		<?php echo CHtml::label('При создании местной перевозки', 'mail_transport_create_2'); ?>
		</div>
		<div class="row">
		<?php echo CHtml::checkBox('mail_kill_rate', (bool)$model['mail_kill_rate']); ?>
		<?php echo CHtml::label('Если была перебита ставка', 'mail_kill_rate'); ?>                
		</div>
		<div class="row">
		<?php echo CHtml::checkBox('mail_deadline', (bool)$model['mail_deadline']); ?>
		<?php echo CHtml::label('При закрытии перевозки', 'mail_deadline'); ?>                
		</div>
		<div class="row">
		<?php echo CHtml::checkBox('mail_before_deadline', (bool)$model['mail_before_deadline']); ?>
		<?php echo CHtml::label('За ' . Yii::app()->params['minNotyfy'] . ' минут до закрытия ставки', 'mail_before_deadline'); ?>                
		</div>
	</fieldset>
	<fieldset>
		<legend>Настройки для оповещения онлайн</legend>
		<?php //echo CHtml::errorSummary($model); ?>
		
		<div class="row">
		<?php echo CHtml::checkBox('site_transport_create_1', (bool)$model['site_transport_create_1']); ?>
		<?php echo CHtml::label('При создании международной перевозки', 'site_transport_create_1'); ?>
		</div>
		<div class="row">
		<?php echo CHtml::checkBox('site_transport_create_2', (bool)$model['site_transport_create_2']); ?>
		<?php echo CHtml::label('При создании местной перевозки', 'site_transport_create_2'); ?>
		</div>
		<div class="row">
		<?php echo CHtml::checkBox('site_kill_rate', (bool)$model['site_kill_rate']); ?>
		<?php echo CHtml::label('Если была перебита ставка', 'site_kill_rate'); ?>                
		</div>
		<div class="row">
		<?php echo CHtml::checkBox('site_deadline', (bool)$model['site_deadline']); ?>
		<?php echo CHtml::label('При закрытии перевозки', 'site_deadline'); ?>                
		</div>
		<div class="row">
		<?php echo CHtml::checkBox('site_before_deadline', (bool)$model['site_before_deadline']); ?>
		<?php echo CHtml::label('За ' . Yii::app()->params['interval'] . ' минут до закрытия ставки', 'site_before_deadline'); ?>                
		</div>
	</fieldset>
	<div class="row submit">
	<?php 
	    echo CHtml::submitButton('Сохранить', array('class' => 'r-submit')); 
        //btn-green btn
		//echo CHtml::hiddenField('id', Yii::app()->user->_id);
	?>
	</div>
<?php echo CHtml::endForm(); ?>
</div>



