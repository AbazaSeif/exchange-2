<li>
<?php
    echo CHtml::link($data->location_from.' &mdash; '.$data->location_to, '/admin/transport/edittransport/id/'.$data->id.'/', array('id'=>'li_'.$data->id, 'class'=>'ajax'));
?>
</li>