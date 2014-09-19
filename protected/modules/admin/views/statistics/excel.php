<?php if($model!=null):?>
<table border="1">
    <tr><?php echo $dateFrom.' - '.$dateTo?></tr>
    <tr>
        <th style="background-color: #cccccc;">Id 1C</th>
        <th style="background-color: #cccccc;">Время закрытия заявки</th>
        <th style="background-color: #cccccc;">Место загрузки</th>
        <th style="background-color: #cccccc;">Место разгрузки</th>
        <th style="background-color: #cccccc;">Кол-во ставок</th>
        <th style="background-color: #cccccc;">Кол-во фирм</th>
        <th style="background-color: #cccccc;">Фирма победитель</th>
        <th style="background-color: #cccccc;">Лучшая ставка</th>
        <th style="background-color: #cccccc;">Начальная ставка</th>
        <th style="background-color: #cccccc;">Валюта</th>
    </tr>
    <?php foreach($model as $transport):
        $currency = ' €';
        $showRate = $withNds = '';
        if (!$transport->currency) {
           $currency = ' руб.';
        } else if($transport->currency == 1) {
           $currency = ' $';
        } 
        $rateCount = Rate::model()->countByAttributes(array(
            'transport_id'=> $transport->id
        ));
        $users = Yii::app()->db->createCommand(array(
            'select'   => 'user_id',
            'distinct' => 'true',
            'from'     => 'rate',
            'where'    => 'transport_id = ' . $transport->id,
        ))->queryAll();
        $userCount = count($users);
        
        $rate = Rate::model()->findByPk($transport->rate_id);
        $ferryman = User::model()->findByPk($rate->user_id);
        $ferrymanField = UserField::model()->findByAttributes(array('user_id'=>$rate->user_id));
        if ($rate->price) {
            $showRate = floor($rate->price);
            if($ferrymanField->with_nds && $transport->type == Transport::RUS_TRANSPORT) {
                $price = ceil($rate->price + $rate->price * Yii::app()->params['nds']);
                if($price%10 != 0) $price -= $price%10;
                $withNds .= ' (c НДС: '. $price.')';
            }
        }
        if($withNds) $showRate = $showRate.' '.$withNds;
    ?>
    <tr>
        <td><?php echo $transport->t_id; ?></td>
        <td><?php echo date('Y-m-d H:i', strtotime($transport->date_close)); ?></td>
        <td><?php echo $transport->location_from; ?></td>
        <td><?php echo $transport->location_to; ?></td>
        <td><?php echo $rateCount; ?></td>
        <td><?php echo $userCount; ?></td>
        <td><?php echo ($ferryman->company) ? $ferryman->company : 'Нет ставок' ?></td>
        <td><?php echo $showRate;  ?></td>
        <td><?php echo $transport->start_rate; ?></td>
        <td><?php echo $currency; ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>
<?php //$this->redirect('/admin/statistics/'); ?>



