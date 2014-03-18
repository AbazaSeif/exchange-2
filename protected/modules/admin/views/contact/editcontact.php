<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    $submit_text = 'Сохранить';
    $name = $model->id;
    $allCompanies = $this->getCompanies();
    $companies = array();
    $delete_button = CHtml::link('Удалить', '/admin/contact/deletecontact/id/'.$model->id, array('id'=>'del_'.$model->name,'class'=>'btn del', 'onclick'=>'return confirm("Внимание! Контактное лицо будет безвозвратно удалено. Продолжить?")'));
    $header_form = 'Редактирование контактного лица "'.$model->name . ' ' . $model->surname . '"';
    $action = '/admin/contact/editcontact/id/'.$model->id;
    //if ($model->isNewRecord) {
    if (!$model->id) {
        $submit_text = 'Подтвердить';
        $name = 'new';
        $header_form = 'Создание нового контактного лица';
        $action = '/admin/contact/createcontact/';
        unset($delete_button);
    }
    
    foreach($allCompanies as $one){
        $companies[$one['id']] = $one['company'];
    }
?>
<div class="form">
<div class="header-form">
    <?php echo $header_form; ?>
</div>
<?php $form = $this->beginWidget('CActiveForm', array('id'=>'contactform',
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
    echo CHtml::button('Закрыть',array('onclick'=>'$(".total .right").html(" ");','class'=>'btn'));
    echo CHtml::submitButton($submit_text,array('id'=>'but_'.$name,'class'=>'btn btn-green')); ?>
</div>
<div class="firm field">
<?php echo $form->error($model, 'u_id');
    echo $form->labelEx($model, 'u_id');
    echo $form->dropDownList($model, 'u_id', $companies); ?>
</div>
<div class="surname field">
<?php  echo $form->error($model, 'surname'); 
    echo $form->labelEx($model, 'surname');
    echo $form->textField($model, 'surname'); ?>
</div>
<div class="name field">
<?php  echo $form->error($model, 'name'); 
    echo $form->labelEx($model, 'name');
    echo $form->textField($model, 'name');?>
</div>
<div class="secondname field">
<?php  echo $form->error($model, 'secondname'); 
    echo $form->labelEx($model, 'secondname');
    echo $form->textField($model, 'secondname'); ?>
</div>
<div class="secondname field">
<?php 
    if($model->id){
        echo $form->error($model, 'password_confirm'); 
        echo $form->labelEx($model, 'password_confirm');
        echo $form->passwordField($model, 'password_confirm'); 
    } else {
        echo $form->error($model, 'password'); 
        echo $form->labelEx($model, 'password');
        echo $form->passwordField($model, 'password'); 
    }
?>
</div>
<div class="email field">
<?php  echo $form->error($model, 'email');
    echo $form->labelEx($model, 'email');
    echo $form->emailField($model, 'email'); ?>
</div>
<div class="phone field">
<?php  echo $form->error($model, 'phone');
    echo $form->labelEx($model, 'phone');
    echo $form->textField($model, 'phone'); ?>
</div>
<div class="phone2 field">
<?php  echo $form->error($model, 'phone2');
    echo $form->labelEx($model, 'phone2');
    echo $form->textField($model, 'phone2'); ?>
</div>
<div class="status field">
<?php  echo $form->error($model, 'status');
    echo $form->labelEx($model, 'status');
    echo $form->dropDownList($model, 'status', User::$userStatus); ?>
</div>
<?php if($model->id):?>
<div style="display:none;">
<?php  echo $form->hiddenField($model, 'password'); ?>
</div>
<?php
    endif;
    $this->endWidget();
?> 
</div>
