<?
    $submit_text = 'Сохранить';
    $name = $model->name;
    $delete_button = CHtml::link('Удалить операцию','/admin/user/deleteoperation/name/'.$model->name, array('id'=>'del_'.$model->name,'class'=>'btn del', 'onclick'=>'return confirm("Внимание! Операция будет безвозвратно удалена. Продолжить?")'));
    $header_form = 'Редактирование роли '.$name;
    $action = '/admin/user/editoperation/name/'.$model->name;
    if ($model->isNewRecord){
        $submit_text = 'Создать';
        $name = 'new';
        $header_form = 'Создание новой операции';
        $action = '/admin/user/createoperation/';
        unset($delete_button);
    }
?>
<div class="form">
<div class="header-form">
    <? echo $header_form; ?>
</div>
<? $form = $this->beginWidget('CActiveForm', array('id'=>'form_'.$model->name,
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
    ),)); ?>

<div class="buttons">
<?  echo $delete_button;
    echo CHtml::button('Закрыть операцию', array('onclick'=>'$(".total .right").html(" ");','class'=>'btn'));
    echo CHtml::submitButton($submit_text, array('class'=>'btn btn-green'));
?>
</div>

<div class="name field row">
    <?
    echo $form->error($model, 'name'); 
    echo $form->labelEx($model, 'name');
    echo $form->textField($model, 'name');
    ?>
</div>
<div class="description field">
    <? 
    echo $form->error($model, 'description');
    echo $form->labelEx($model, 'description');
    echo $form->textarea($model, 'description');
    ?>
</div>
<div class="bizrule field">
    <? 
    echo $form->error($model, 'bizrule');
    echo $form->labelEx($model, 'bizrule');
    echo $form->textarea($model, 'bizrule');
    ?>
</div>
<div style="display:none;">
    <?
    echo $form->hiddenField($model,'type');
    echo $form->hiddenField($model,'data');
    ?>
</div>
<? $this->endWidget(); ?>
</div>