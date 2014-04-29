<?php
    $action = '/admin/contact/editcontact/id/'.$data->id.'/';
    $status = 'Активный';
    if($data->status == 0) $status = 'Не подтвержден';
    else if($data->status == 2) $status = 'Предупрежден';
    else if($data->status == 3) $status = 'Временно заблокирован';
    else if($data->status == 4) $status = 'Заблокирован';
    
?>

<div class="c-user">
    <div class="width-45">
        <div class="width-100">
            <?php if(Yii::app()->user->checkAccess('trEditUserContact')): ?>
            <a class="t-header" href="<?php echo $action; ?>" >
                <?php echo '"' . $data->company . '"'?>
            </a>
            <?php else:  ?>
            <span class="t-header">
                <?php echo '"' . $data->company . '"'?>
            </span>
            <?php endif; ?>
        </div>
    </div>
    <div class="width-15">
        <div class="width-100 u-status">
            <?php echo $status ?>
        </div>
    </div>
    <div class="width-20">
        <div class="width-100">
            <?php echo $data->email ?>
        </div>
    </div>
    <div class="block-images">
        <div class="width-100">
            <?php if($data->status == User::USER_ACTIVE || $data->status == User::USER_WARNING): ?>
            <img status="<?php echo $data->status?>" userId="<?php echo $data->id ?>" src="/images/ico-no-blocked.png">
            <?php else: ?>
            <img status="<?php echo $data->status?>" userId="<?php echo $data->id ?>" src="/images/ico-blocked.png">
            <?php endif; ?>
        </div>
    </div>
</div>