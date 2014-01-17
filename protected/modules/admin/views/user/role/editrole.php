<?php
    /*
     * Вид editrole - редактирование или создание роли
     * 
     */
// Стартовые параметры
    $submit_text = 'Сохранить';
    $name = $model->name;
    $delete_button = CHtml::link('Удалить роль', '/admin/user/deleterole/name/'.$model->name, array('id'=>'del_'.$model->name,'class'=>'btn del', 'onclick'=>'return confirm("Внимание! Роль будет безвозвратно удалена. Продолжить?")'));
    $header_form = 'Редактирование роли '.$name;
    $action = '/admin/user/editrole/name/'.$model->name;
    if ($model->isNewRecord){
        $submit_text = 'Создать';
        $name = 'new';
        $header_form = 'Создание новой роли';
        $action = '/admin/user/createrole/';
        unset($delete_button);
    }
?>
<div class="form">
<div class="header-form">
<?  // Заголовок формы
    echo $header_form; ?>
</div>
<?  $form = $this->beginWidget('CActiveForm', array('id'=>'form'.$model->name,
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
    echo CHtml::button('Закрыть роль',array('onclick'=>'$(".total .right").html(" ");','class'=>'btn'));
    echo CHtml::submitButton($submit_text,array('id'=>'but_'.$name,'class'=>'btn btn-green')); ?>
</div>

<div class="name field">
<?  echo $form->error($model, 'name'); 
    echo $form->labelEx($model, 'name');
    echo $form->textField($model, 'name'); ?>
</div>
<div class="description field">
<?  echo $form->error($model, 'description');
    echo $form->labelEx($model, 'description');
    echo $form->textarea($model, 'description'); ?>
</div>
<?  $checked = array();
    foreach ($model->authItemChildren1 as $it){
        array_push($checked, $it->child);
    } ?>

<div class="operat-50 operat-sel">
    <div class="header-h4">Выбранные операции</div>
    <ul id="checkbox-selected" class="dropper">
    <?  if(isset($operation)){
            foreach ($operation as $key=>$item){
                if(in_array($item->name, $checked)){
                    echo '<li class="checkbox">';
                    echo '<span>'.$item->name.'</span>';
                    echo CHtml::checkBox('Operations[name][]', true , array('value'=>$item->name,'id'=>'op_'.$item->name));
                    echo '</li>';
                    unset($operation[$key]);
                }
            }
        } ?>
    </ul>
</div>
<? $this->endWidget(); ?>
<div class="operat-50 operat-all">
    <div class="header-h4">Все операции</div>
    <ul id="checkbox-all" class="dropper">
    <?  if(isset($operation)){
            foreach ($operation as $item){
                echo '<li class="checkbox">';
                echo '<span>'.$item->name.'</span>';
                echo CHtml::checkBox('Operations[name][]', true , array('value'=>$item->name,'id'=>'op_'.$item->name));
                echo '</li>';
            }
        } ?>
    </ul>
</div>

<div class="quick_buttons">
    <span id="select" class="btn quick_but">Выбрать все</span>
    <span id="clear" class="btn quick_but">Очистить</span>
    <span id="edit" class="btn quick_but">Добавить edit</span>
    <span id="read" class="btn quick_but">Добавить read</span>
    <span id="create" class="btn quick_but">Добавить create</span>
    <span id="delete" class="btn quick_but">Добавить delete</span>
    <span id="user" class="btn quick_but">User</span>
    <span id="manager" class="btn quick_but">Manager</span>
</div>
</div>
<script>
    $( "#checkbox-selected, #checkbox-all" ).sortable({
        connectWith:".dropper"
    }).disableSelection();
    $('.quick_but').click(function(){
        switch($(this).attr('id')){
            case 'select':
                $('#checkbox-selected').append($('#checkbox-all li'));
                break;
            case 'clear':
                $('#checkbox-all').append($('#checkbox-selected li'));
                break;
            case 'edit':
                addcheckbox ('edit');
                break;
            case 'read':
                addcheckbox ('read');
                break;
            case 'create':
                addcheckbox ('create');
                break;
            case 'delete':
                addcheckbox ('delete');
                break;
            case 'user':
                $('#checkbox-all').append($('#checkbox-selected li'));
                addcheckbox ('read');
                break;
            case 'manager':
                $('#checkbox-all').append($('#checkbox-selected li'));
                addcheckbox ('read');
                addcheckbox ('edit');
                addcheckbox ('create');
                break;
        }
    })
    function addcheckbox (name){
        $('#checkbox-all li').each(function(){
            regexp = new RegExp(name);
            if (regexp.test($(this).text())){
                $('#checkbox-selected').append($(this));
            }
        })
        return true;
    }
</script>