<?php
    $header_form = '"'.$model->location_from.' &mdash; '.$model->location_to . '"';
    $submit_text = 'Сохранить';
    $close_text = 'Закрыть';
    $delete_button = CHtml::tag('button', array(
            'id'=>'delete-transport',
            'type'=>'button',
            'class'=>'btn-admin btn-del',
            'name'=>$model->id,
        ), 'Удалить перевозку'
    );
    $alertMsg = Yii::app()->user->getFlash('message');
    $errorMsg = Yii::app()->user->getFlash('message');

    $duplicate_button = CHtml::link('Копировать', '/admin/transport/duplicatetransport/id/'.$model->id, array('id'=>'dup_'.$model->id,'class'=>'btn-admin'));
    $action = '/admin/transport/edittransport/id/'.$model->id;
    $creator = '';
    $delTransport = (string)Transport::DEL_TRANSPORT;
    $draftTransport = (string)Transport::DRAFT_TRANSPORT;

    if (!$model->id) {
        $submit_text = 'Создать';
        $close_text = 'Закрыть';
        $name = 'new';
        $header_form = '';
        $action = '/admin/transport/createtransport/';
        unset($delete_button);
        unset($duplicate_button);
    } else if(!empty($model->user_id)) {
        if(is_numeric($model->user_id)){
            $userModel = AuthUser::model()->findByPk($model->user_id);
        } else {
            $userModel = AuthUser::model()->find('login like :search', array(':search' => $model->user_id));
        }
        $creator = $userModel->surname.' '.$userModel->name.' '.$userModel->secondname;
    }
?>
<script>
$(function(){
    alertify.set({ delay: 40000 });
    <?php if ($alertMsg) :?>
        alertify.success("<?php echo $alertMsg; ?>");
    <?php elseif ($errorMsg): ?>
        alertify.error("<?php echo $errorMsg; ?>");
    <?php endif; ?>
});
</script>
<div class="total">
    <div class="left">
        <?php if (!$model->id): ?>
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
<?php
    echo CHtml::button($close_text,array('id'=>'close-transport', 'class'=>'btn-admin'));
    if($model->status != $delTransport) echo $delete_button;
    echo $duplicate_button;
    if($model->status != $delTransport) echo CHtml::submitButton($submit_text,array('id'=>'but_'.$model->id,'class'=>'btn-admin')); 
?>
</div>
<?php if ($model->id): ?>
    <?php if($model->status != $delTransport || $model->status != $draftTransport): ?>
        <div class="link-to-frontend"><a target="_blank" href="<?php echo Yii::app()->getBaseUrl(true) ?>/transport/description/id/<?php echo $model->id ?>/">Перейти к перевозке</a></div>
    <?php endif; ?>
    <div class="additional-info"> 
        <?php 
        if(!empty($creator)){
            echo 'Создатель: '.$creator.' ('.date("d.m.Y H:i", strtotime($model->date_published)).')';
        } else {
            echo 'Опубликовано: '.date("d.m.Y H:i", strtotime($model->date_published));
        }
        ?>
    </div>
<?php endif; ?>
<div class="field">
<?php echo $form->error($model, 'type');
    echo $form->labelEx($model, 'type');
    if (!$model->id) echo $form->dropDownList($model, 'type', Transport::$group);
    else echo $form->dropDownList($model, 'type', Transport::$group, array('disabled'=>true));
?>
</div>
<div class="field">
<?php echo $form->error($model, 't_id'); 
    echo $form->labelEx($model, 't_id');
    if($model->status == $delTransport) echo $form->textField($model, 't_id', array('disabled'=>true));
    else echo $form->textField($model, 't_id');
?>    
</div>
<div class="field">
<?php  echo $form->error($model, 'location_from'); 
    echo $form->labelEx($model, 'location_from');
    if (!$model->id) echo $form->textField($model, 'location_from');
    else echo $form->textField($model, 'location_from', array('disabled'=>true));
?>    
</div>
<div class="field">
<?php  echo $form->error($model, 'location_to'); 
    echo $form->labelEx($model, 'location_to');
    if (!$model->id) echo $form->textField($model, 'location_to');
    else echo $form->textField($model, 'location_to', array('disabled'=>true));
?>    
</div>
<div class="field custom">
<?php  echo $form->error($model, 'customs_clearance_EU'); 
    echo $form->labelEx($model, 'customs_clearance_EU');
    if (!$model->id) echo $form->textField($model, 'customs_clearance_EU');
    else echo $form->textField($model, 'customs_clearance_EU', array('disabled'=>true));
