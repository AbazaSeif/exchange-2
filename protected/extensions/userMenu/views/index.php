<?php
if(!Yii::app()->user->isGuest){
    if(Yii::app()->user->isTransport) {
        $user = User::model()->findByPk(Yii::app()->user->_id);
    } else if(Yii::app()->user->isContactUser) {
        $user = UserContact::model()->findByPk(Yii::app()->user->_id);
    } else {
        $user = AuthUser::model()->findByPk(Yii::app()->user->_id);
    }
?>
    <div class='user-info'>
        <?php echo '<span class="user-name"> Добро пожаловать, '.$user->name.'!</span>'; ?>
    </div>
    <?php
        if(Yii::app()->user->isTransport)
            { ?>
            <ul class="user-menu">
                <li><a href="/">Главная</a></li>
                <li><a href="/user/event/" id="menu-events">События <span id="event-counter"></span></a></li>
                <li><a href="/user/transport/all/">Мои перевозки</a>
                    <ul id="submenu" class="user-submenu">
                        <li><a href="/user/transport/active/">Активные</a></li>
                        <li><a href="/user/transport/archive/s/1/">Выигранные</a></li>
                        <li><a href="/user/transport/archive/">Проигранные</a></li>
                    </ul>
                </li>
                <li><a href="/user/option/">Настройки</a></li>
                <!--li><a href="/user/contact/">Контактные лица</a></li-->
                <li><a href="/user/logout/" class="exit">Выход</a></li>
            </ul>
    <?php }  else {?>
                <ul class="user-menu">
                    <li><a href="/">Главная</a>
                        <?php if(Yii::app()->user->checkAccess('readTransport')){ ?>
                            <li><a href="/admin/" class="admin">Административная панель</a></li>
                        <?php  } ?>
                    <li><a href="/user/logout/" class="exit">Выход</a></li>
                </ul>
            <?php }
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
    <div class="position">
    <?php echo CHtml::link('Восстановление доступа', array('/site/restore'), array('class' => 'color')); ?>
    </div>
    <div>
    <?php echo CHtml::link('Заявка на регистрацию', array('/site/registration'), array('class' => 'color')); ?>
    </div>
<?php $this->endWidget();    
}
?>

<script>
$(document).ready(function(){
    <?php if(!Yii::app()->user->isGuest && Yii::app()->user->isTransport): ?>
    var userId = <?php echo $user->id ?>;
    //var socket = io.connect('http://exchange.lbr.ru:3000/');
    var socket = io.connect('http://localhost:3000/');
    
    socket.emit('init', userId, <?php echo Yii::app()->params['minNotyfy'] ?>);
    
    var countSubmenuElem = null;
    if ($("#submenu")) {
        countSubmenuElem = parseInt($('#submenu').children().length);
    }
    menu.countSubmenuElem = countSubmenuElem;
    menu.init();
    
    socket.emit('events', userId);   
    socket.on('updateEvents', function (data) {
        if(parseInt(data.count) != 0) {
            $('#event-counter').html(data.count);    
        } else $('#event-counter').html('');
    });
    
    socket.on('onlineEvent', function (data) {
        $.onlineEvent({ msg : data.msg, className : 'classic', sticked:true, position:{right:0,bottom:0}, time:10000});
    });
    <?php endif;?>
});
</script>
