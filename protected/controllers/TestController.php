<?php
class TestController extends Controller
{
    public function actionIndex()
    {
        Transport::model()->findByPk(3870)->delete();
    }
}
