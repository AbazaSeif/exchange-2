<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <meta name="format-detection" content="telephone=no">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta content="all" name="robots">
        <meta content="dynamic" name="document-state">
        <meta content="2 days" name="revisit-after">
        <meta content="Global" name="distribution">
        <meta http-equiv="pragma" content="no-cache">

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/back/backend.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/front/frontend.css" />
        
        <!--link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/front/page.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/front/chat.css"/>
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/js/front/jScrollPane/jScrollPane.css"/-->
        
        <!--script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/front/script.js" /-->
        <!--script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-1.8.3.js" /-->
        <!--script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/front/jScrollPane/jquery.mousewheel.js" />
        <!--script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/front/jScrollPane/jScrollPane.min.js" /-->
        
        <?php
           // Проверка на наличие Jquery
            Yii::app()->clientScript->registerCoreScript('jquery');
           /* Yii::app()->clientScript->registerScriptFile('/js/front/script.js');
            Yii::app()->clientScript->registerScriptFile('/js/front/jScrollPane/jquery.mousewheel.js');
            Yii::app()->clientScript->registerScriptFile('/js/front/jScrollPane/jScrollPane.min.js');
            */
            
        ?>

        <title>Перевозки ЛБР</title>
</head>

<body>

<div class="container" id="page">
        <header>
            <div class="logo">
                <a href="/">
                    <img src="/images/logo.png" title="ЛБР-Агромаркет" alt="Логотип ЛБР-Агромаркет"/>
                </a>
                <!--div class="region">
                     <div class="select-region">
                        <div class="you-region">
                            <span>Ваш регион:</span>
                        </div>
                        <div class="selected-region">
                            <div id="show_regions_table_button_wrapper">
                                <a id="show_regions_table_button">Не выбран</a>
                            </div>
                            <a class="arrow-wrapper"><span id="show_regions_table_button_arrow" class="arrow"></span></a>
                        </div>
                     </div>
                </div-->
            </div>
            <!--div class="menu main">
                <ul class="menuMainTop">
                    <?php $href='selskohozyaystvennaya-tehnika'; ?>
                    <li <?php if(is_numeric(strpos( mb_strtolower(Yii::app()->request->requestUri), $href)) || 
                            (Yii::app()->request->cookies['rootmenualias']->value =='selskohozyaystvennaya-tehnika' && Yii::app()->params['currentMenuItem']->level==5)) echo 'class="active"' ?> >
                        <a href="/selskohozyaystvennaya-tehnika/type/">
                            <img src="/images/mainMenuIcon/toppict1.png" alt="Сельскохозяйственная техника">
                            <span>Сельхоз техника</span>
                        </a>
                    </li>
                    <?php $href='stroitelnaya-tehnika'; ?>
                    <li>
                        <a href="http://www.lbr.nichost.ru/spareparts/?c=83">
                            <img src="/images/mainMenuIcon/toppict2.png" alt="Запасные части">
                            <span>Запчасти</span>
                        </a>
                    </li>
                    <li>
                        <a href="/service/">
                            <img src="/images/mainMenuIcon/toppict3.png" alt="Сервисное обслуживание">
                            <span>Сервис</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="menu second">
                <ul class="menuStaticTop">
                    <li>
                        <a href="/company/" title="О компании">О компании</a>
                    </li>
                    <li>
                        <a href="http://carrer.git-lbr.ru/" title="Вакансии">Вакансии</a>
                    </li>
                    <li>
                        <a href="/search/" title="Поиск по сайту">Поиск</a>
                    </li>
                    <li>
                        <a href="/users/login/" title="Вход на сайт"><? echo Yii::app()->user->isGuest? "Вход":"Выход"; ?></a>
                    </li>
                </ul>
            </div>
            <div class="map">
                <a href="/company/contacts/">
                    <span>Контакты</span>
                    <img src="/images/map.jpg" title="Контакты ЛБР-Агромаркет" alt="ЛБР-Агромаркет контакты"/>
                </a>
            </div-->
            <div id="top_banner"><p>Биржа перевозок ЛБР-АгроМаркета</p></div>
            
        </header>
	<!--div id="header">
		<div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
	</div--><!-- header -->

	<!--div id="mainmenu">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Home', 'url'=>array('/site/index')),
				array('label'=>'About', 'url'=>array('/site/page', 'view'=>'about')),
				array('label'=>'Contact', 'url'=>array('/site/contact')),
				array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
			),
		)); ?>
	</div--><!-- mainmenu -->
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>
                
        <div class="wrapper">
	   <?php echo $content; ?>
        </div>
                
	<div class="clear"></div>
        
        <footer>
            <div class="f-left">
                <img src="/images/logo-foot.gif" alt="Лого подвал ЛБР-Агромаркет" title="Логотип ЛБР-Агромаркет" />
            </div>
            <!--div class="f-center">
                <?php if(Yii::app()->params['currentMenuItem']->level==1){ ?>
                <a href="http://www.webcom-media.ru/">Продвижение сайта</a> - <a href="http://www.webcom-media.ru"> Webcom Media<sup>®</sup></a>
                <?php }
                else{ ?>
                <a href="http://www.webcom-media.ru"> Webcom Media<sup>®</sup></a>
                <?php } ?>
            </div>
            <div class="f-right">
                <ul class="f-nav">
                    <li><a href="/">Главная</a></li>
                    <li><a href="/company/">О компании</a></li>
                    <li><a href="/company/vacancy/">Вакансии</a></li>
                    <li class="parent"><a href="/company/contacts/">Контакты</a></li>
                    <li><a href="/sitemap/">Карта сайта</a></li>
                </ul>
            </div-->
        </footer>
	<!--div id="footer">
		Copyright &copy; <?php //echo date('Y'); ?> by My Company.<br/>
		All Rights Reserved.<br/>
		<?php //echo Yii::powered(); ?>
	</div--><!-- footer -->

</div><!-- page -->

</body>
</html>
