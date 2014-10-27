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
        
        <link rel="stylesheet" type="text/css" href="/css/front/frontend.css?<?php echo time(); ?>" />
        <link rel="stylesheet" type="text/css" href="/css/front/jquery.mCustomScrollbar.css" />
        <script src="http://exchange.lbr.ru:3000/socket.io/socket.io.js"></script>
        <!--script src="http://localhost:3000/socket.io/socket.io.js"></script-->
        <?php
            Yii::app()->clientScript->registerCoreScript('jquery');
            Yii::app()->clientScript->registerScriptFile('/js/easyTooltip.js');
            Yii::app()->clientScript->registerScriptFile('/js/jquery.mCustomScrollbar.concat.min.js');
            Yii::app()->clientScript->registerScriptFile('/js/front/Timer.min.js?'.time());
            //Yii::app()->clientScript->registerScriptFile('/js/front/min/Timer_copy.js?'.time());
            Yii::app()->clientScript->registerScriptFile('/js/front/rateList.min.js?'.time());
            //Yii::app()->clientScript->registerScriptFile('/js/front/min/rateList_copy.js?'.time());
            Yii::app()->clientScript->registerScriptFile('/js/front/menu.js');
            Yii::app()->clientScript->registerScriptFile('/js/front/frontend.js');
            Yii::app()->clientScript->registerScriptFile('/js/front/OnlineEvent.js');
            Yii::log('Server time - '.date("Y-m-d H:i"), 'info');
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
            <noscript><div class="hide"></noscript>
            <ul class="menu">
                <li><a href="/feedback/" title="Обратная связь">Обратная связь</a></li>
                <li><a href="/help/" title="Инструкции">Инструкции</a></li>
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
            <p>2014 &copy; ООО "ЛБР-Агромаркет"</p>
        </div>
    </body>
</html>
