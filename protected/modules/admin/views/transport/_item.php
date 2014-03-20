<?php
    $action = '/admin/transport/edittransport/id/'.$data->id.'/';
    $rate = Rate::model()->findByPk($data->rate_id);
    $ferryman = User::model()->findByPk($rate->user_id);
    //echo '<pre>';
    //var_dump($data);
?>
<div class="transport">
    <div class="width-10">
        <?php echo $data->t_id ?>
    </div>
    <div class="width-15">
        <?php echo date('d.m.Y H:i', strtotime($data->date_from)) ?>
    </div>
    <div class="width-40">
        <div class="width-100">
            <a class="t-header" href="<?php echo $action; ?>" >
                <?php echo '"' . $data->location_from . ' &mdash; ' . $data->location_to . '"'?>
            </a>
        </div>
        <div class="width-100">
            <div class="t-points"><span><?php echo $data->location_from . $allPoints . ' -> ' . $data->location_to ?></span></div>
        </div>
    </div>
    <div class="width-20">
        <?php echo $ferryman->company ?>
    </div>
    <div class="width-10">
        <?php echo $rate->price ?>
    </div>
</div>