<?php
class TestController extends Controller
{
    public function actionIndex()
    {
        Transport::model()->findByPk(4283)->delete();
        echo '4283';
    }
}
