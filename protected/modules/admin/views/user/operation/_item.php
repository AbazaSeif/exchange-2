<li>
<?php
if(Yii::app()->user->checkAccess('editOperation'))
{
    echo CHtml::link($data->name, '/admin/user/editoperation/name/'.$data->name.'/', array('id'=>$data->name, 'class'=>'ajax'));
}else{
    echo '<span>'.$data->name.'</span>';
}
?>
</li>