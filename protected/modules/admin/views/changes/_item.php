<?php
    if(!isset($data->user_id)) {
        $action = '/admin/changes/showchanges/id/'.$data->id;    
        $dateLastEdit = Yii::app()->db->createCommand()
            ->select('max(date)')
            ->from('changes')
            ->where('user_id = '.$data->id)
            ->queryScalar()
        ;
        $name = $data->name;
        $surname = $data->surname;
        $secondname = $data->secondname;
    } else {
        $action = '/admin/changes/showchanges/id/'.$data->user_id;  
        $dateLastEdit = Yii::app()->db->createCommand()
            ->select('max(date)')
            ->from('changes')
            ->where('user_id = '.$data->user_id)
            ->queryScalar()
        ;
        $user = Yii::app()->db_auth->createCommand()
            ->from('user')
            ->where('id = '.$data->user_id)
            ->queryRow()
        ;
        $name = $user[name];
        $surname = $user[surname];
        $secondname = $user[secondname];
    }
?>

<div class="a-user">
    <div class="width-15">
        <div class="width-100">
            <a class="t-header-surname" href="<?php echo $action; ?>" >
                <?php echo $surname ?>
            </a>
        </div>
    </div>
    <div class="width-15">
        <div class="width-100">
            <a class="t-header-name" href="<?php echo $action; ?>" >
                <?php echo $name ?>
            </a>
        </div>
    </div>
    <div class="width-15">
        <div class="width-100">
            <a class="t-header-secondname" href="<?php echo $action; ?>" >
                <?php echo $secondname ?>
            </a>
        </div>
    </div>
    <div class="width-15">
        <div class="width-100 last-edit">
            <?php echo (!empty($dateLastEdit)) ? date('Y.m.d H:i', strtotime($dateLastEdit)) : ''; ?>
        </div>
    </div>
</div>