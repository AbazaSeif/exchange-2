<?php
class UserdelrateController extends Controller
{
    public function actionIndex($input = null)
    {
        
        $this->render('history', array('input'=>$input));
    }
}

