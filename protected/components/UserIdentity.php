<?php

/**
* UserIdentity represents the data needed to identity a user.
* It contains the authentication method that checks if the provided
* data can identity the user.
*/
class UserIdentity extends CUserIdentity
{
    private $_id;
    
    public function authenticate()
    {
        $record = User::model()->findByAttributes(array('login'=>$this->username));
        
        if($record===null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } else if($record->password!==crypt($this->password,$record->password)) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else if(in_array($record['status'], array(User::USER_TEMPORARY_BLOCKED, User::USER_BLOCKED, User::USER_NOT_CONFIRMED))) {
            $this->errorCode = 1000 + $record['status'];
        } else {
            $this->_id = $record->group_id;
            $this->setState('_id', $record->id);
            $this->setState('_level', UserGroup::model()->findByPk($record->group_id)->level);
            $this->errorCode=self::ERROR_NONE;
        }
        return $this->errorCode;
    }

    public function getId()
    {
        return $this->_id;
    }
}