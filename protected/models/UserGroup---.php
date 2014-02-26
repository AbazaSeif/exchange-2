<?php

/**
 * This is the model class for table "user_group".
 *
 * The followings are the available columns in table 'user_group':
 * @property integer $id
 * @property string $name
 * @property integer $level
 *
 * The followings are the available model relations:
 * @property User[] $users
 */
class UserGroup extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_group';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('level', 'numerical', 'integerOnly'=>true),
			array('name', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, level', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'users' => array(self::HAS_MANY, 'User', 'group_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'level' => 'Level',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('level',$this->level);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserGroup the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
         //  Метод возвращает массив всех доступных групп пользователей для данного пользователя
        //  $only_id - если false, вернет массив вида ID=>имя, если true - вернет массив только из ID
        static public function getUserGroupArray($only_id = false){
                //$groups = UserGroup::model()->findAll('level>='.Yii::app()->user->_level);
                $groups = UserGroup::model()->findAll('level>='.Yii::app()->user->level);
                $groupsArray = array();
                $groups_id = array();
                foreach( $groups as $group ){
                    $groupsArray[$group->id] = $group->name;
                    array_push($groups_id, $group->id);
                }
                if ($only_id){
                    return $groups_id;
                }else{
                    return $groupsArray;
                }
        }
        
        //  Метод сохраняет связи Группа-Роль в таблицу соответствий
        //  $_POST['Roles'] - массив с наименованиями ролей
        protected function afterSave() {
            parent::afterSave();
                $auth=Yii::app()->authManager;
                $children = $auth->getAuthAssignments($this->id);
                foreach ($children as $name=>$child){
                    $auth->revoke($name, $this->id);
                }
                if (isset($_POST['Roles'])){
                    foreach ($_POST['Roles']['name'] as $item){
                        $auth->assign($item, $this->id);
                    }
                }
            return true;
        }
        
        //  Статический метод, проверяющий доступ к группе
        //  $params - массив параметров, где:
        //  $params['level'] - уровень изменяемой группы
        static function userGroupAccess($params){
            if ($params){
                if ($params['level']>Yii::app()->user->_level)
                    return true;
            }
            return false;
        }

        //  Метод устанавливает сортировку по-умолчанию
        public function defaultScope()
        {
                return array(
                    'order'=>$this->getTableAlias(false, false).'.level ASC'
                );
        }
}
