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
        
        <link rel="stylesheet" type="text/css" href="/css/front/frontend.css" />
        <link rel="stylesheet" type="text/css" href="/css/front/tip-darkgray/tip-darkgray.css" />
        <link rel="stylesheet" type="text/css" href="/css/front/jquery.mCustomScrollbar.css" />
        <!--script src="http://exchange.lbr.ru:3000/socket.io/socket.io.js"></script-->
        <script src="http://localhost:3000/socket.io/socket.io.js"></script>
        <?php
            Yii::app()->clientScript->registerCoreScript('jquery');
            Yii::app()->clientScript->registerScriptFile('/js/jquery.dotdotdot.js');
            Yii::app()->clientScript->registerScriptFile('/js/jquery.poshytip.js');
            Yii::app()->clientScript->registerScriptFile('/js/jquery.mCustomScrollbar.concat.min.js');
            Yii::app()->clientScript->registerScriptFile('/js/front/Timer.js');
            Yii::app()->clientScript->registerScriptFile('/js/front/rateList.js');
            Yii::app()->clientScript->registerScriptFile('/js/front/menu.js');
            Yii::app()->clientScript->registerScriptFile('/js/front/frontend.js');
            Yii::app()->clientScript->registerScriptFile('/js/front/OnlineEvent.js');
        ?>
    </head>
    <body>
        <?php if(!Yii::app()->user->isGuest &&  Yii::app()->user->isTransport): ?>
        <div id="online-event"></div>
         <?php endif; ?>
        <div class="header">
            <div class="logo">
                <a href="/">
                    <img src="/images/logo.png" title="ЛБР-Агромаркет" alt="Логотип ЛБР-Агромаркет"/>
                </a>
            </div>
            <div class="center">
                <noscript><p class="no-script">К сожалению Вы не сможете воспользоваться биржой, т.к. Ваш браузер не поддерживает JavaScript! </p></noscript> 
                <span>Онлайн биржа перевозок</span>
            </div>
            <noscript><div style="display: none"></noscript>
            <ul class="menu">
                <li><a href="/feedback/" title="Обратная связь">Обратная связь</a></li>
                <li><a href="/help/" title="Помощь">Помощь</a></li>
            </ul>
            <noscript></div></noscript>
        </div>
        <div class="wrapper">
            <div class="w-left">
                <?php $this->widget('ext.userMenu.UserMenu'); ?>
            </div>
            <div class="w-right">
                <noscript><div style="display: none"></noscript>
                <?php $this->renderPartial( 'user.views.site.dialog' ); ?>
                <?php echo $content; ?>
                <noscript></div></noscript>
            </div>
        </div>
        <div class="footer">
            <p>2014 &copy; ООО "ЛБР-Агромаркет"</p>
        </div>
    </body>
</html>