?>    
</div>
<div class="field custom">
<?php  echo $form->error($model, 'customs_clearance_RF'); 
    echo $form->labelEx($model, 'customs_clearance_RF');
    if (!$model->id) echo $form->textField($model, 'customs_clearance_RF');
    else echo $form->textField($model, 'customs_clearance_RF', array('disabled'=>true));
?>    
</div>
<div class="field">
<?php echo $form->error($model, 'start_rate'); 
    echo $form->labelEx($model, 'start_rate');
    if (!$model->id) echo $form->textField($model, 'start_rate');
    else echo $form->textField($model, 'start_rate', array('disabled'=>true));
?>    
</div>
<div class="field">
<?php echo $form->error($model, 'currency');
    echo $form->labelEx($model, 'currency');
    if (!$model->id) echo $form->dropDownList($model, 'currency', Transport::$currencyGroup);
    else echo CHtml::textField('currency', Transport::$currencyGroup[$model->currency], array('disabled'=>true));
?>
</div>
<div class="field">
<?php  echo $form->error($model, 'description'); 
    echo $form->labelEx($model, 'description');
    if (!$model->id) echo $form->textArea($model, 'description');
    else echo $form->textArea($model, 'description', array('disabled'=>true));
?>    
</div>
<div class="field">
<?php  echo $form->error($model, 'auto_info'); 
    echo $form->labelEx($model, 'auto_info');
    if (!$model->id) echo $form->textArea($model, 'auto_info');
    else echo $form->textArea($model, 'auto_info', array('disabled'=>true));
?>    
</div>
<div class="field">
<?php  echo $form->error($model, 'pto'); 
    echo $form->labelEx($model, 'pto');
    if (!$model->id) echo $form->textArea($model, 'pto');
    else echo $form->textArea($model, 'pto', array('disabled'=>true));
?>    
</div>
<div class="field">
<?php echo $form->error($model, 'status');
    echo $form->labelEx($model, 'status');
    if($model->status == $delTransport) echo CHtml::textField('TransportForm[status]', 'Удалена ('.date('d.m.Y H:i', strtotime($model->del_date)).')', array('disabled'=>true));//$form->textField($model, 'status', array('disabled'=>true));
    else echo $form->dropDownList($model, 'status', Transport::$status); ?>
</div>
<?php if($model->status == $delTransport): ?>
    <div class="field">
    <?php 
    echo $form->labelEx($model, 'del_reason');
    echo $form->textArea($model, 'del_reason', array('disabled'=>true));
    ?>
    </div>
<?php endif; ?>
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
        if (!$model->id) echo $form->textField($model, 'date_close'); 
        else echo $form->textField($model, 'date_close', array('disabled'=>true)); 
    ?>
</div>
<div class="field">
<?php  echo $form->error($model, 'date_from'); 
    echo $form->labelEx($model, 'date_from');
    $model->date_from = date("d-m-Y H:i", strtotime($model->date_from));
    if (!$model->id) echo $form->textField($model, 'date_from');
    else echo $form->textField($model, 'date_from', array('disabled'=>true));
?>    
</div>
<div class="field">
<?php echo $form->error($model, 'date_to'); 
    echo $form->labelEx($model, 'date_to');
    $model->date_to = date("d-m-Y H:i", strtotime($model->date_to));
    if (!$model->id) echo $form->textField($model, 'date_to'); 
    else echo $form->textField($model, 'date_to', array('disabled'=>true));
?>    
</div>
<div class="field custom">
<?php echo $form->error($model, 'date_to_customs_clearance_RF'); 
    echo $form->labelEx($model, 'date_to_customs_clearance_RF');
    if (!$model->id) echo $form->textField($model, 'date_to_customs_clearance_RF'); 
    else echo $form->textField($model, 'date_to_customs_clearance_RF', array('disabled'=>true)); 
?>    
</div>

