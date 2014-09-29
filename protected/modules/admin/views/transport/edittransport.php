<?php
    $header_form = '"'.$model->location_from.' &mdash; '.$model->location_to . '"';
    $submit_text = 'Сохранить';
    $close_text = 'Закрыть';
    $delete_button = CHtml::link('Удалить перевозку', '/admin/transport/deletetransport/id/'.$model->id, array('id'=>$model->id,'class'=>'btn-admin btn-del', 'onclick'=>'return confirm("Внимание! Перевозка будет безвозвратно удалена. Продолжить?")'));
    $duplicate_button = CHtml::link('Копировать', '/admin/transport/duplicatetransport/id/'.$model->id, array('id'=>'dup_'.$model->id,'class'=>'btn-admin'));//, 'onclick'=>'return confirm("Внимание! Перевозка будет безвозвратно удалена. Продолжить?")'));
    $action = '/admin/transport/edittransport/id/'.$model->id;
    $creator = '';
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
<?php
    if ($mess = Yii::app()->user->getFlash('message')){
        echo '<div class="trMessage success">'.$mess.'</div>';
    } else if ($mess = Yii::app()->user->getFlash('error')) {
        echo '<div class="trMessage error">'.$mess.'</div>';
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
<?php
    echo CHtml::button($close_text,array('id'=>'close-transport', 'class'=>'btn-admin'));
    echo $delete_button;
    echo $duplicate_button;
    echo CHtml::submitButton($submit_text,array('id'=>'but_'.$name,'class'=>'btn-admin')); 
?>
</div>
<?php if ($model->id): ?>
    <div class="link-to-frontend"><a target="_blank" href="<?php echo Yii::app()->getBaseUrl(true) ?>/transport/description/id/<?php echo $model->id ?>/">Перейти к перевозке</a></div>
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
    //echo $form->dropDownList($model, 'type', Transport::$group); 
    if (!$model->id) echo $form->dropDownList($model, 'type', Transport::$group);
    else echo $form->dropDownList($model, 'type', Transport::$group, array('disabled'=>true));
?>
</div>
<div class="field">
<?php echo $form->error($model, 't_id'); 
    echo $form->labelEx($model, 't_id');
    echo $form->textField($model, 't_id');
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
    //echo $form->dropDownList($model, 'currency', Transport::$currencyGroup, array('disabled'=>true));
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
        if (!$model->id) echo $form->textField($model, 'date_close'); 
        else echo $form->textField($model, 'date_close', array('disabled'=>true)); 
    ?>
</div>
<div class="field">
<?php  echo $form->error($model, 'date_from'); 
    echo $form->labelEx($model, 'date_from');
    $model->date_from = date("d-m-Y H:i", strtotime($model->date_from));
    if (!$model->id) echo $form->textField($model, 'date_from');
    //else echo $form->textField($model, 'date_from', array('disabled'=>true));
    else echo $form->textField($model, 'date_from');
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
    //$model->date_to_customs_clearance_RF = date("d-m-Y H:i", strtotime($model->date_to_customs_clearance_RF));
    if (!$model->id) echo $form->textField($model, 'date_to_customs_clearance_RF'); 
    else echo $form->textField($model, 'date_to_customs_clearance_RF', array('disabled'=>true)); 
?>    
</div>

<div class="field">
<?php echo $form->hiddenField($model, 'id'); ?>
</div>
<?php //if (!$model->isNewRecord): ?>
<?php if ($model->id): ?>
<div>
    <?php if(count($points)): ?>
    <div class="header-h4">Промежуточные пункты маршрута</div>
    <ul id="points-all">
        <li>
           <span>Название пункта</span>
           <span>Дата</span>
           <span class="del-col"></span>
        </li>
    <?php foreach ($points as $point){
            echo '<li class="point">';
            echo '<span>';
                echo '<span class="p-point">'.$point->point.'</span>';
                echo CHtml::textField('Points['.$point->id.']', $point->point, array('class'=>''));
            echo '</span>';
            
            echo '<span>'.((!empty($point->date))? date("d-m-Y H:i", strtotime($point->date)) : '').'</span>';
            //echo '<span class="del-col del-row"></span>';
            echo '<span></span>';
            echo '</li>';
        }?>
    </ul>
    <?php endif; ?>
</div>
<div>
    <div class="header-h4">Список ставок</div>
    <div id="rate-message" class="hide"><div></div></div>
    <?php if(count($rates)): ?>
    <ul id="rates-all">
        <li>
           <span>Дата</span>
           <span>Компания</span>
           <span>Размер ставки</span>
           <span class="del-col"></span>
        </li>
    <?php foreach ($rates as $item){
        //var_dump($item);
            if($minRateId == $item['id']) echo '<li class="item win" r-id="'.$item['id'].'">';
            else echo '<li class="item" r-id="'.$item['id'].'">';
            echo '<span>'.date("d-m-Y H:i:s", strtotime($item['date'])).'</span>';
            echo '<span>' . $item['company'] . '</span>';
            echo '<span>';
            echo '<span class="price">'.$item['price'].'</span>';
            echo CHtml::textField('Rates['.$item['id'].']', $item['price'], array('class'=>'form-price'));
            echo '</span>';
            echo '<span>' . '<span class="hide">' . CHtml::button('',array('class'=>'del-col confirm-row')) . '</span>' . '<span>' . CHtml::button('',array('class'=>'del-col del-row')) . '</span>';
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
    
    <?php if(Yii::app()->user->checkAccess('editRate')): ?>
        //editor.initRateEditor(); // редактирование ставок
        // сортировка перетаскиванием промежуточных пунктов
        /* $( "#points-all" ).sortable({
            revert: true
        });*/
    <?php endif; ?>
});
</script>