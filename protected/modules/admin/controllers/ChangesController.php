<?php

class ChangesController extends Controller
{
    public function actionIndex($input = null)
    {
        //if(Yii::app()->user->checkAccess('readChanges'))
        //{
            $filter = $idString = $idNumeric = array();
            $model = new Changes;
            $model->unsetAttributes();

            if (!empty($_GET['Changes']))
                $model->attributes = $_GET['Changes'];
            
            $users = Changes::model()->findAll(array(
                'select'=>'user_id',
                'group'=>'user_id',
                'distinct'=>true,
            ));
            
            foreach($users as $user){
                $id = $user['user_id'];
                if(is_numeric($id)) {
                    $idNumeric[] = $id;
                } else {
                    $idString[] = $id;
                }
            }
            
            if(!empty($idNumeric)) {
                $intResults = Yii::app()->db_auth->createCommand()
                    ->select('id, surname, name, secondname')
                    ->from('user')
                    ->where(array('in', 'id', $idNumeric))
                    ->queryAll()
                ;
                
                foreach($intResults as $result){
                    $filter[$result['id']] = $result['surname'].' '.$result['name'].' '.$result['secondname'];
                }
            }
            
            if(!empty($idString)) {
                $stringResults = Yii::app()->db_auth->createCommand()
                    ->select('login, surname, name, secondname')
                    ->from('user')
                    ->where(array('in', 'login', $idString))
                    ->queryAll()
                ;
                
                foreach($stringResults as $result){
                    $name = $result['surname'].' '.$result['name'].' '.$result['secondname'];
                    if(!in_array($name, $filter))
                       $filter[$result['login']] = $name;
                }
            }            

            asort($filter);
            
            $this->render('changes', array(
                'model'=>$model,
                'filter'=>$filter,
            ));
        /*} else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }*/
    }
    
    public function actionShowChanges($id)
    {
        if(Yii::app()->user->checkAccess('readChanges')) 
        {
            $userId = Changes::model()->findByPk($id)->user_id;
            $user = $user = Yii::app()->db_auth->createCommand()
                ->from('user')
                ->where('id = '.$userId)
                ->queryRow()
            ;
            $userName = $header_form = '"'.$user[surname].' '.$user[name].'"';
            
            $criteria = new CDbCriteria();
            $criteria->addCondition('user_id = '.$userId);
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
            $this->render('showchanges', array('data'=>$dataProvider, 'user'=>$userName), false, true);
        } else {
            throw new CHttpException(403,Yii::t('yii','У Вас недостаточно прав доступа.'));
        }
    }
}
