<li>
<?php
if(Yii::app()->user->checkAccess('editUser'))
{
    echo CHtml::link($data->company, '/admin/user/edituser/id/'.$data->id.'/', array('id'=>'li_'.$data->id, 'class'=>'ajax'));
}else{
    echo '<span>'.$data->company.'</span>';
}
?>
</li>