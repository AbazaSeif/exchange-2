<?php

/**
 * This is the model class for table "group".
 *
 * The followings are the available columns in table 'group':
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 * @property integer $level
 * @property string $description
 *
 * The followings are the available model relations:
 * @property Group $parent
 * @property Group[] $groups
 * @property User[] $users
 */
class AuthGroup extends CActiveRecord
{
    public function getDbConnection(){
        return Yii::app()->db_auth;
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'group';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('parent_id, level', 'numerical', 'integerOnly'=>true),
            array('name, description', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, parent_id, level, description', 'safe', 'on'=>'search'),
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
            'parent' => array(self::BELONGS_TO, 'Group', 'parent_id'),
            'groups' => array(self::HAS_MANY, 'Group', 'parent_id'),
            'users' => array(self::HAS_MANY, 'User', 'g_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Название',
            'parent_id' => 'Родитель',
            'level' => 'Уровень',
            'description' => 'Описание',
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
        $criteria->compare('parent_id',$this->parent_id);
        $criteria->compare('level',$this->level);
        $criteria->compare('description',$this->description,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Group the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function behaviors()
    {
        return array(
            'nestedSetBehavior'=>array(
                'class'=>'ext.yiiext.behaviors.trees.NestedSetBehavior',
                'leftAttribute'=>'lft',
                'rightAttribute'=>'rgt',
                'levelAttribute'=>'level',
                'hasManyRoots'=>false,
            ),
        );
    }

    public function defaultScope()
    {
        return array(
            'order'=>$this->getTableAlias(false, false).'.lft ASC'
        );
    }

    static function getUserGroupArray($only_id = false){
        $groups = Group::model()->findAll();
        $groupsArray = array();
        $groups_id = array();
        foreach( $groups as $group ){
            $groupsArray[$group->id] = str_repeat('— ', $group->level).' '.$group->name;
            array_push($groups_id, $group->id);
        }
        if ($only_id){
            return $groups_id;
        }else{
            return $groupsArray;
        }
    }
}
