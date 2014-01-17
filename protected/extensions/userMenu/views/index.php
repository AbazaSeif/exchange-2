<?php 
if(!Yii::app()->user->isGuest){
    $user = User::model()->findByPk(Yii::app()->user->_id);
?>
    <div class='user-info'>
        <?php echo '<span class="user-name"> Добро пожаловать, '.$user->surname.'!</span>'; ?>
    </div>
    <ul class="user-menu">
        <li><a href="/site/event/" id="menu-events">События</a></li>
        <li><a href="/site/index/s/1/">Все перевозки</a>
            <ul class="user-submenu">
                <li><a href="/site/active/">Активные</a></li>
                <li><a href="/site/archive/s/1/">Выигранные</a></li>
                <li><a href="/site/archive/">Проигранные</a></li>
            </ul>
        </li>
        <li><a href="/site/option/">Настройки</a></li>
        <li><a href="/user/logout/">Выход</a></li>
    </ul>
<?php }else{
    $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
    )); ?>
	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username'); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password'); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>
	<div class="row rememberMe">
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->label($model,'rememberMe'); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Войти', array('class'=>'btn')); ?>
	</div>
<?php $this->endWidget();    
}
?>