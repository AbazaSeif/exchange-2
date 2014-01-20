<?php
    $color = $data->status ? 'status-in' : 'status-out';
	$lastRate = $this->getPrice($data->rate_id);
	
    echo CHtml::openTag('li', array(
       'class' => 'transport-list ' . $color,
    ));

	$now = date('Y m d H:i:s', strtotime('now'));
	$end = date('Y m d H:i:s', strtotime($data->date_to . ' -' . Yii::app()->params['hoursBefore'] . ' hours'));

	echo CHtml::link('<h3> Перевозка "' . $data->location_from . '-' . $data->location_to . '"</h3>', array('/user/transport/description/', 'id'=>$data->id));
	
	if(!Yii::app()->user->isGuest) {
		echo '<div>', 
				'<div>',
					'Текущая ставка: ',  (!empty($lastRate))? $lastRate : $data->start_rate,
				'</div>',
			 '</div>'
		;
	}
	
	echo '<div>', 
            '<div>',
                '<span id="counter-' . $data->id. '">', '</span>', 
            '</div>',
         '</div>'
    ;
	
    echo CHtml::closeTag('li');  
?>

<script>
	var myClassObject = new Timer();
    myClassObject.init(<?php echo '"' . $now . '"' ?>, <?php echo '"' . $end . '"' ?>, 'counter-' + <?php echo $data->id ?>);
</script>
