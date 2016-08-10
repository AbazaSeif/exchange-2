<?php
class TestController extends Controller
{
    public function actionIndex()
    {
        TransportInterPoint::model()->deleteAll('t_id=:tid', array(':tid' => 394));
        echo 394;
    }
}
