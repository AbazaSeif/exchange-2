<?php
if(!Yii::app()->user->isGuest){
    $user = User::model()->findByPk(Yii::app()->user->_id);
?>
    <div class='user-info'>
        <?php echo '<span class="user-name"> Добро пожаловать, '.$user->name.'!</span>'; ?>
    </div>
    <?php if(Yii::app()->user->checkAccess('transport') && !Yii::app()->user->isRoot)
            { ?>
            <ul class="user-menu">
                <li><a href="/">Главная</a></li>
                <li><a href="/user/event/" id="menu-events">События <span id="event-counter"></span></a></li>
                <li><a href="/user/transport/all/">Все перевозки</a>
                    <ul id="submenu" class="user-submenu">
                        <li><a href="/user/transport/active/">Активные</a></li>
                        <li><a href="/user/transport/archive/s/1/">Выигранные</a></li>
                        <li><a href="/user/transport/archive/">Проигранные</a></li>
                    </ul>
                </li>
                <li><a href="/user/option/">Настройки</a></li>
                <li><a href="/user/logout/">Выход</a></li>
            </ul>
    <?php }  else {?>
                <ul class="user-menu">
                    <li><a href="/">Главная</a>
                        <?php if(Yii::app()->user->checkAccess('admin')){ ?>
                            <li><a href="/admin/">Административная панель</a></li>
                        <?php  } ?>
                    <li><a href="/user/logout/">Выход</a></li>
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
    <div>
    <?php echo CHtml::link('Подать заявку на регистрацию', array('/user/registration'), array('class' => 'registration')); ?>
    </div>
<?php $this->endWidget();    
}
?>
<script>
$(document).ready(function(){
    <?php if(!Yii::app()->user->isGuest): ?>
       updateCounter();
       setInterval(function(){updateCounter()}, 5000);
    
    //null - если нет
    var activeElement = sessionStorage.getItem('menu');
    var activeSubElement = sessionStorage.getItem('submenu');
                
    if(activeElement != null){
        <?php if(Yii::app()->user->isRoot): ?>
        if(activeElement == 1) sessionStorage.setItem('menu', null);
        else $('.user-menu li').eq(activeElement).find('a:first').addClass('menu-active');
        <?php else: ?>
        if(activeElement == 3) activeElement = 6;
        $('.user-menu li').eq(activeElement).find('a:first').addClass('menu-active');
         <?php endif;?>
    }
    
    if(activeSubElement != null){
        activeSubElement = parseInt(activeSubElement) - 2;
        $('#submenu li').eq(activeSubElement).find('a:first').addClass('menu-active');
    } 
    
    $( "#submenu>li>a" ).click(function() {
        if(!$(this).hasClass('menu-active')) {
            $("a.menu-active").removeClass('menu-active');
            $(this).addClass('menu-active');
            sessionStorage.setItem('menu', null);
            sessionStorage.setItem('submenu', $(this).parents("li").index());
        }
    });
    
    $( ".user-menu>li>a" ).click(function() {
        if(!$(this).hasClass('menu-active')) {
            $( "a.menu-active" ).removeClass('menu-active');
            $(this).addClass('menu-active');
            sessionStorage.setItem('menu', $(this).parents("li").index());
            sessionStorage.setItem('submenu', null);
        }
    });
    <?php endif;?>
});

function updateCounter(){
    $.ajax({
        url: '/user/updateEventCounter',
	    success: function(data){
	    $('#event-counter').html(data);
    }});
}
</script>
