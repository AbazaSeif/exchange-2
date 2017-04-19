<?php
class TestController extends Controller
{
    public function actionIndex()
    {
        Transport::model()->findByPk(1151)->delete();
        echo '1151';
    }
}
