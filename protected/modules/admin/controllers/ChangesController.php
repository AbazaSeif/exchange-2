<?php

class ChangesController extends Controller
{
    public function actionIndex()
    {
        if(Yii::app()->user->checkAccess('readChanges'))
        {
            $criteria = new CDbCriteria();
            $sort = new CSort();
            $sort->sortVar = 'sort';
            $sort->defaultOrder = 'surname ASC';
            $sort->attributes = array(
                'group_id' => array(
                    'group_id' => 'Группа',
                    'asc' => 'group_id ASC',
                    'desc' => 'group_id DESC',
                    'default' => 'asc',
                ),
                'surname' => array(
                    'surname' => 'Фамилии',
                    'asc' => 'surname ASC',
                    'desc' => 'surname DESC',
                    'default' => 'asc',
                ),
                'name' => array(
                    'surname' => 'Имя',
                    'asc' => 'name ASC',
                    'desc' => 'name DESC',
                    'default' => 'asc',
                )
            );
            $dataProvider = new CActiveDataProvider('User', 
                array(
                    'criteria'=>$criteria,
                    'sort'=>$sort,
                    'pagination'=>array(
                        'pageSize'=>'10'
                    )
                )
            );
            $this->render('changes', array('data'=>$dataProvider, 'view'=>$view));
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
    
    public function actionShowChanges($id)
    {
        if(Yii::app()->user->checkAccess('readChanges')) {
            $criteria = new CDbCriteria();
            $criteria->addCondition('user_id = '.$id);
            $sort = new CSort();
            $sort->sortVar = 'sort';
            $sort->defaultOrder = 'date DESC';
            $dataProvider = new CActiveDataProvider('Changes', 
                array(
                    'criteria'=>$criteria,
                    'sort'=>$sort,
                    'pagination'=>array(
                        'pageSize'=>'10'
                    )
                )
            );
            
            $this->renderPartial('editchanges', array('data'=>$dataProvider), false, true);
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
}