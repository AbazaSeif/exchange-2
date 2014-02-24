<?php

class WebUser extends CWebUser
{
    public $loginUrl=array('/users/login/');

    // Автоматический вход на сайт
    public $allowAutoLogin = true;

    public function isTransport()
    {
        return $this->transport==='1';
    }

    // Проверка на суперпользователя(root)
    public function getIsRoot()
    {
        return $this->level==='0';
    }

    public function getStateKeyPrefix()
    {
        return 'lbragromarket';
    }

    // Проверяет соответствие ключей, при автоматическом входе на сайт
    protected function beforeLogin($id,$states,$fromCookie,$cookie=false)
    {
        if($fromCookie)
        {
            $key = AuthKey::model()->find('key=:k AND user_id=:u', array(':k'=>$cookie->value, ':u'=>$id));
            if(!$key)
                return false;
        }
        return true;
    }

    // Удаляет ключ из базы при выходе из сайта
    protected function beforeLogout()
    {
        AuthKey::model()->deleteAll('user_id=:uid',array(':uid'=>$this->_id));
        return true;
    }

    // Метод добавляет cookie для залогинившегося пользователя и записывает ключ в базу
    // для дальнейшей автоматической авторизации на других ресурсах ЛБР
    protected function saveToCookie($duration)
    {
        $app=Yii::app();
        $cookie=$this->createIdentityCookie($this->getStateKeyPrefix());
        $cookie->expire=time()+$duration;

        // Устанавливает cookie для всех поддоменов
        $cookie->domain='.'.$app->params[host];

        $time = time();
        $data=array(
            $this->getId(),
            $this->getName(),
            $duration,
            $this->saveIdentityStates()
        );
        $cookie->value=$app->getSecurityManager()->hashData(serialize($data));

        // Регенерирует ключ, либо создает новый в базе
        $key = AuthKey::model()->find('user_id=:u', array(':u'=>$this->getId()));

        if(!$key)
            $key = new AuthKey();

        $key->key = $cookie->value;
        $key->user_id = $this->_id;
        $key->date = $time;
        if($key->save())
            $app->getRequest()->getCookies()->add($cookie->name,$cookie);

    }

    protected function restoreFromCookie()
    {
        $app=Yii::app();
        $request=$app->getRequest();
        $cookie=$request->getCookies()->itemAt($this->getStateKeyPrefix());
        if($cookie && !empty($cookie->value) && is_string($cookie->value) && ($data=$app->getSecurityManager()->validateData($cookie->value))!==false)
        {
            $data=@unserialize($data);
            if(is_array($data) && isset($data[0],$data[1],$data[2],$data[3]))
            {
                list($id,$name,$duration,$states)=$data;
                // В метод передается дополнительная переменная $cookie
                if($this->beforeLogin($id,$states,true,$cookie))
                {
                    $this->changeIdentity($id,$name,$states);
                    if($this->autoRenewCookie)
                    {
                        $this->saveToCookie($duration);
                    }
                    $this->afterLogin(true);
                }
            }
        }
    }

    // Метод возвращает true, если пользователь root либо имеет права на проверяемую операцию
    public function checkAccess($operation,$params=array(),$allowCaching=true)
    {
        if($this->getIsRoot())
            return true;
        else
            return parent::checkAccess($operation,$params,$allowCaching);

    }

}