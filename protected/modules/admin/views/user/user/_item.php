<?php
    $action = '/admin/user/edituser/id/'.$data->id.'/';
    $status = 'Активный';
    if($data->status == 0) $status = 'Не подтвержден';
    else if($data->status == 2) $status = 'Предупрежден';
    else if($data->status == 3) $status = 'Временно заблокирован';
    else if($data->status == 4) $status = 'Заблокирован';
    
?>

<div class="a-user">
    <div class="width-45">
        <div class="width-100">
            <a class="t-header" href="<?php echo $action; ?>" >
                <?php echo '"' . $data->company . '"'?>
            </a>
        </div>
    </div>
    <div class="width-15 u-inn">
        <div class="width-100">
            111111111111
        </div>
    </div>
    <div class="width-15">
        <div class="width-100 u-status">
            <?php echo $status ?>
        </div>
    </div>
    <div class="width-20">
        <div class="width-100">
            Причина блокировки
        </div>
    </div>
</div>