<?php

class RateController extends Controller
{
    /*public function actionIndex()
    {
        if(Yii::app()->user->checkAccess('Rate')){
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
                $view = $this->renderPartial('editrate', array('model'=>$model), true, true);
            }
            $this->render('rate', array('data'=>$dataProvider, 'view'=>$view));
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }*/
        
    public function actionCreateRate()
    {
        if(Yii::app()->user->checkAccess('createRate')){
            $model = new Rate();
            $model['date'] = date('Y-m-d H:i');
            if (isset($_POST['Rate'])){
                $model->attributes = $_POST['Rate'];
                $model->save();
            }
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionEditRate($id)
    {
        $model = Rate::model()->findByPk($id);
        if(Yii::app()->user->checkAccess('editRate'))
        {
            if (isset($_POST['Rate'])){
                $model->attributes = $_POST['Rate'];
                $model->save();
            }
        } else {
             throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }

    public function actionDeleteRate($id)
    {
        if(Yii::app()->user->checkAccess('deleteRate') && $id != Yii::app()->user->getState('_id')) {
            Rate::model()->deleteByPk($id);
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
}