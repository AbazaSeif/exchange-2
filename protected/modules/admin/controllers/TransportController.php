<?php

class TransportController extends Controller
{
	public function actionIndex()
	{
        if(Yii::app()->user->checkAccess('readTransport'))
        {
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
		}else{
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
	}
	
	public function actionCreateTransport()
	{
		if(Yii::app()->user->checkAccess('createTransport')){
			$model = new Transport();
			$model->date_from = date('Y-m-d H:i');
			$model->date_to = date('Y-m-d H:i');
			if (isset($_POST['Transport'])){
				$model->attributes = $_POST['Transport'];
				if($model->save()){
					Yii::app()->user->setFlash('saved_id', $model->id);
					Yii::app()->user->setFlash('message', 'Перевозка создана успешно.');
					$this->redirect('/admin/transport/');
				}
			}
			$this->renderPartial('edittransport', array('model'=>$model));//, 'group'=>$group), false, true);
		} else {
			throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
		}
	}
	
	public function actionEditTransport($id)
	{
        //var_dump($_POST['Transport']); 
        //exit;
        if(Yii::app()->user->checkAccess('editTransport')){
            $model = Transport::model()->findByPk($id);
            $rates = Rate::model()->findAll(array('order'=>'date desc', 'condition'=>'transport_id='.$id));
            if (isset($_POST['Transport'])){
                ///!!!!
                //echo '<pre>';
                //var_dump($_POST['Rates']); exit;
                
                $model->attributes = $_POST['Transport'];
                if($model->save()){
                    Yii::app()->user->setFlash('saved_id', $model->id);
                    Yii::app()->user->setFlash('message', 'Перевозка сохранена успешно.');
                    $this->redirect('/admin/transport/');
                }
            }
            $this->renderPartial('edittransport', array('model'=>$model, 'rates'=>$rates)); //, false, true);
		}else{
			throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
		}
	}
	
	public function actionDeleteTransport($id)
	{
        if(Yii::app()->user->checkAccess('deleteTransport')){
		    $model = Transport::model()->findByPk($id);
			if(Transport::model()->deleteByPk($id)){
				Yii::app()->user->setFlash('message', 'Перевозка "' . $model->location_from . ' &mdash; ' . $model->location_to . '" удалена успешно.');
				$this->redirect('/admin/transport/');
			}
	    } else {
			throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
		}
	}
}