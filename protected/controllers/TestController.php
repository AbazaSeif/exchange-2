<?php
//require_once("/usr/share/pear/Mail.php");
//require_once("/usr/share/pear/Mail/mime.php");  

class TestController extends Controller
{
    public function actionIndex()
    {
       echo Yii::app()->basePath; 
       echo '<br/>';
       echo dirname(__FILE__).'/../../../../../../';
       exit; 
    }
}

