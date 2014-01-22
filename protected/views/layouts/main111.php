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
	<!--link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" /-->
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/back/backend.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/front/frontend.css" />
	
	<?php
		Yii::app()->clientScript->registerCoreScript('jquery'); 
		$cs=Yii::app()->clientScript;  
		$cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery-1.8.3.js', CClientScript::POS_HEAD);  
		$cs->registerScriptFile(Yii::app()->baseUrl . '/js/front/timer.js', CClientScript::POS_HEAD);  
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
				<!--a href="/" title="Вход на сайт">
				   <?php echo Yii::app()->user->isGuest ? "Вход":"Выход"; ?>
				</a-->
			</div>
		</div>
		<div id="top_banner"><p>Биржа перевозок ЛБР-АгроМаркет</p></div>
	</header>
	       
    <!--div class="wrapper"-->
	<div class="wrapper">
	   <ul class="wrapper-row">
		   <li class="sidebar">
				<!--div>
					<a href="/site/index/s/1/">
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
					<a href="/site/archive/s/1/">
						<span> Архивные перевозки, в которых выиграл </span>
					</a>
				</div>
				<div>
					<a href="/site/archive/">
						<span> Архивные перевозки, в которых проиграл </span>
					</a>
				</div>
				<div>
					<a href="/site/option/">
						<span>Настройки</span>
					</a>
				</div>
				<div>
					<a href="/site/event/">
						<span>События</span>
					</a>
				</div>
				<div style="margin-top: 20px;">Для backend: </div>
				<div>
					<a href="/site/transport/">
						<span> - Все перевозки</span>
					</a>
				</div>
				<div>
					<a href="/site/createUser/">
						<span> - Создание перевозки</span>
					</a>
				</div-->
				
				<div>
				    <ul id="vert_menu">
						<li><a href="/site/index/s/1/"><span>Все перевозки</span></a></li>
						<li><a href="/site/index/"><span>Все перевозки - кратко</span></a></li>
						<li><a href="/site/active/"><span>Активные перевозки</span></a></li>
						<li><a href="/site/archive/s/1/"><span>Архивные - выиграл</span></a></li>
						<li><a href="/site/archive/"><span>Архивные - проиграл</span></a></li>
						<li><a href="/site/option/"><span>Настройки</span></a></li>
						<li>
						    <a href="/site/event/">
								<span id="events-menu">
									События
									<span id="events-count" style="float: right; margin-right: 10px">
									    1111
									</span>
								</span>
						    </a>
						</li>
					</ul>
  			    </div>
				
				<!--div>
					<a href="/site/transport/">
						<span> - Все перевозки</span>
					</a>
				</div>
				<div>
					<a href="/site/createUser/">
						<span> - Создание перевозки</span>
					</a>
				</div-->
				<!--div class="del">
				    <ul id="nav">
						<li><a href="#"><img src="/images/111/t1.png" /> Home</a></li>
						<li><a href="#" class="sub" tabindex="1"><img src="/images/111/t2.png" />HTML/CSS</a><img src="/images/111/up.gif" alt="" />
							<ul>
								<li><a href="#"><img src="/images/111/empty.gif" />Link 1</a></li>
								<li><a href="#"><img src="/images/111/empty.gif" />Link 2</a></li>
								<li><a href="#"><img src="/images/111/empty.gif" />Link 3</a></li>
								<li><a href="#"><img src="/images/111/empty.gif" />Link 4</a></li>
								<li><a href="#"><img src="/images/111/empty.gif" />Link 5</a></li>
							</ul>
						</li>
						<li><a href="#" class="sub" tabindex="1"><img src="/images/111/t3.png" />jQuery/JS</a><img src="/images/111/up.gif" alt="" />
							<ul>
								<li><a href="#"><img src="/images/111/empty.gif" />Link 6</a></li>
								<li><a href="#"><img src="/images/111/empty.gif" />Link 7</a></li>
								<li><a href="#"><img src="/images/111/empty.gif" />Link 8</a></li>
								<li><a href="#"><img src="/images/111/empty.gif" />Link 9</a></li>
								<li><a href="#"><img src="/images/111/empty.gif" />Link 10</a></li>
							</ul>
						</li>
						<li><a href="#"><img src="/images/111/t2.png" />PHP</a></li>
						<li><a href="#"><img src="/images/111/t2.png" />MySQL</a></li>
						<li><a href="#"><img src="/images/111/t2.png" />XSLT</a></li>
					</ul>
				</div-->
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
