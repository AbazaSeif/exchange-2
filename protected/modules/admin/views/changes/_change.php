<div class="change">
    <div class="width-15">
        <span><?php echo date('d.m.y H:i', strtotime($data->date)) ?></span>
    </div>
    <div class="width-20">
        <span><?php echo $data->action_name ?></span>
    </div>
    <div class="width-50">
        <span><?php echo $data->description ?></span>
    </div>
</div>