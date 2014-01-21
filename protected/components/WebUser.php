<?php

class WebUser extends CWebUser
{
    public $loginUrl=array('/user/login/');

    public function getIsRoot()
    {
            return $this->_level==='0';
    }
    
    public function checkAccess($operation,$params=array(),$allowCaching=true)
    {
        if($this->getIsRoot())
            return true;
        else
            return parent::checkAccess($operation,$params,$allowCaching);
        
    }
}