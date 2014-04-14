<?php
    $action = '/admin/transport/edittransport/id/'.$data->id.'/';
    $rate = Rate::model()->findByPk($data->rate_id);
    $ferryman = User::model()->findByPk($rate->user_id);
    $ferrymanField = UserField::model()->findByAttributes(array('user_id'=>$rate->user_id));
    $rateCount = Rate::model()->countByAttributes(array(
        'transport_id'=> $data->id
    ));
    $users = Yii::app()->db->createCommand(array(
        'select'   => 'user_id',
        'distinct' => 'true',
        'from'     => 'rate',
        'where'    => 'transport_id = ' . $data->id,
    ))->queryAll();
    $userCount = count($users);
    
    $showRate = $withNds = '';
    $currency = ' €';
    
    if (!$data->currency) {
       $currency = ' руб.';
    } else if($data->currency == 1) {
       $currency = ' $';
    }
    
    if ($rate->price) {
        $showRate = floor($rate->price) . $currency;
        if($ferrymanField->with_nds) {
            $price = ceil($rate->price + $rate->price * Yii::app()->params['nds']);
            if($price%10 != 0) $price -= $price%10;
            $withNds .= ' (c НДС: '. $price . ' '. $currency . ')';
        }
    }
?>

<div class="transport">
    <div class="width-10">
        <?php echo $data->t_id ?>
    </div>
    <div class="width-15">
        <?php echo date('d.m.Y H:i', strtotime($data->date_close)) ?>
    </div>
    <div class="width-30">
        <div class="width-100">
            <a class="t-header" href="<?php echo $action; ?>" >
                <?php echo '"' . $data->location_from . ' &mdash; ' . $data->location_to . '"'?>
            </a>
        </div>
        <div class="width-100">
            <div class="t-points"><span><?php echo $data->location_from . $allPoints . ' -> ' . $data->location_to ?></span></div>
        </div>
    </div>
    <div class="width-5">
        <?php echo $rateCount ?>
    </div>
    <div class="width-5">
        <?php echo $userCount ?>
    </div>
    <div class="width-20 t-company">
        <?php echo ($ferryman->company) ? $ferryman->company : 'Нет ставок'?>
    </div>
    <div class="width-15">
        <div class="width-100">
           <?php echo $showRate ?>
        </div>
        <?php if($withNds): ?>
        <div class="width-100">
           <?php echo $withNds ?>
        </div>
        <?php endif; ?>
    </div>
</div>