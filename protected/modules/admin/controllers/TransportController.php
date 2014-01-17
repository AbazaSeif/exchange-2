<?php

class TransportController extends Controller
{
	public function actionIndex()
	{
		//if(Yii::app()->user->checkAccess('transport')){
			$criteria = new CDbCriteria();
			$sort = new CSort();
			//$sort->sortVar = 'sort';
			//$sort->defaultOrder = 'surname ASC';
			/*$sort->attributes = array(
							'group_id'=>array(
								'group_id'=>'Группа',
								'asc'=>'group_id ASC',
								'desc'=>'group_id DESC',
								'default'=>'desc',
							),
							'surname'=>array(
								'surname'=>'Фамилии',
								'asc'=>'surname ASC',
								'desc'=>'surname DESC',
								'default'=>'desc',
							),
			);*/
			$dataProvider = new CActiveDataProvider('Transport', 
					array(
						'criteria'=>$criteria,
						'sort'=>$sort,
						'pagination'=>array(
							'pageSize'=>'10'
						)
					)
			);
			
			if ($id_item = Yii::app()->user->getFlash('saved_id')){
				$model = User::model()->findByPk($id_item);
				$group = UserGroup::getUserGroupArray();
				$view = $this->renderPartial('edituser', array('model'=>$model, 'group'=>$group), true, true);
			}
			$this->render('transport', array('data'=>$dataProvider, 'view'=>$view));
		
		/*
		}else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
		*/
	}
	
	public function actionEditTransport($id)
	{ 
	    //Yii::app()->clientScript->registerCssFile('/css/ui/custom-theme/jquery-ui-1.9.2.custom.css');
		$model = Transport::model()->findByPk($id);
		//var_dump($model);
		/*$params = array('group'=>$model->group_id, 'userid'=>$id);
		if(Yii::app()->user->checkAccess('editUser', $params))
		{*/
			/*$group = UserGroup::getUserGroupArray();
			if (isset($_POST['User'])){
				$model->attributes = $_POST['User'];
				if($model->save()){
					Yii::app()->user->setFlash('saved_id', $model->id);
					Yii::app()->user->setFlash('message', 'Пользователь '.$model->login.' сохранен успешно.');
					$this->redirect('/admin/user/');
				}
			}*/
		$this->renderPartial('edittransport', array('model'=>$model));//, false, true);
		/*}else{
			throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
		}*/
	}
	
	public function actionEditUser($id)
	{
		/*
		$model = User::model()->findByPk($id);
		$params = array('group'=>$model->group_id, 'userid'=>$id);
		if(Yii::app()->user->checkAccess('editUser', $params))
		{
		*/
			$group = UserGroup::getUserGroupArray();
			if (isset($_POST['User'])){
				$model->attributes = $_POST['User'];
				if($model->save()){
					Yii::app()->user->setFlash('saved_id', $model->id);
					Yii::app()->user->setFlash('message', 'Пользователь '.$model->login.' сохранен успешно.');
					$this->redirect('/admin/user/');
				}
			}
			$this->renderPartial('user/edittransport', array('model'=>$model, 'group'=>$group), false, true);
		/*}else{
			throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
		}*/
	}
}