<?php

class TransportController extends Controller
{
	public function actionIndex()
	{
		//if(Yii::app()->user->checkAccess('transport')){
			$criteria = new CDbCriteria();
			$sort = new CSort();
			$sort->sortVar = 'sort';
			$sort->defaultOrder = 'location_from ASC';
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
				$model = Transport::model()->findByPk($id_item);
				$group = UserGroup::getUserGroupArray();
				$view = $this->renderPartial('edittransport', array('model'=>$model, 'group'=>$group), true, true);
			}
			$this->render('transport', array('data'=>$dataProvider, 'view'=>$view));
		
		/*
		}else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
		*/
	}
	
	public function actionCreateTransport()
	{
		//if(Yii::app()->user->checkAccess('createTransport')){
			$model = new Transport();
			$model->date_from = date('Y-m-d H:i');
			$model->date_to = date('Y-m-d H:i');
			//$group = UserGroup::getUserGroupArray();
			if (isset($_POST['Transport'])){
				$model->attributes = $_POST['Transport'];
				if($model->save()){
					Yii::app()->user->setFlash('saved_id', $model->id);
					Yii::app()->user->setFlash('message', 'Перевозка создана успешно.');
					$this->redirect('/admin/transport/');
				}
			}
			$this->renderPartial('edittransport', array('model'=>$model));//, 'group'=>$group), false, true);
		/*} else {
			throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
		}*/
	}
	
	public function actionEditTransport($id)
	{
		$model = Transport::model()->findByPk($id);
		
		//var_dump($model);
		/*$params = array('group'=>$model->group_id, 'userid'=>$id);
		if(Yii::app()->user->checkAccess('editUser', $params))
		{*/
			//$group = UserGroup::getUserGroupArray();
			if (isset($_POST['Transport'])){
				$model->attributes = $_POST['Transport'];
				if($model->save()){
				//	Yii::app()->user->setFlash('saved_id', $model->id);
				//	Yii::app()->user->setFlash('message', 'Перевозка сохранена успешно.');
					$this->redirect('/admin/transport/');
				}
			}
		$this->renderPartial('edittransport', array('model'=>$model)); //, false, true);
		/*
		}else{
			throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
		}
		*/
	}
	
	public function actionDeleteTransport($id)
	{
		$model = Transport::model()->findByPk($id);
		//$params = array('group'=>$model->group_id, 'userid'=>$id);
	   // if(Yii::app()->user->checkAccess('deleteTransport', $params) && $id != Yii::app()->user->getState('_id')) {
			if(Transport::model()->deleteByPk($id)){
				Yii::app()->user->setFlash('message', 'Перевозка "' . $model->location_from . ' &mdash; ' . $model->location_to . '" удалена успешно.');
				$this->redirect('/admin/transport/');
			}
	   /* } else {
			throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
		}*/
	}
}