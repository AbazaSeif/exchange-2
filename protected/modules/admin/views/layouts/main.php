<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title>Административная панель</title>
    </head>
    <body>
        <header>
            <div class="menu">
                <a target="_blank" href="/" class="logo-link">Биржа ЛБР</a>
            <?php
            $menu = Yii::app()->params['menu_admin'];
            echo returnMenu($menu);
            
            if(!Yii::app()->user->isGuest){
            ?>
                <a href="/user/logout/" class="admin-logout">Выход</a>
                <a href="/admin/faq/" class="admin-logout">ЧЗВО</a>
            <?php
            }
            ?>
            </div>
        </header>
        <div class="wrapper">
            <?php echo $content; ?>
        </div>
    </body>
</html>


<?php
function returnMenu($arr){
    $menu = '<ul class="nav">';
    foreach ($arr as $key=>$link){
        if (is_array($link)){
            $menu .= '<li class="item parent"><span>'.$key.'</span>'.returnMenu($link).'</li>';
        }else{
            $menu .= '<li class="item"><a href="'.$link.'">'.$key.'</a></li>';
        }
    }
    $menu .= '</ul>';
    return $menu;
  }
?>

<script>
     $(function() {
        $('.menu .nav li.item').click(function(){
            //console.log(11);
            sessionStorage.clear();
        });
     });
</script>