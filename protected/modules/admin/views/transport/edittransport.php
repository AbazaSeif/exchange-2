<?php
    $header_form = '"'.$model->location_from.' &mdash; '.$model->location_to . '"';
    $submit_text = 'Сохранить';
    $close_text = 'Закрыть редактирование';
    $delete_button = CHtml::link('Удалить перевозку', '/admin/transport/deletetransport/id/'.$model->id, array('id'=>'del_'.$model->id,'class'=>'btn del', 'onclick'=>'return confirm("Внимание! Перевозка будет безвозвратно удалена. Продолжить?")'));
    
    $action = '/admin/transport/edittransport/id/'.$model->id;
    if ($model->isNewRecord) {
        $submit_text = 'Создать';
        $close_text = 'Закрыть';
        $name = 'new';
        $header_form = '';
        $action = '/admin/transport/createtransport/';
        unset($delete_button);
    }
?>

<div class="total">
    <div class="left">
        <?php if ($model->isNewRecord): ?>
        <h1>Создание перевозки</h1>
        <?php else: ?>
        <h1>Редактирование перевозки</h1>
        <?php endif?>
        <div class="header-form">
            <?php echo $header_form; ?>
        </div>
        <div>Для того, чтобы вернуться к списку перевозок нажмите кнопку "<?php echo $close_text?>"
        </div>
    </div>
<div class="right">
<div class="form">
<?php
    if ($mess = Yii::app()->user->getFlash('message')){
        echo '<div class="trMessage success">'.$mess.'</div>';
    }
?>
<?php $form = $this->beginWidget('CActiveForm', array(
        'id'=>'transport-form',
        'action'=>$action,
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
    ));
?>
<div class="buttons">
<?php  echo $delete_button; 
    echo CHtml::button($close_text,array('id'=>'close-transport', 'class'=>'btn'));
    echo CHtml::submitButton($submit_text,array('id'=>'but_'.$name,'class'=>'btn btn-green')); 
?>
</div>
<div class="field">
<?php echo $form->error($model, 'type');
    echo $form->labelEx($model, 'type');
    echo $form->dropDownList($model, 'type', Transport::$group); ?>
</div>
<div class="field">
<?php  echo $form->error($model, 'location_from'); 
    echo $form->labelEx($model, 'location_from');
    echo $form->textField($model, 'location_from');
?>    
</div>
<div class="field">
<?php  echo $form->error($model, 'location_to'); 
    echo $form->labelEx($model, 'location_to');
    echo $form->textField($model, 'location_to');?>    
</div>
<div class="field">
<?php echo $form->error($model, 'start_rate'); 
    echo $form->labelEx($model, 'start_rate');
    echo $form->textField($model, 'start_rate');
?>    
</div>
<div class="field">
<?php echo $form->error($model, 'currency');
    echo $form->labelEx($model, 'currency');
    echo $form->dropDownList($model, 'currency', Transport::$currencyGroup); ?>
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
<?php echo $form->error($model, 'status');
    echo $form->labelEx($model, 'status');
    echo $form->dropDownList($model, 'status', Transport::$status); ?>
</div>
<div class="field">
    <?php
        echo CHtml::label('Часовой Пояс', 'timer_label');
        echo CHtml::textField('timer_label', 'Московское время', array('disabled'=>true));
    ?>
</div>
<div class="field">
    <?php
        echo $form->error($model, 'date_close'); 
        echo $form->labelEx($model, 'date_close');
        $model->date_close = date("d-m-Y H:i", strtotime($model->date_close));
        echo $form->textField($model, 'date_close'); 
    ?>
</div>
<div class="field">
<?php  echo $form->error($model, 'date_from'); 
    echo $form->labelEx($model, 'date_from');
    $model->date_from = date("d-m-Y H:i", strtotime($model->date_from));
    echo $form->textField($model, 'date_from'); ?>    
</div>
<div class="field">
<?php echo $form->error($model, 'date_to'); 
    echo $form->labelEx($model, 'date_to');
    $model->date_to = date("d-m-Y H:i", strtotime($model->date_to));
    echo $form->textField($model, 'date_to'); ?>    
</div>

<div class="field">
<?php echo $form->hiddenField($model, 'id'); ?>
</div>
<?php if (!$model->isNewRecord): ?>
<div>
    <?php if(count($points)): ?>
    <div class="header-h4">Промежуточные пункты маршрута</div>
    <ul id="points-all">
        <li>
           <span>Название пункта</span>
           <!--span>Порядок</span-->
           <span class="del-col"></span>
        </li>
    <?php foreach ($points as $point){
            echo '<li class="point">';
            echo '<span>';
                echo '<span class="p-point">'.$point->point.'</span>';
                echo CHtml::textField('Points['.$point->id.']', $point->point, array('class'=>''));
            echo '</span>';
            //echo '<span>'.$point->sort.'</span>';
            echo '<span class="del-col del-row"></span>';
            echo '</li>';
        }?>
    </ul>
    <?php endif; ?>
</div>
<div>
    <div class="header-h4">Список ставок</div>
    <?php if(count($rates)): ?>
    <ul id="rates-all">
        <li>
           <span>Дата</span>
           <span>Компания</span>
           <span>Размер ставки</span>
           <span class="del-col"></span>
        </li>
    <?php foreach ($rates as $item){
            if($minRateId == $item['id']) echo '<li class="item win">';
            else echo '<li class="item">';
            echo '<span>'.$item['date'].'</span>';
            echo '<span>' . $item['company'] .'</span>';
            echo '<span>';
            echo '<span class="price">'.$item['price'].'</span>';
            echo CHtml::textField('Rates['.$item['id'].']', $item['price'], array('class'=>'form-price'));
            echo '</span>';
            echo '<span class="del-col del-row"></span>';
            echo '</li>';
        }?>
    </ul>
    <?php else: echo '<div class="no-rates">Нет ставок</div>';
    endif; ?>
</div>
<?php endif; ?>
<?php $this->endWidget(); ?> 
</div>
</div>
</div>
<script>
$(document).ready(function(){
    var editor = new ЕditTransport();
    editor.initCalendar();
    /*$( "#Transport_date_from" ).datetimepicker({
        dateFormat: 'dd-mm-yy',
        timeFormat: 'HH:mm',
    });

    $( "#Transport_date_to" ).datetimepicker({
        dateFormat: 'dd-mm-yy',
        timeFormat: 'HH:mm',
    });*/
    <?php if(Yii::app()->user->checkAccess('editRate')): ?>
        editor.initRateEditor();
        $( "#points-all" ).sortable({
            revert: true
        });
    <?php endif; ?>
        
        
    $('#close-transport').click(function(){document.location.href = "http://exchange.lbr.test/admin/transport/";})
});
/*
function updateFieldTimerDeadline() {
    var input = $('#Transport_date_from').val();
    m = input.match(/(\d+)-(\d+)-(\d+) (\d+):(\d+)/);
    var startDate = new Date(m[3], m[2]-1, m[1], m[4], m[5]);
    startDate.setHours(startDate.getHours()-<?php echo Yii::app()->params['hoursBefore'] ?>);
    var day = startDate.getDate();
    if(day < 10) day = '0' + day;
    var month = startDate.getMonth() + 1;
    if(month < 10) month = '0' + month;
    var year = startDate.getFullYear();
    var hour = startDate.getHours();
    if(hour < 10) hour = '0' + hour;
    var min = startDate.getMinutes();
    if(min < 10) min = '0' + min;
    var timer = day + '-' + month + '-' + year + ' '+ hour + ':' + min;
    $('#timer_deadline').val(timer);
}
*/
</script>