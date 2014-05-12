<?php
    $action = '/admin/changes/showchanges/id/'.$data->id; 
?>

<div class="a-user">
    <div class="width-15">
        <div class="width-100">
            <a class="t-header" href="<?php echo $action; ?>" >
                <?php echo $data->surname ?>
            </a>
        </div>
    </div>
    <div class="width-15">
        <div class="width-100">
            <a class="t-header" href="<?php echo $action; ?>" >
                <?php echo $data->name ?>
            </a>
        </div>
    </div>
    <div class="width-15">
        <div class="width-100">
            <a class="t-header" href="<?php echo $action; ?>" >
                <?php echo $data->secondname ?>
            </a>
        </div>
    </div>
</div>