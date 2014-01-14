<div>
	<fieldset>
	<legend>Не прочитанные события</legend>
	<?php if(!empty($newEvents)): ?>
		<?php foreach($newEvents as $event): ?>
			<div class="row">
				<?php echo CHtml::link('Перевозка №' . $event['transport_id'] . '', array('site/description/', 'id'=>$event['transport_id'])); ?>
				<span>
				   <?php echo $this->getEventMessage($event['event_type']); ?>
				</span>
			</div>
		<?php endforeach; ?>
    <?php else: ?>
	    <div>Нет новых событий</div>
	<?php endif;?>
	</fieldset>

	<?php if(!empty($oldEvents)): ?>
		<fieldset>
		<legend>Прочитанные события</legend>
		<?php foreach($oldEvents as $event): ?>
			<div class="row">
				<?php echo CHtml::link('Перевозка №' . $event['transport_id'] . '', array('site/description/', 'id'=>$event['transport_id'])); ?>
				<span>
				   <?php echo $this->getEventMessage($event['event_type']); ?>
				</span>
			</div>
		<?php endforeach; ?>
		</fieldset>
	<?php endif;?>
</div>