<?php
    /*
     * Вид editrole - редактирование или создание роли
     * 
     */
// Стартовые параметры
    $submit_text = 'Сохранить';
    $name = $model->name;
    $delete_button = CHtml::link('Удалить группу', '/admin/user/deletegroup/id/'.$model->id, array('id'=>'del_'.$model->id,'class'=>'btn del', 'onclick'=>'return confirm("Внимание! Группа будет безвозвратно удалена. Продолжить?")'));
    $header_form = 'Редактирование группы '.$name;
    if ($model->isNewRecord){
        $submit_text = 'Создать';
        $name = 'new';
        $header_form = 'Создание новой группы';
        unset($delete_button);
    }
?>
<div class="form">
<div class="header-form">
<?php  // Заголовок формы
    echo $header_form; ?>
</div>
<?php  $form = $this->beginWidget('CActiveForm', array('id'=>'editgroup'.$model->id,
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
    
<?php  
    echo $delete_button; 
    echo CHtml::button('Закрыть группу',array('onclick'=>'$(".total .right").html(" ");','class'=>'btn'));
    echo CHtml::submitButton($submit_text,array('id'=>'but_'.$model->id,'class'=>'btn btn-green'));
?>
</div>

<div class="name field">
<?php  echo $form->error($model, 'name'); 
    echo $form->labelEx($model, 'name');
    echo $form->textField($model, 'name'); ?>
</div>
<div class="level field">
<?php  echo $form->error($model, 'level');
    echo $form->labelEx($model, 'level');
    echo $form->textField($model,'level', array('id'=>'level')); ?>
</div>
<?php $checked = array();
if (isset($checkbox)){
    foreach ($checkbox as $it){
        array_push($checked, $it->itemname);
    }
}?>

<div class="operat-50 operat-sel">
    <div class="header-h4">Выбранные роли</div>
    <ul id="checkbox-selected" class="dropper">
    <?php  if(isset($role)){
            foreach ($role as $key=>$item){
                if(in_array($item->name, $checked)){
                    echo '<li class="checkbox">';
                    echo '<span>'.$item->name.'</span>';
                    echo CHtml::checkBox('Roles[name][]', true , array('value'=>$item->name,'id'=>'ro_'.$item->name));
                    echo '</li>';
                    unset($role[$key]);
                }
            }
        } ?>
    </ul>
</div>
<?php $this->endWidget(); ?>
<div class="operat-50 operat-all">
    <div class="header-h4">Все роли</div>
    <ul id="checkbox-all" class="dropper">
    <?php  if(isset($role)){
            foreach ($role as $item){
                echo '<li class="checkbox">';
                echo '<span>'.$item->name.'</span>';
                echo CHtml::checkBox('Roles[name][]', true , array('value'=>$item->name,'id'=>'ro_'.$item->name));
                echo '</li>';
            }
        } ?>
    </ul>
</div>

<div class="quick_buttons">
    <span id="select" class="btn quick_but">Выбрать все</span>
    <span id="clear" class="btn quick_but">Очистить</span>
    <span id="edit" class="btn quick_but">Добавить Editor</span>
    <span id="read" class="btn quick_but">Добавить Reader</span>
    <span id="admin" class="btn quick_but">Добавить Admin</span>
</div>
</div>
<style>
    .right .operat-all 
    {
        top: 96px;
    }
</style>
<script>
    $( "#checkbox-selected, #checkbox-all" ).sortable({
        connectWith:".dropper",
        placeholder: "placeholder"
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
                addcheckbox ('Editor');
                break;
            case 'read':
                addcheckbox ('Reader');
                break;
            case 'admin':
                addcheckbox ('Admin');
                break;
        }
    })
    function addcheckbox (name){
        $('#checkbox-all li').each(function(){
            regexp = new RegExp(name, 'g');
            if (regexp.test($(this).text())){
                $('#checkbox-selected').append($(this));
            }
        })
        return true;
    }
    $( "#level" ).spinner({
      spin: function( event, ui ) {
        if ( ui.value > 10 ) {
          $( this ).spinner( "value", <?php echo Yii::app()->user->_level+1; ?> );
          return false;
        } else if ( ui.value < <?php echo Yii::app()->user->_level+1; ?> ) {
          $( this ).spinner( "value", 10 );
          return false;
        }
      }
    });
</script>