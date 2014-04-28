<?php
    $header_form = '"'.$model->company . '"';
    $submit_text = 'Сохранить';
    $close_text = 'Закрыть';
    $delete_button = CHtml::link('Удалить', '/admin/user/deleteuser/id/'.$model->id, array('id'=>'del-user','class'=>'btn-admin btn-del', 'onclick'=>'return confirm("Внимание! Пользователь будет безвозвратно удален. Продолжить?")'));
    $action = '/admin/user/edituser/id/'.$model->id;
    $name = $model->id;
    if (!$model->id) {
        $submit_text = 'Создать';
        $header_form = '';
        $action = '/admin/user/createuser/';
        unset($delete_button);
    }
?>
<div class="total">
    <div class="left">
        <?php if (!$model->id): ?>
        <h1>Создание компании</h1>
        <?php else: ?>
        <h1>Редактирование компании</h1>
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
                    echo '<div class="uMessage success">'.$mess.'</div>';
                } else if ($mess = Yii::app()->user->getFlash('error')) {
                    echo '<div class="uMessage error">'.$mess.'</div>';
                }
            ?>
            <?php $form = $this->beginWidget('CActiveForm', array('id'=>'userform',
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
            <?php
                echo CHtml::button($close_text,array('id'=>'close-user', 'class'=>'btn-admin'));
                echo $delete_button;
                echo CHtml::submitButton($submit_text,array('id'=>'but_'.$name,'class'=>'btn-admin')); 
            ?>
            </div>
            <div class="company field">
            <?php  echo $form->error($model, 'company'); 
                echo $form->labelEx($model, 'company');
                echo $form->textField($model, 'company'); ?>
            </div>
            <div class="inn field">
            <?php  echo $form->error($model, 'inn'); 
                echo $form->labelEx($model, 'inn');
                echo $form->textField($model, 'inn'); ?>
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
            <div class="password field">
            <?php  
                if ($model->id) {
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
            <div class="status field">
            <?php  echo $form->error($model, 'status');
                echo $form->labelEx($model, 'status');
                echo $form->dropDownList($model, 'status', User::$userStatus); ?>
            </div>
            <div class="reason field">
            <?php  echo $form->error($model, 'reason');
                echo $form->labelEx($model, 'reason');
                echo $form->textArea($model, 'reason'); ?>
            </div>
            <div class="block_date field">
            <?php  echo $form->error($model, 'block_date'); 
                echo $form->labelEx($model, 'block_date');
                if(!empty($model->block_date))$model->block_date = date("d-m-Y", strtotime($model->block_date));
                //else $model->block_date = date('d-m-Y', strtotime('+5 days'));
                echo $form->textField($model, 'block_date'); 
            ?>
            </div>
            <div class="country field">
            <?php  echo $form->error($model, 'country'); 
                echo $form->labelEx($model, 'country');
                echo $form->textField($model, 'country'); ?>
            </div>
            <div class="region field">
            <?php  echo $form->error($model, 'region'); 
                echo $form->labelEx($model, 'region');
                echo $form->textField($model, 'region'); ?>
            </div>
            <div class="city field">
            <?php  echo $form->error($model, 'city'); 
                echo $form->labelEx($model, 'city');
                echo $form->textField($model, 'city'); ?>
            </div>
            <div class="district field">
            <?php  echo $form->error($model, 'district'); 
                echo $form->labelEx($model, 'district');
                echo $form->textField($model, 'district'); ?>
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
            <div class="email field">
            <?php  echo $form->error($model, 'email');
                echo $form->labelEx($model, 'email');
                echo $form->emailField($model, 'email'); ?>
            </div>
            <?php if ($model->id): ?>
            <div style="display:none;">
            <?php  echo $form->hiddenField($model, 'password'); ?>
            </div>
            <?php endif; ?>
            <?php if($model->id): ?>
            <div>
                <div class="header-h4">Созданные контактные лица</div>
                <?php if(count($contacts)): ?>
                <ul id="contacts-all">
                    <li>
                       <span>Контактные лица</span>
                       <span>Email</span>
                    </li>
                <?php foreach ($contacts as $contact){
                        echo '<li class="point">';
                        echo '<span>';
                            echo $contact['name'] . ' ' . $contact['secondname'] . ' ' . $contact['surname'];
                        echo '</span>';
                        echo '<span>';
                            echo $contact['email'];
                        echo '</span>';
                        echo '</li>';
                    }?>
                </ul>
                <?php else: ?>
                <div>Нет контактных лиц</div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php $this->endWidget();?> 
        </div>
    </div>
</div>
<script>
$(document).ready(function(){ 
    var activeStatus = parseInt(sessionStorage.getItem('userStatus'));
    if(!isNaN(activeStatus)) {
        var href = $('#del-user').attr('href');
        $('#del-user').attr('href', href + '/status/' + activeStatus);
    }
    $('#close-user').click(function(){
        if(isNaN(activeStatus)) document.location.href = "<?php echo Yii::app()->getBaseUrl(true) ?>/admin/user/";
        else document.location.href = "<?php echo Yii::app()->getBaseUrl(true) ?>/admin/user/index/status/" + activeStatus;
    });
    <?php if($model->status == User::USER_NOT_CONFIRMED || $model->status == User::USER_ACTIVE): ?>
    $('#UserForm_reason').parent().addClass('hide');
    <?php endif; ?>
    
    <?php if($model->status != User::USER_TEMPORARY_BLOCKED): ?>
    $('#UserForm_block_date').parent().addClass('hide');
    <?php endif; ?>
    
    
    $('#UserForm_status').change(function(){
         if(this.value == <?php echo User::USER_NOT_CONFIRMED ?> || this.value == <?php echo User::USER_ACTIVE ?>){
             $('#UserForm_reason').parent().addClass('hide');
         } else {
             $('#UserForm_reason').parent().removeClass('hide');
         }
         if(this.value == <?php echo User::USER_TEMPORARY_BLOCKED ?>) {
             $('#UserForm_block_date').parent().removeClass('hide');
             $('#UserForm_block_date').val('<?php echo date('d-m-Y', strtotime('+5 days')) ?>');
         }
         else $('#UserForm_block_date').parent().addClass('hide');
    });
    /************************************/
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

    $( "#UserForm_block_date" ).datepicker({
        dateFormat: 'dd-mm-yy',
    });
});
</script>
