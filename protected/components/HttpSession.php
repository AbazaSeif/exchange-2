<?php

class HttpSession extends CHttpSession{
    
    public function init()
    {
        $this->setSessionName('lbrsession');
        $this->setCookieParams(array('domain'=>'.'.Yii::app()->params['host']));
        parent::init();
    }
}
