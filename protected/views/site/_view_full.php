<?php
//var_dump($rates);
    $color = $data->status ? 'status-in' : 'status-out';
    echo CHtml::openTag('li', array(
       'class' => 'transport-list ' . $color,
    ));

	$now = date('Y m d H:i:s', strtotime('now'));
	$end = date('Y m d H:i:s', strtotime($data->date_to));
		
	echo CHtml::link('<h3> Перевозка "' . $data->location_from . '-' . $data->location_to . '"</h3>', array('site/description/', 'id'=>$data->id));
	echo '<div>', 
            '<div>',
                'До закрытия: ', '<span id="counter-' . $data->id. '">', '</span>', 
            '</div>',
         '</div>'
    ;
	
	echo '<div>', 
            '<div>',
                'Текущая ставка: ',  (!empty($rates[$data->id]))? $rates[$data->id]:$data->start_rate,
            '</div>',
         '</div>'
    ;
	
	echo '<div>', 
            '<div>',
                'ID: ', $data->id,
            '</div>',
         '</div>'
    ;
    echo CHtml::closeTag('li');  
?>
<script>
	var myClassObject = new Timer();
    myClassObject.init(<?php echo '"' . $now . '"' ?>, <?php echo '"' . $end . '"' ?>, 'counter-' + <?php echo $data->id ?>);
</script>
