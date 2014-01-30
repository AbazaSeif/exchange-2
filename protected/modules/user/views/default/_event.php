<?php
   // echo '<pre>';
   // var_dump($data);
   //t-header
   //echo 2222;
?>
<div class="rate <?php echo ((int)$data->status) ? 'rate-new' : ''?>">
    <div class="width-70">
        <?php echo CHtml::link('Перевозка "' . $data->transport->location_from . ' &mdash; ' . $data->transport->location_to . '"', array('/transport/description/', 'id'=>$data->transport_id), array('class'=>'t-header')); ?>
        
        <div class="t-date">
            <span class="t-d-type"><?php echo $this->getEventMessage($data->event_type) ?></span>
        </div>
    </div>
</div>
