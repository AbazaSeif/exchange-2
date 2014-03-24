<?php
    $action = '/user/default/editcontact/id/'.$data->id.'/';
?>

<div class="contact">
    <div class="width-60">
        <a class="c-header" href="<?php echo $action; ?>" >
            <?php echo $data->surname . ' ' . $data->name . ' ' .  $data->secondname ?>
        </a>
    </div>
    <div class="width-40">
        <?php echo $data->email ?>
    </div>
</div>