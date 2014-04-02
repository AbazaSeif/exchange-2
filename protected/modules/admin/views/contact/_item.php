<li>
<?php
$pos = strpos($data->company, '(');
$company = substr($data->company, 0, $pos);
if(Yii::app()->user->checkAccess('trEditUserContact')) {
    echo CHtml::link($data->surname.' '.$data->name .' ('.$company.')', '/admin/contact/editcontact/id/'.$data->id.'/', array('id'=>'li_'.$data->id, 'class'=>'ajax'));
} else {
    echo '<span>'.$data->surname.' '.$data->name.' ('.$company.')'. '</span>';
}
?>
</li>