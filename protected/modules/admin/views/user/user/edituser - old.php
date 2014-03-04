<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    $submit_text = 'Сохранить';
    $name = $model->id;
    $delete_button = CHtml::link('Удалить пользователя', '/admin/user/deleteuser/id/'.$model->id, array('id'=>'del_'.$model->name,'class'=>'btn del', 'onclick'=>'return confirm("Внимание! Пользователь будет безвозвратно удален. Продолжить?")'));
    $header_form = 'Редактирование пользователя '.$model->login;
    $action = '/admin/user/edituser/id/'.$model->id;
    if ($model->isNewRecord){
        $submit_text = 'Создать';
        $name = 'new';
        $header_form = 'Создание нового пользователя';
        $action = '/admin/user/createuser/';
        unset($delete_button);
    }
?>
<div class="form">
<div class="header-form">
    <? echo $header_form; ?>
</div>
<? $form = $this->beginWidget('CActiveForm', array('id'=>'form'.$model->id,
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
<?  echo $delete_button; 
    echo CHtml::button('Закрыть пользователя',array('onclick'=>'$(".total .right").html(" ");','class'=>'btn'));
    echo CHtml::submitButton($submit_text,array('id'=>'but_'.$name,'class'=>'btn btn-green')); ?>
</div>

<?php 
$noshow = array('id', 'password');
foreach ($model as $itm=>$v)
{
    if(!in_array($itm, $noshow)):
        echo '<div class="'.$itm.' field">';
        echo $form->error($model, $itm); 
        echo $form->labelEx($model, $itm);
        echo $form->textField($model, $itm);
        echo '</div>';
    endif;
}
?>
<div class="password field">
<?  echo CHtml::label('Пароль', 'User_password');
    echo CHtml::passwordField('User_password', '', array('id'=>'User_password')); ?>
</div>
<div style="display:none;">
<?  echo $form->hiddenField($model, 'password'); ?>
</div>
<? $this->endWidget();?> 
</div>