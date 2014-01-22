<li>
<?php
//$params = array('group'=>$data->group_id,'userid'=>$data->id);
//if(Yii::app()->user->checkAccess('editUser', $params))
//{
    echo CHtml::link($data->location_from.' &mdash; '.$data->location_to, '/admin/transport/edittransport/id/'.$data->id.'/', array('id'=>'li_'.$data->id, 'class'=>'ajax'));
/*
}else{
    echo '<span>'.$data->surname.' '.$data->name.' ('.$data->group->name.')</span>';
}
*/
?>
</li>