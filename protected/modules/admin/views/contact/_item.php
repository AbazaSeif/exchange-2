<li>
<?php
if(Yii::app()->user->checkAccess('editContact')) {
    echo CHtml::link($data->surname.' '.$data->name, '/admin/contact/editcontact/id/'.$data->id.'/', array('id'=>'li_'.$data->id, 'class'=>'ajax'));
} else {
    echo '<span>'.$data->surname.' '.$data->name.'</span>';
}
?>
</li>