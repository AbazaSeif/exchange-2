<script>
$(function() {
	$.datepicker.regional['ru'] = {
		closeText: 'Закрыть',
		prevText: '&#x3c;Пред',
		nextText: 'След&#x3e;',
		currentText: 'Сегодня',
		monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
		monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
		dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
		dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
		dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
	};
    $.datepicker.setDefaults($.datepicker.regional['ru']); 
	$( "#date_from" ).datetimepicker({
		dateFormat: 'yy-mm-dd',
	    timeFormat: 'HH:mm',
	});
	$( "#date_to" ).datetimepicker({
		dateFormat: 'yy-mm-dd',
	    timeFormat: 'HH:mm',
	});
});
</script>

<?php
    $header_form = 'Редактирование перевозки "'.$model->location_from.' &mdash; '.$model->location_to . '"';
    $submit_text = 'Сохранить';
	$delete_button = CHtml::link('Удалить перевозку', '/admin/transport/deletetransport/id/'.$model->id, array('id'=>'del_'.$model->id,'class'=>'btn del', 'onclick'=>'return confirm("Внимание! Перевозка будет безвозвратно удалена. Продолжить?")'));
    
	$action = '/admin/transport/edittransport/id/'.$model->id;
	$group = array(
	    0=>'Международная',
	    1=>'Локальная',
	);
	if ($model->isNewRecord){
        $submit_text = 'Создать';
        $name = 'new';
        $header_form = 'Создание новой перевозки';
        $action = '/admin/transport/createtransport/';
        unset($delete_button);
    }
?>
<div class="form">
<div class="header-form">
    <?php echo $header_form; ?>
</div>
<?php 
	$form = $this->beginWidget('CActiveForm', array('id'=>'form'.$model->id,
    'action'=>$action,
    'enableClientValidation'=>true,
    'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'afterValidate'=>'js:function( form, data, hasError ) 
			{     
				if( hasError ){
					return false;
				}
				else{
					return true;
				}
			}'
    ),));
?>
<div class="buttons">
<?php  echo $delete_button; 
    echo CHtml::button('Закрыть перевозку',array('onclick'=>'$(".total .right").html(" ");','class'=>'btn'));
    echo CHtml::submitButton($submit_text,array('id'=>'but_'.$name,'class'=>'btn btn-green')); 
?>
</div>
<div class="field">
<?php echo $form->error($model, 'start_rate'); 
      echo CHtml::label('Начальная ставка', 'start_rate'); 
      echo $form->textField($model, 'start_rate');
?>    
</div>
<div class="field">
<?php  echo $form->error($model, 'location_from'); 
    echo CHtml::label('Пункт отправки', 'location_from');
    echo $form->textField($model, 'location_from');
?>    
</div>
<div class="field">
<?php  echo $form->error($model, 'location_to'); 
    echo $form->labelEx($model, 'location_to');
    echo $form->textField($model, 'location_to');?>    
</div>
<div class="field">
<?php  echo $form->error($model, 'description'); 
    echo $form->labelEx($model, 'description');
    echo $form->textArea($model, 'description');?>    
</div>
<div class="field">
<?php  echo $form->error($model, 'auto_info'); 
	echo $form->labelEx($model, 'auto_info');
    echo $form->textArea($model, 'auto_info');?>    
</div>
<div class="field">
<?php  echo $form->error($model, 'date_from'); 
	echo $form->labelEx($model, 'date_from');
    echo CHtml::textField('date_from', date("Y-m-d H:i", strtotime($model->date_from))) ?>    
</div>
<div class="field">
<?php  echo $form->error($model, 'date_to'); 
	echo $form->labelEx($model, 'date_to');
    echo CHtml::textField('date_to', date("Y-m-d H:i", strtotime($model->date_to)), array('name'=>111)) ?>    
</div>
<div class="field">
<?php echo $form->error($model, 'type');
    echo $form->labelEx($model, 'type');
    echo $form->dropDownList($model, 'type', $group); ?>
</div>
<?php $this->endWidget();?> 
</div>