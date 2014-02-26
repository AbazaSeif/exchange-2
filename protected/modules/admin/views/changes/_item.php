<li>
<?php
echo CHtml::link($data->surname.' '.$data->name, '/admin/changes/showchanges/id/'.$data->id.'/', array('id'=>'li_'.$data->id, 'class'=>'ajax'));
?>
</li>