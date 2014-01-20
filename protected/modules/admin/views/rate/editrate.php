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
	$( "#date" ).datetimepicker({
		dateFormat: 'yy-mm-dd',
	    timeFormat: 'HH:mm',
	});
});
</script>

<?php
    $header_form = 'Редактирование ставки';
    $submit_text = 'Сохранить';
	$delete_button = CHtml::link('Удалить ставку', '/admin/rate/deleterate/id/'.$model->id, array('id'=>'del_'.$model->id,'class'=>'btn del', 'onclick'=>'return confirm("Внимание! Ставка будет безвозвратно удалена. Продолжить?")'));
    
	$action = '/admin/rate/editrate/id/'.$model->id;
	$group = array(
	    0=>'Международная',
	    1=>'Локальная',
	);
	if ($model->isNewRecord){
        $submit_text = 'Создать';
        $name = 'new';
        $header_form = 'Создание новой ставки';
        $action = '/admin/rate/createrate/';
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
    echo CHtml::button('Закрыть ставку',array('onclick'=>'$(".total .right").html(" ");','class'=>'btn'));
    echo CHtml::submitButton($submit_text,array('id'=>'but_'.$name,'class'=>'btn btn-green')); 
?>
</div>
<div class="field">
<?php echo $form->error($model, 'transport_id');
    echo $form->labelEx($model, 'transport_id');
    echo $form->textField($model, 'transport_id'); ?>
</div>
<div class="field">
<?php echo $form->error($model, 'price');
    echo $form->labelEx($model, 'price');
    echo $form->textField($model, 'price'); ?>
</div>
<div class="field">
<?php echo $form->error($model, 'user_id');
    echo $form->labelEx($model, 'user_id');
    echo $form->textField($model, 'user_id'); ?>
</div>
<div class="field">
<?php echo $form->error($model, 'date');
    echo $form->labelEx($model, 'date');
   // echo $form->textField($model, 'date'); 
   echo CHtml::textField('date', date("Y-m-d H:i", strtotime($model->date))) ?> 
</div>
<?php $this->endWidget();?> 
</div>