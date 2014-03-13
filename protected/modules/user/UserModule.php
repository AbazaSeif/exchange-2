<?php

class UserModule extends CWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'user.models.*',
			'user.components.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
                    if(Yii::app()->user->isGuest){
                        Yii::app()->user->returnUrl = Yii::app()->request->requestUri;
                        Yii::app()->request->redirect('/user/login/');
                    } else{
                        return true;
                    }
		}
		else
			return false;
	}
}
