<!DOCTYPE html>
<html>
    <head>
        <meta name="format-detection" content="telephone=no">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta content="all" name="robots">
        <meta content="dynamic" name="document-state">
        <meta content="2 days" name="revisit-after">
        <meta content="Global" name="distribution">
        <meta http-equiv="pragma" content="no-cache">
        <meta name="description" content="<?php echo Yii::app()->params['meta_description']; ?>">
        <title><?php echo Yii::app()->params['meta_title']; ?></title>
        
        <!--link rel="stylesheet" type="text/css" href="/css/front/frontend.css?<?php echo time(); ?>" /-->
        <!--link rel="stylesheet" type="text/css" href="/css/front/jquery.mCustomScrollbar.css" /-->
        <link rel="shortcut icon" type="image/jpg" href="<?php echo Yii::app()->request->baseUrl.'/images/favicon.jpg';?>"/>
        <!--script src="http://exchange.lbr.ru:3001/socket.io/socket.io.js"></script-->
        <script src="http://localhost:3000/socket.io/socket.io.js"></script>
        <?php
            Yii::app()->clientScript->registerCssFile('/distribution/css/styles.min.css?'.time());
            Yii::app()->clientScript->registerCoreScript('jquery');
            Yii::app()->clientScript->registerScriptFile('/distribution/js/scripts.min.js?'.time());
            
            //Yii::app()->clientScript->registerScriptFile('/js/easyTooltip.js');
            //Yii::app()->clientScript->registerScriptFile('/js/jquery.mCustomScrollbar.concat.min.js');
            //Yii::app()->clientScript->registerScriptFile('/js/front/Timer.min.js?'.time());
            ////Yii::app()->clientScript->registerScriptFile('/js/front/min/Timer_copy.js?'.time());
            //Yii::app()->clientScript->registerScriptFile('/js/front/rateList.min.js?'.time());
            //Yii::app()->clientScript->registerScriptFile('/js/front/min/rateList_copy.js?'.time());
            //Yii::app()->clientScript->registerScriptFile('/js/front/menu.js');
            //Yii::app()->clientScript->registerScriptFile('/js/front/frontend.js');
            //Yii::app()->clientScript->registerScriptFile('/js/front/OnlineEvent.js');
            
            //google analitics
            //Yii::app()->clientScript->registerScriptFile('/js/lbr.google.analytics.js');
        ?>
    </head>
    <body>
        <?php if(!Yii::app()->user->isGuest &&  Yii::app()->user->isTransport): ?>
        <div id="online-event"></div>
        <?php endif; ?>
        <div class="header">
            <div class="logo">
                <a href="http://www.lbr.ru/">
                    <img src="/images/logo.png" title="ЛБР-АгроМаркет" alt="Логотип ЛБР-АгроМаркет"/>
                </a>
            </div>
            <div class="center">
                <noscript><p class="no-script">К сожалению Вы не сможете воспользоваться биржой, т.к. Ваш браузер не поддерживает JavaScript! </p></noscript> 
                <span>Онлайн биржа перевозок</span>
            </div>
            <noscript><div class="hide"></noscript>
            <ul class="menu">
                <li><a class="color" href="/feedback/" title="Обратная связь">Обратная связь</a></li>
                <li><a class="color" href="/help/" title="Инструкции">Инструкции</a></li>
            </ul>
            <noscript></div></noscript>
        </div>
        <div class="wrapper">
            <div class="w-left">
                <?php $this->widget('ext.userMenu.UserMenu'); ?>
            </div>
            <div class="w-right">
                <?php if (!Yii::app()->user->isGuest): ?>
                <noscript><div class="hide"></noscript>
                <?php endif; ?>
                <?php //$this->renderPartial( 'user.views.site.dialog' ); ?>
                <?php echo $content; ?>
                <?php if (!Yii::app()->user->isGuest): ?>
                <noscript></div></noscript>
                <?php endif; ?>
            </div>
        </div>
        <div class="footer">
            <p><?php echo date("Y"); ?> &copy; ООО "ЛБР-АгроМаркет"</p>
        </div>
    </body>
    <script>
        <?php if(!Yii::app()->user->isGuest && Yii::app()->user->isTransport): ?>
        //var socket = io.connect('http://exchange.lbr.ru:3001/');
        var socket = io.connect('http://localhost:3000/');
        if(typeof(socket) !== 'undefined') {
            socket.emit('init', <?php echo Yii::app()->user->_id ?>);
            socket.on('timer', function(data) {
                var container = $('#counter-' + data.transportId);
                if(container.length) {
                    if(data.access) {
                       container.html(data.time);
                    } else {
                       if($(".ui-dialog-content").length) $(".ui-dialog-content").dialog( "close" );
                       if($(".r-submit").length) $('.r-submit').addClass('disabled');
                       if($(".rate-wrapper").length) $('.rate-wrapper').slideUp("slow");
                       container.removeClass('open');
                       container.html('<span class="t-closed"><img class="small-loading" src="/images/loading-small.gif"/>Обработка результатов</span>'); 
                       setTimeout(function(){ 
                           container.html('<span class="t-closed closed">Перевозка закрыта</span>');
                           if($('.items .transport').length) container.parents().eq(2).slideUp("slow");
                       }, 180000);
                    }
                }
            });
            
            socket.on('error', function(data) {
                $('[id^="counter-"]').text('Разрыв связи с сервером');
            });
            
            socket.on('connect', function (data) {
                $('[id^="counter-"]').text('');
            });
            
            socket.on('onlineEvent', function (data) {
                $.onlineEvent({ msg : data.msg, className : 'classic', sticked:true, position:{right:0,bottom:0}, time:10000});
            });
            
            socket.on('updateEvents', function (data) {
                if (parseInt(data.count) != 0) {
                    $('#event-counter').html(data.count);    
                } else $('#event-counter').html('');
            });
        }
        <?php endif; ?>
    </script>
</html>
