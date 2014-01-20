<?php

class RateController extends Controller
{
	public function actionIndex()
	{
		//if(Yii::app()->user->checkAccess('Rate')){
			$criteria = new CDbCriteria();
			$sort = new CSort();
			$sort->sortVar = 'sort';
			$sort->defaultOrder = 'id ASC';
			$dataProvider = new CActiveDataProvider('Rate', 
					array(
						'criteria'=>$criteria,
						'sort'=>$sort,
						'pagination'=>array(
							'pageSize'=>'10'
						)
					)
			);
			
			if ($id_item = Yii::app()->user->getFlash('saved_id')){
				$model = Rate::model()->findByPk($id_item);
				$group = UserGroup::getUserGroupArray();
				$view = $this->renderPartial('editrate', array('model'=>$model, 'group'=>$group), true, true);
			}
			$this->render('rate', array('data'=>$dataProvider, 'view'=>$view));
		/*
		}else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
		*/
	}
	
	public function actionCreateRate()
	{
		//if(Yii::app()->user->checkAccess('createRate')){
			$model = new Rate();
			$model['date'] = date('Y-m-d H:i');
			//$group = UserGroup::getUserGroupArray();
			if (isset($_POST['Rate'])){
				$model->attributes = $_POST['Rate'];
				if($model->save()){
					Yii::app()->user->setFlash('saved_id', $model->id);
					Yii::app()->user->setFlash('message', 'Ставка создана успешно.');
					$this->redirect('/admin/rate/');
				}
			}
			$this->renderPartial('editRate', array('model'=>$model));//, 'group'=>$group), false, true);
		/*} else {
			throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
		}*/
	}
	
	public function actionEditRate($id)
	{
		$model = Rate::model()->findByPk($id);
		
		//var_dump($model);
		/*$params = array('group'=>$model->group_id, 'userid'=>$id);
		if(Yii::app()->user->checkAccess('editUser', $params))
		{*/
			//$group = UserGroup::getUserGroupArray();
			if (isset($_POST['Rate'])){
				$model->attributes = $_POST['Rate'];
				if($model->save()){
				//	Yii::app()->user->setFlash('saved_id', $model->id);
				//	Yii::app()->user->setFlash('message', 'Ставка сохранена успешно.');
					$this->redirect('/admin/rate/');
				}
			}
		$this->renderPartial('editRate', array('model'=>$model)); //, false, true);
		/*
		}else{
			throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
		}
		*/
	}
	
	public function actionDeleteRate($id)
	{
		$model = Rate::model()->findByPk($id);
		//$params = array('group'=>$model->group_id, 'userid'=>$id);
	   // if(Yii::app()->user->checkAccess('deleteRate', $params) && $id != Yii::app()->user->getState('_id')) {
			if(Rate::model()->deleteByPk($id)){
				Yii::app()->user->setFlash('message', 'Ставка удалена успешно.');
				$this->redirect('/admin/rate/');
			}
	   /* } else {
			throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
		}*/
	}
}