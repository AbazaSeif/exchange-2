<?php
class TestController extends Controller
{
    public function actionIndex()
    {
        Transport::model()->findByPk(3871)->delete();
        Transport::model()->findByPk(3872)->delete();
    }
}
