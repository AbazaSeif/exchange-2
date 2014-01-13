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
	<?php
	   // Проверка на наличие Jquery
		Yii::app()->clientScript->registerCoreScript('jquery');
	?>
    <title>Перевозки ЛБР</title>
</head>
<body>
	<header>
		<div class="logo">
			<a href="/">
				<img src="/images/logo.png" title="ЛБР-Агромаркет" alt="Логотип ЛБР-Агромаркет"/>
			</a>
			<div>
				<a href="/" title="Вход на сайт">
				   <?php echo Yii::app()->user->isGuest ? "Вход":"Выход"; ?>
				</a>
			</div>
		</div>
		<div id="top_banner"><p>Биржа перевозок ЛБР-АгроМаркет</p></div>
	</header>
	       
    <!--div class="wrapper"-->
	<div class="wrapper">
	   <ul class="wrapper-row">
		   <li class="sidebar">
				<div>
					<a href="/site/index/s/1">
						<span>Все перевозки</span>
					</a>
				</div>
				<div>
					<a href="/site/index/">
						<span>Все перевозки - кратко</span>
					</a>
				</div>
				<div>
					<a href="/site/active/">
						<span>Активные перевозки</span>
					</a>
				</div>
				<div>
					<a href="/site/archive/">
						<span> - Архивные перевозки, в которых выиграл - </span>
					</a>
				</div>
				<div>
					<a href="/site/archive/">
						<span> - Архивные перевозки, в которых проиграл - </span>
					</a>
				</div>
				<div>
					<a href="/site/option/">
						<span>Настройки</span>
					</a>
				</div>
				<div>
					<a href="/site/event/">
						<span> - События - </span>
					</a>
				</div>
		   </li>
		   <li>
		       <?php echo $content; ?>
		   </li>
	   </ul>
    </div>
	
	<div class="clear"></div>
	
	<footer>
		<div class="f-left">
			<img src="/images/logo-foot.gif" alt="Лого подвал ЛБР-Агромаркет" title="Логотип ЛБР-Агромаркет" />
		</div>
	</footer>
</body>
</html>
