<?php

class ChangesController extends Controller
{
    public function actionIndex($input = null)
    {
        if(Yii::app()->user->checkAccess('readChanges'))
        {
            $criteria = new CDbCriteria();
            
            if(User::prepareSqlite()) {
                if(!empty($input)) {
                    $criteria->condition = 'lower(name) like lower(:input) or lower(surname) like lower(:input) or lower(secondname) like lower(:input) or lower(email) like lower(:input)';
                    $criteria->params = array(':input' => '%' . $input . '%');
                }
            }           

            $sort = new CSort();
            $sort->sortVar = 'sort';
            $sort->defaultOrder = 'surname ASC';
            $sort->attributes = array(
                'surname' => array(
                    'surname' => 'Фамилия',
                    'asc' => 'surname ASC',
                    'desc' => 'surname DESC',
                    'default' => 'asc',
                ),
                'name' => array(
                    'name' => 'Имя',
                    'asc' => 'name ASC',
                    'desc' => 'name DESC',
                    'default' => 'asc',
                ),
                'secondname' => array(
                    'name' => 'Отчество',
                    'asc' => 'secondname ASC',
                    'desc' => 'secondname DESC',
                    'default' => 'asc',
                )
            );
            
            $dataProvider = new CActiveDataProvider('AuthUser', 
                array(
                    'criteria'=>$criteria,
                    'sort'=>$sort,
                    'pagination'=>array(
                        'pageSize'=>'8'
                    )
                )
            );
            $this->render('changes', array('data'=>$dataProvider, 'input'=>$input));
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
    
    public function actionDateOrder()
    {
        if(Yii::app()->user->checkAccess('readChanges'))
        {
            $criteria = new CDbCriteria();
            $criteria->distinct = true;
            $criteria->group='user_id';
            $criteria->select = '*, max(date) as last_edit';
           
            $sort = new CSort();
            $sort->sortVar = 'sort';
            $sort->defaultOrder = 'last_edit DESC';
            $sort->attributes = array(
                'last_edit' => array(
                    'asc' => 'last_edit ASC',
                    'desc' => 'last_edit DESC',
                    'default' => 'asc',
                )
            );
            
            $dataProvider = new CActiveDataProvider('Changes', 
                array(
                    'criteria'=>$criteria,
                    'sort'=>$sort,
                    'pagination'=>array(
                        'pageSize'=>'8'
                    )
                )
            );
            $this->render('changes', array('data'=>$dataProvider));
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
            $this->render('editchanges', array('data'=>$dataProvider, 'id'=>$id), false, true);
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
    
    public function actionSearch()
    {
        $query = trim($_GET['q']);
        $result = array();
        if(User::prepareSqlite()) {
            if (!empty($query)){
                $result = Yii::app()->db_auth->createCommand()
                    ->select('id, name, secondname, surname, email')
                    ->from('user')
                    ->where('lower(name) like lower("%'.$query.'%") or lower(surname) like lower("%'.$query.'%") or lower(secondname) like lower("%'.$query.'%") or lower(email) like lower("%'.$query.'%")')
                    ->limit(7)
                    ->queryAll()
                ;
            }
        }
        $this->renderPartial('application.modules.admin.views.default.quickAjaxResult', array('data' =>$result, 'changesFlag' => true));
    }
}