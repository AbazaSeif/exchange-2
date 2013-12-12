<?php
    $color = $data->status ? 'status-in' : 'status-out';
    echo CHtml::openTag('li', array(
       'class' => 'transport-list ' . $color,
    ));
    echo CHtml::openTag('div', array(
        'class' => 'round-div'
    ));
    echo '<span class="tan-bg">
             <b class="r5"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b><b class="r1"></b>
          </span>';
    echo CHtml::link('<h3> Перевозка №'.$data->id. '</h3>', array('site/description', 'id'=>$data->id));
    echo '<div class="round-div-date">'.date('d.m.Y H:i', strtotime($data->date_published)).'</div>';
    echo CHtml::openTag('div', array(
        'class' => 'inner-box'
    ));
    echo CHtml::openTag('div', array(
        'class' => 'transport-content'
    ));
    echo '<div>', 'Статус: ', $data->status ? 'Активна': 'Закрыта', '</div>';
    echo '<div>', 
            '<div>', 
                'Пункт отправки: ', $data->location_from,
            '</div>',
            '<div class="transport-date">',
                'Дата отправки: ', date('d.m.Y H:i', strtotime($data->date_from)), 
            '</div>', 
         '</div>';
    
    echo '<div>', 
            '<div>', 
                'Пункт назначения: ', $data->location_to,
            '</div>',
            '<div class="transport-date">',
                'Дата прибытия: ', date('d.m.Y H:i', strtotime($data->date_to)), 
            '</div>', 
         '</div>';
    
    echo '<b class="r1"></b><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r5"></b>';
    echo CHtml::closeTag('div');
    echo CHtml::closeTag('div');
    echo CHtml::closeTag('li');  
    
