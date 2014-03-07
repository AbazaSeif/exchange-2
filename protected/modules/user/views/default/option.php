<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array('id'=>'options',
    'action'=>'/user/option/',
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

    //var_dump($pass);
?>
    <div>
        <div class="title"><img src="/images/mail.jpg"><span>Настройки для оповещения по почте</span></div>                
                <div class="row">
		<?php echo CHtml::checkBox('Option_mail_transport_create_1', (bool)$model['mail_transport_create_1']); ?>
		<?php echo CHtml::label('При создании международной перевозки', 'mail_transport_create_1'); ?>
		</div>
		<div class="row">
		<?php echo CHtml::checkBox('Option_mail_transport_create_2', (bool)$model['mail_transport_create_2']); ?>
		<?php echo CHtml::label('При создании региональной перевозки', 'mail_transport_create_2'); ?>
		</div>
		<div class="row">
		<?php echo CHtml::checkBox('mail_kill_rate', (bool)$model['mail_kill_rate']); ?>
		<?php echo CHtml::label('Если была перебита ставка', 'mail_kill_rate'); ?>                
		</div>
		<div class="row">
		<?php echo CHtml::checkBox('mail_deadline', (bool)$model['mail_deadline']); ?>
		<?php echo CHtml::label('При закрытии перевозки', 'mail_deadline'); ?>                
		</div>
		<div class="row">
		<?php echo CHtml::checkBox('mail_before_deadline', (bool)$model['mail_before_deadline']); ?>
		<?php echo CHtml::label('За ' . Yii::app()->params['minNotyfy'] . ' минут до закрытия перевозки', 'mail_before_deadline'); ?>                
		</div>
        </div>
        <div>
            <div class="title"><img src="/images/option.jpg"><span>Параметры отображения</span></div>
            <div class="row">
                <?php echo CHtml::checkBox('with_nds', (bool)$model['with_nds']); ?>
                <?php echo CHtml::label('Показывать цену с НДС', 'with_nds'); ?>
            </div>
        </div>
	<div class="row submit">
	<?php 
	    echo CHtml::submitButton('Сохранить', array('class' => 'r-submit')); 
	?>
	</div>
    <?php $this->endWidget();?> 
</div>
<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array('id'=>'password',
    'action'=>'/user/option/',
    'enableClientValidation'=>true,
    'clientOptions'=>array(
        'validateOnSubmit'=>true,
        'afterValidate'=>'js:function( form, data, hasError ) {     
                if( hasError ){
                    return false;
                }
                else{
                    return true;
                }
            }'
    ),));
?>
        <div>
            <div class="title"><img src="/images/option.jpg"><span>Сменить пароль</span></div>
            <div class="row password">
            <?php  
                echo $form->error($pass, 'password'); 
                echo $form->labelEx($pass, 'password');
                /*echo $form->passwordField($pass, 'password', array(
                    //'value' => '   '
                ));*/
                echo $form->passwordField($pass, 'password');
            ?>
            <?php
                //echo CHtml::label('Пароль', 'OptionForm_password');
                //echo CHtml::passwordField('OptionForm_password', '   ', array('id'=>'OptionForm_password')); ?>
            </div>
            <!--div class="row password">
            <?php
                //echo $form->error($pass, 'new_password'); 
                //echo $form->labelEx($pass, 'new_password');
                //echo $form->passwordField($pass, 'new_password');
            ?>    
            </div>                
            <div class="row password">
            <?php  
                //echo $form->error($pass, 'new_confirm'); 
                //echo $form->labelEx($pass, 'new_confirm');
                //echo $form->passwordField($pass, 'new_confirm');
            ?>    
            </div-->
            
        </div>
	<div class="row submit">
	<?php 
	    echo CHtml::submitButton('Подтвердить', array('class' => 'r-submit')); 
	?>
	</div>
    <?php $this->endWidget();?> 
</div>