<div class="field">
<?php echo $form->hiddenField($model, 'id'); ?>
</div>
<?php if ($model->id): ?>
<div>
    <?php if(count($points)): ?>
    <div class="header-h4">Промежуточные пункты маршрута</div>
    <table class="points-all" cellspacing='0'>
        <tr>
            <th>Название пункта</th>
            <th>Дата</th>
        </tr>
        <?php foreach ($points as $point){
            echo '<tr>';
            echo '<td>'.$point->point.'</td>';
            echo '<td>'.((!empty($point->date))? date("d.m.Y H:i", strtotime($point->date)) : '').'</td>';
            echo '</tr>';
        }
        /*foreach ($points as $point){
            echo '<li class="point">';
            echo '<span>';
                echo '<span class="p-point">'.$point->point.'</span>';
                echo CHtml::textField('Points['.$point->id.']', $point->point, array('class'=>''));
            echo '</span>';
            
            echo '<span>'.((!empty($point->date))? date("d-m-Y H:i", strtotime($point->date)) : '').'</span>';
            //echo '<span class="del-col del-row"></span>';
            echo '<span></span>';
            echo '</li>';
        }
        */
        ?> 
    </table>
    <?php endif; ?>
</div>
<div>
    <div class="header-h4">Список ставок</div>
    <?php if(count($rates)): ?>
    <table class="rates-all" cellspacing='0'>
        <tr>
            <th>Дата</th>
            <th>Размер ставки</th>
            <th>Компания</th>
            <th></th>
        </tr>
        <?php foreach ($rates as $item){
            if($minRateId == $item['id']) echo '<tr class="win" r-id="'.$item['id'].'">';
            else echo '<tr r-id="'.$item['id'].'">';
            echo '<td>'.date("d.m.Y H:i:s", strtotime($item['date'])).'</td>';
            echo '<td>'.$item['price'].'</td>';
            echo '<td>'.$item['company'].'</td>';
            echo '<td>'.CHtml::button('',array('class'=>'del-col del-row')).'</td>';
            echo '</tr>';
        }
        ?>
    </table>
    <?php else: echo '<div class="no-rates">Нет ставок</div>';
    endif; ?>
</div>
<?php endif; ?>
<?php $this->endWidget(); ?> 
</div>
</div>
</div>
<div>
    <?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id' => 'delTr',
        'options' => array(
            'title' => 'Подтверждение удаления',
            'autoOpen' => false,
            'modal' => true,
            'resizable'=> false,
        ),
    ));
    ?>
    <div class="row">
        <span>Укажите причину удаления:</span> 
        <?php echo CHtml::textArea('delete-reason', '', array('trId'=>$model->id)); ?>
    </div>
    <div class="rate-button">
    <?php echo CHtml::button('Удалить',array('id' => 'setDelTr', 'class' => 'btn')); ?>
    </div>
    <div class="rate-button">
    <?php echo CHtml::button('Отмена',array('id' => 'abordDelTr', 'class' => 'btn')); ?>
    </div>
    <?php
        $this->endWidget('zii.widgets.jui.CJuiDialog');
    ?>
</div>
<div>
    <?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id' => 'delRate',
        'options' => array(
            'title' => 'Подтверждение удаления ставки',
            'autoOpen' => false,
            'modal' => true,
            'resizable'=> false,
        ),
    ));
    ?>
    <div class="row">
        <span>Вы уверены, что хотите удалить ставку?</span>
    </div>
    <div class="rate-button">
    <?php echo CHtml::button('Удалить',array('id' => 'setDelRate', 'class' => 'btn')); ?>
    </div>
    <div class="rate-button">
    <?php echo CHtml::button('Отмена',array('id' => 'abordDelRate', 'class' => 'btn')); ?>
    </div>
    <?php
        $this->endWidget('zii.widgets.jui.CJuiDialog');
    ?>
</div>
<script>
$(document).ready(function() {
    var activeType = parseInt(sessionStorage.getItem('transportType'));
    $('#close-transport').click(function(){
        if(isNaN(activeType)) document.location.href = "<?php echo Yii::app()->getBaseUrl(true) ?>/admin/transport";
        else document.location.href = "<?php echo Yii::app()->getBaseUrl(true) ?>/admin/transport/index/transportType/"+activeType;
    });
    var editor = new ЕditTransport();
    editor.initCalendar();
    editor.showFieldsForInternational();
    $('#TransportForm_type').change(function(){
         editor.showFieldsForInternational();
    });
    
    <?php //if(Yii::app()->user->checkAccess('editRate')): ?>
        //editor.initRateEditor(); // редактирование ставок
        // сортировка перетаскиванием промежуточных пунктов
        /* $( "#points-all" ).sortable({
            revert: true
        });*/
    <?php //endif; ?>
});
</script>


