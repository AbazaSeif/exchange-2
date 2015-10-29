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
            Yii::app()->clientScript->registerCssFile('/distribution/css/styles.min.css?1');
            Yii::app()->clientScript->registerCoreScript('jquery');
            Yii::app()->clientScript->registerScriptFile('/distribution/js/scripts.min.js?1');
            
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
        
        socket.on('timer', function(data) {
            var container = $('#counter-' + data.transportId);
            if(container.length) {
            //if(data.transportId == <?php echo $transportInfo['id'] ?>) {
                if(data.access) {
                   container.html(data.time);
                } else {
                   $(".ui-dialog-content").dialog( "close" );
                   $('.r-submit').addClass('disabled');
                   $('.rate-wrapper').slideUp("slow");
                   container.removeClass('open');
                   container.html('<span class="t-closed"><img class="small-loading" src="/images/loading-small.gif"/>Обработка результатов</span>'); 
                   setTimeout(function(){ container.html('<span class="t-closed closed">Перевозка закрыта</span>') }, 180000);
                }
            }
        });
        <?php endif; ?>
    </script>
</html>
