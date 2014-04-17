<?php
//require_once("/usr/share/pear/Mail.php");
//require_once("/usr/share/pear/Mail/mime.php");  

class TestController extends Controller
{
    public function actionIndex()
    {
       echo Yii::app()->basePath; 
       echo '<br/>';
       echo dirname(__FILE__); 
       echo '<br/>';
       //if(file_exists(dirname(__FILE__).'/../../../../../../../../usr/share/pear/Mail.php'))
       $path = dirname(__FILE__).'/../../yii/';
       if(file_exists($path))
      // if(file_exists('/usr/share/pear/Mail.php'))
           echo '1 - ' . $path;
       else echo 0;
       //echo dirname(__FILE__).'/../../../../../../../';
       exit; 
    }
}

