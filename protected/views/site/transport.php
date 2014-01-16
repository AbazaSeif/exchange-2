<!--div class="form">
<?php echo CHtml::beginForm('/site/saveTransport/', 'POST', array('id'=>'user')); ?>
	<div class="row">
	<?php //echo CHtml::checkBox('mail_transport_create_1'); ?>
	<?php //echo CHtml::label('При создании международной перевозки', 'mail_transport_create_1'); ?>
	</div>
	<div class="row submit">
	<?php echo CHtml::submitButton('Сохранить'); ?>
	</div>
<?php echo CHtml::endForm(); ?>
</div-->

<div class="form">
<?php //echo CHtml::beginForm(); ?>
<table class="simple-little-table">
<tr><th class="row-number">№</th><th class="row-number">ID</th><th>Пункт отправки</th><th>Пункт назначения</th><th></th></tr>
<?php foreach($model as $k=>$item): ?>
<?php if($k%2 == 0):?>
<tr class='even'>
<?php else: ?>
<tr>
<?php endif; ?>
<td><?php echo $k + 1; ?></td>
<td><?php echo $item['id']; ?></td>
<td><?php echo $item['location_from']; ?></td>
<td><?php echo $item['location_to']; ?></td>
<td><?php echo 'img'; ?></td>
</tr>
<?php endforeach; ?>
</table>
 
<?php //echo CHtml::submitButton('Сохранить'); ?>
<?php //echo CHtml::endForm(); ?>



