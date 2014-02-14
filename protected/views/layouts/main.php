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

        <!--[if lt IE 8]>
            <link rel="stylesheet" type="text/css" href="/css/ie.css" media="screen, projection" />
        <![endif]-->

        <link rel="stylesheet" type="text/css" href="/css/front/frontend.css" />

        <?php
			Yii::app()->clientScript->registerCoreScript('jquery');
			Yii::app()->clientScript->registerScriptFile('/js/front/Timer.js');
			Yii::app()->clientScript->registerScriptFile('/js/front/rateList.js');
        ?>
        <script src="http://localhost:3000/socket.io/socket.io.js"></script>
        <script>
             var name = 'not logged in';
             <?php if(!Yii::app()->user->isGuest): ?>
             <?php 
                 $user = User::model()->findByPk(Yii::app()->user->_id);
             ?>
             name = "<?php echo $user->name . ' ' . $user->surname ?>";
             id = <?php echo Yii::app()->user->_id ?>;
             <?php endif; ?>
             var socket = io.connect('http://localhost:3000');
             
             $(document).ready(function(){
                $("button").click(function(){
                   // just some simple logging
                   $("p#log").html('sent message: ' + $("input#msg").val());
                   // отправить серверу
                   socket.emit('chat', $("input#msg").val() );
                   // Печатает сообщение текущего отправителя (т.к. сообщение не будет отправлено отправителю)
                   $("p#data_recieved").append("<br />\r\n" + name + ': ' + $("input#msg").val());
                   // очищаем input
                   $("input#msg").val('');
                });
                // отправить серверу
                socket.emit('register', name, id );
             });
             
             // listen for chat event and recieve data
             socket.on('chat', function (data) {
                $("p#data_recieved").append("<br />\r\n" + data.msgr + ': ' + data.msg);
                $("p#log").html('new message: ' + data.msg);
             });
             
             socket.on('init', function (data) {
                $("p#init").append("<br />\r\n" + data.name + ' - ' + data.price);              
             });
        </script>
    </head>
    <body>
        <div class="wrapper">
            <div class="w-left">
                <div class="logo">
                    <a href="/">
                        <img src="/images/logo.png" title="ЛБР-Агромаркет" alt="Логотип ЛБР-Агромаркет"/>
                    </a>
                </div>
                <?php $this->widget('ext.userMenu.UserMenu'); ?>
                <div class="footer">
                    <div id="socket-info" style="background-color: orange">
                    ****** 111 *******
                    </div>
                    <input type="text" id="msg"></input><button>Click me</button>
                      <p id="log"></p>
                      <p id="data_recieved"></p>
                      <p id="init"></p>
                    <p>2014 &copy; ООО "ЛБР-Агромаркет"</p>
                </div>
            </div>
            <div class="w-right">
                <?php echo $content; ?>
            </div>
        </div>
    </body>
</html>
