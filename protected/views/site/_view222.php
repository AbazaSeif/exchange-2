<?php
    $color = $data->status ? 'status-in' : 'status-out';
    echo CHtml::openTag('li', array(
       'class' => 'transport-list ' . $color,
    ));

	$now  = new DateTime('now');
    $date = new DateTime($data->date_to);
	$diff = date_diff($now, $date);
	
	$minutes = $this->addFormat($diff->format('%i'));
	$seconds = $this->addFormat($diff->format('%s'));
	$final1  = $diff->format('%y-%m-%d %H:'.$minutes.':'.$seconds);
	$final   = $diff->format('%y год(а) %m месяц(ев) %d дней %H:'.$minutes.':'.$seconds);
	
	echo '<h3> Перевозка "' . $data->location_from . '-' . $data->location_to . '"</h3>';
	
	echo '<div>', 
            '<div>',
                'Осталось: ', $final, ' = ', strtotime($final1), 
			'</div>',
         '</div>'
    ;
    echo CHtml::closeTag('li');  
    
