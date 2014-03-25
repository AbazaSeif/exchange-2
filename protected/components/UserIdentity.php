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
        $status = 1;
        if(is_numeric($this->username)) {
           $record = User::model()->findByAttributes(array('inn'=>$this->username));
        } else {
            $record = User::model()->findByAttributes(array('email'=>$this->username));
            if(!$record) {
                $record = AuthUser::model()->findByAttributes(array('login'=>$this->username));
                $status = 0;
            } else if($record->type_contact){
                $status = 2;
            }
        }
        
        $this->errorCode = $this->getError($record);
        if($this->errorCode==self::ERROR_NONE) {
            if($status=='0') {
                $this->_id = $record->g_id;
                $this->setState('level', AuthGroup::model()->findByPk($record->g_id)->level);
            }
            $this->setState('_id', $record->id);
            $this->setState('transport', $status);
        }
        return $this->errorCode;
    }

    protected function getError($user=null)
    {
        if($user===null)
            return self::ERROR_USERNAME_INVALID;
        elseif($user->password!==crypt($this->password,$user->password))
            return self::ERROR_PASSWORD_INVALID;
        elseif($user->status && in_array($user->status, array(User::USER_TEMPORARY_BLOCKED, User::USER_BLOCKED, User::USER_NOT_CONFIRMED)))
            return 1000 + $user->status;
        else
            return self::ERROR_NONE;
    }
    
    public function getId()
    {
        return $this->_id;
    }
}