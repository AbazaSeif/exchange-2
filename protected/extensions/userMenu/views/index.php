<?php 
if(!Yii::app()->user->isGuest){
    $user = User::model()->findByPk(Yii::app()->user->_id);
?>
    <div class='user-info'>
        <?php echo '<span class="user-name"> Добро пожаловать, '.$user->surname.'!</span>'; ?>
    </div>
    <?php if(Yii::app()->user->checkAccess('transport'))
            { ?>
            <ul class="user-menu">
			    <li><a href="/" id="menu-events">Главная</a></li>
                <li><a href="/user/event/" id="menu-events">События <span id="event-counter"></span></a></li>
                <li><a href="/user/transport/all/">Все перевозки</a>
                    <ul class="user-submenu">
                        <li><a href="/user/transport/active/">Активные</a></li>
                        <li><a href="/user/transport/archive/s/1/">Выигранные</a></li>
                        <li><a href="/user/transport/archive/">Проигранные</a></li>
                    </ul>
                </li>
                <li><a href="/user/option/">Настройки</a></li>
                <li><a href="/user/logout/">Выход</a></li>
            </ul>
<?php       }
        }else{
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
    }?>
	
<script>
$(document).ready(function(){
updateCounter();
setInterval(function(){updateCounter()}, 5000);
});

function updateCounter(){
    $.ajax({
		url: '/user/updateEventCounter',
		success: function(data){
			$('#event-counter').html(data);
	}});
}
</script>