<?php
    $header_form = 'Редактирование перевозки "'.$model->location_from.' &mdash; '.$model->location_to . '"';
    $submit_text = 'Сохранить';
    $delete_button = CHtml::link('Удалить перевозку', '/admin/transport/deletetransport/id/'.$model->id, array('id'=>'del_'.$model->id,'class'=>'btn del', 'onclick'=>'return confirm("Внимание! Перевозка будет безвозвратно удалена. Продолжить?")'));

    $action = '/admin/transport/edittransport/id/'.$model->id;
    // вынести !!!
    $group = array(
        0=>'Международная',
        1=>'Региональная',
    );
    ////////////////////
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
<?php $form = $this->beginWidget('CActiveForm', array('id'=>'form'.$model->id,
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
<?php echo $form->error($model, 'date_to'); 
	echo $form->labelEx($model, 'date_to');
    echo CHtml::textField('date_to', date("Y-m-d H:i", strtotime($model->date_to))) ?>    
</div>
<div class="field">
<?php echo $form->error($model, 'type');
    echo $form->labelEx($model, 'type');
    echo $form->dropDownList($model, 'type', $group); ?>
</div>
<div class="field">
<?php echo $form->hiddenField($model, 'id'); ?>
</div>
<div>
    <div class="header-h4">Все ставки</div>
    <?php if(count($rates)): ?>
    <ul id="rates-all">
        <li>
           <span>Дата</span>
           <span>Размер ставки</span>
           <span class="del-col"></span>
        </li>
    <?php foreach ($rates as $item){
            echo '<li class="item">';
            echo '<span>'.$item->date.'</span>';
            echo '<span>';
            echo '<span class="price">'.$item->price.'</span>';
            echo CHtml::textField('Rates['.$item->id.']', $item->price, array('class'=>'form-price'));
            echo '</span>';
            echo '<span class="del-col del-row"></span>';
            echo '</li>';
        }?>
    </ul>
    <?php else: echo '<div class="no-rates">Нет ставок</div>';
    endif; ?>
</div>
<?php $this->endWidget(); ?> 
</div>
<script>
$(document).ready(function(){
    var editor = new ЕditTransport();
    editor.initCalendar();
    <?php if(Yii::app()->user->checkAccess('editRate')): ?>
        editor.initRateEditor();
    <?php endif; ?>
});
</script>