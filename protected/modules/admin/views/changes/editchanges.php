<?php
    if(count($data->getData())){
        $controller = $this;
        $this->widget('zii.widgets.grid.CGridView', array(
            'dataProvider'=>$data,
            'id' => 'grid-changes',
            'columns'=>array(
                'date' => array(
                    'name' => 'date',
                    'value' => 'date("d.m.Y H:i", strtotime($data->date))',
                ),
                'description',
            ),
        )); 
    } else {
        echo '<h3>История пуста</h3>';
    }