<?php
//require_once("/usr/share/pear/Mail.php");
//require_once("/usr/share/pear/Mail/mime.php");  

class TestController extends Controller
{
    public function actionIndex()
    {
       echo Yii::app()->basePath; 
       echo '<br/>';
      // if(file_exists(dirname(__FILE__).'/../../../../../../../usr/share/pear/Mail.php'))
       if(file_exists('/usr/share/pear/Mail.php'))
           echo '1';
       else echo 0;
       //echo dirname(__FILE__).'/../../../../../../../';
       exit; 
    }
}

