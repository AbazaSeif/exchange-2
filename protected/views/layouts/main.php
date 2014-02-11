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
        <link rel="stylesheet" type="text/css" href="/css/front/jquery.mCustomScrollbar.css" />

        <?php
			Yii::app()->clientScript->registerCoreScript('jquery');
            Yii::app()->clientScript->registerScriptFile('/js/jquery.dotdotdot.min.js');
            Yii::app()->clientScript->registerScriptFile('/js/jquery.mCustomScrollbar.concat.min.js');
			Yii::app()->clientScript->registerScriptFile('/js/front/Timer.js');
			Yii::app()->clientScript->registerScriptFile('/js/front/rateList.js');
			Yii::app()->clientScript->registerScriptFile('/js/front/menu.js');
			Yii::app()->clientScript->registerScriptFile('/js/front/frontend.js');
        ?>
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
                    <p>2014 &copy; ООО "ЛБР-Агромаркет"</p>
                </div>
            </div>
            <div class="w-right">
                <?php $this->renderPartial( 'user.views.site.dialog' ); ?>
                <?php echo $content; ?>
                <!--div id="content_1" class="content">
                    <p>Lorem ipsum dolor sit amet. Aliquam erat volutpat. Maecenas non tortor nulla, non malesuada velit.</p>
                    <p>Aliquam erat volutpat. Maecenas non tortor nulla, non malesuada velit. Nullam felis tellus, tristique nec egestas in, luctus sed diam. Suspendisse potenti. </p>
                    <p>Consectetur adipiscing elit. Nulla consectetur libero consectetur quam consequat nec tincidunt massa feugiat. Donec egestas mi turpis. Fusce adipiscing dui eu metus gravida vel facilisis ligula iaculis. Cras a rhoncus massa. Donec sed purus eget nunc placerat consequat.</p>
                    <p>Cras venenatis condimentum nibh a mollis. Duis id sapien nibh. Vivamus porttitor, felis quis blandit tincidunt, erat magna scelerisque urna, a faucibus erat nisl eget nisl. Aliquam consequat turpis id velit egestas a posuere orci semper. Mauris suscipit erat quis urna adipiscing ultricies. In hac habitasse platea dictumst. Nulla scelerisque lorem quis dui sagittis egestas.</p> 
                    <p>Etiam sed massa felis, aliquam pellentesque est.</p>
                    <p>Nam eu arcu at purus tincidunt pharetra ultrices at ipsum. Mauris urna nunc, vulputate quis gravida in, pharetra id mauris. Ut sit amet mi dictum nulla lobortis adipiscing quis a nulla. Etiam diam ante, imperdiet vel scelerisque eget, venenatis non eros. Praesent ipsum sem, eleifend ut gravida eget, tristique id orci. Nam adipiscing, sem in mattis vulputate, risus libero adipiscing risus, eu molestie mi justo eget nulla.</p> 
                    <p>Cras venenatis metus et urna egestas non laoreet orci rutrum. Pellentesque ullamcorper dictum nisl a tincidunt. Quisque et lacus quam, sed hendrerit mi. Mauris pretium, sapien et malesuada pulvinar, lorem leo viverra leo, et egestas mi nisl quis odio. </p>
                    <p>Aliquam erat volutpat. Sed urna arcu, tempus eu vulputate adipiscing, consectetur et orci. Vivamus congue, nunc vitae fringilla convallis, libero massa lacinia lorem, id convallis mauris elit ut leo. Nulla vel odio sem. Duis lorem urna, congue vitae rutrum sed, tincidunt vel tortor. In hac habitasse platea dictumst. Nunc vitae enim ante, vitae facilisis massa. Etiam sagittis sapien at nibh fermentum consectetur convallis lacus blandit.</p>
                    <p>the end.</p>
                </div-->
            </div>
        </div>
    </body>
</html>
