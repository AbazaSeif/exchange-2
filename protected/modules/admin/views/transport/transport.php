<?php $alertMessage = Yii::app()->user->getFlash('message');?>
<script>
    $(function() {
        <?php if ($alertMessage) :?>
             alertify.success('<?php echo $alertMessage; ?>');
        <?php endif; ?>
    });
</script>
<h1>Перевозки</h1>
<div class="create-transport">
    <?php
        echo CHtml::link('Создать перевозку', '/admin/transport/createtransport/', array('class' => 'btn-admin btn-create'));

        echo CHtml::dropDownList('type-transport', $type, array(
            0=>'Международные перевозки',
            1=>'Региональные перевозки',
            2=>'Все перевозки',
        )); 
    ?>
</div>
<div style="clear: both"></div>
<div class="right">
    <?php 
        if ($mess = Yii::app()->user->getFlash('message')){
            echo '<div class="uDelMessage success">'.$mess.'</div>';
        }
    ?>
    <div id="tabs" class="grid-wrapper">
        <ul>
            <li><a href="#tabs-active">Активные</a></li>
            <li><a href="#tabs-archive">Архивные</a></li>
            <li><a href="#tabs-draft">Черновики</a></li>
            <li><a href="#tabs-del">Удаленные</a></li>
        </ul>
        <div id="tabs-active">
            <?php
                $this->widget('zii.widgets.grid.CGridView', array(
                    'filter'=>$dataActive,
                    'dataProvider'=>$dataActive->search(),
                    'template'=>'{items}{pager}{summary}',
                    'summaryText'=>'Элементы {start}—{end} из {count}.',
                    'pager' => array(
                        'class' => 'LinkPager',
                        //'header' => false,
                    ),
                    'columns' => array(
                        't_id',
                        array(
                            'name'=>'date_close',
                            'value'=>'date("Y-m-d H:i", strtotime($data->date_close))',
                        ),               
                        array (
                            'name'=>'location_from',
                            'type'=>'raw',
                            'value'=>'CHtml::link($data->location_from, array("edittransport","id"=>$data->id))',
                        ),                  
                        array (
                            'name'=>'location_to',
                            'type'=>'raw',
                            'value'=>'CHtml::link($data->location_to, array("edittransport","id"=>$data->id))',
                        ), 
                        array (
                            'header'=>'Кол-во ставок',
                            'filter'=>false,
                            'type'=>'raw',
                            'value' => function($data){
                                return Rate::model()->countByAttributes(array(
                                    'transport_id'=> $data->id
                                ));
                            }
                        ), 
                        array (
                            'header'=>'Кол-во фирм',
                            'filter'=>false,
                            'type'=>'raw',
                            'value' => function($data){
                                $users = Yii::app()->db->createCommand(array(
                                    'select'   => 'user_id',
                                    'distinct' => 'true',
                                    'from'     => 'rate',
                                    'where'    => 'transport_id = ' . $data->id,
                                ))->queryAll();
                                
                                return count($users);
                            }
                        ), 
                        array (
                            'header'=>'Фирма-победитель',
                            'filter'=>false,
                            'type'=>'raw',
                            'value' => function($data){
                                $label = 'Нет ставок';
                                if(!empty($data->rate_id)) {
                                    $rate = Rate::model()->findByPk($data->rate_id);
                                    $ferryman = User::model()->findByPk($rate->user_id);
                                    if($ferryman) $label = $ferryman->company;
                                }
                                
                                return $label;
                            }
                        ), 
                        array (
                            'header'=>'Лучшая ставка',
                            'filter'=>false,
                            'type'=>'raw',
                            'value' => function($data) {
                                $label = '';
                                $rate = Rate::model()->findByPk($data->rate_id);
                                if ($rate) {
                                    $ferrymanField = UserField::model()->findByAttributes(array('user_id'=>$rate->user_id));
                                    
                                    $currency = ' €';
                                    if (!$data->currency) {
                                       $currency = ' руб.';
                                    } else if($data->currency == 1) {
                                       $currency = ' $';
                                    }
                                    
                                    $label = number_format(floor($rate->price), 0, '.', ' ') . $currency;
                                    if($ferrymanField->with_nds && $data->type == Transport::RUS_TRANSPORT) {
                                        $price = ceil($rate->price + $rate->price * Yii::app()->params['nds']);
                                        if($price%10 != 0) $price -= $price%10;
                                        $label .= '<br> (c НДС: '. number_format($price, 0, '.', ' ') . ' '. $currency . ')';
                                    }
                                }
                                
                                
                                return $label;
                            }
                        ), 
                        array (
                            'header'=>'Начальная ставка',
                            'name'=>'start_rate',
                            'type'=>'raw',
                            'value' => function($data) {
                                $currency = ' €';
                                if (!$data->currency) {
                                   $currency = ' руб.';
                                } else if($data->currency == 1) {
                                   $currency = ' $';
                                }
                                return number_format($data->start_rate, 0, '.', ' ').$currency;
                            }
                        )
                    )
                ));
            ?>
        </div>
        <div id="tabs-archive">
        <?php
            $this->widget('zii.widgets.grid.CGridView', array(
                'filter'=>$dataArchive,
                'dataProvider'=>$dataArchive->search(),
                'template'=>'{items}{pager}{summary}',
                'summaryText'=>'Элементы {start}—{end} из {count}.',
                'pager' => array(
                    'class' => 'LinkPager',
                    //'header' => false,
                ),
                'columns' => array(
                    't_id',
                    array(
                        'name'=>'date_close',
                        'value'=>'date("Y-m-d H:i", strtotime($data->date_close))',
                    ),               
                    array (
                        'name'=>'location_from',
                        'type'=>'raw',
                        'value'=>'CHtml::link($data->location_from, array("edittransport","id"=>$data->id))',
                    ),                  
                    array (
                        'name'=>'location_to',
                        'type'=>'raw',
                        'value'=>'CHtml::link($data->location_to, array("edittransport","id"=>$data->id))',
                    ), 
                    array (
                        'header'=>'Кол-во ставок',
                        'filter'=>false,
                        'type'=>'raw',
                        'value' => function($data){
                            return Rate::model()->countByAttributes(array(
                                'transport_id'=> $data->id
                            ));
                        }
                    ), 
                    array (
                        'header'=>'Кол-во фирм',
                        'filter'=>false,
                        'type'=>'raw',
                        'value' => function($data){
                            $users = Yii::app()->db->createCommand(array(
                                'select'   => 'user_id',
                                'distinct' => 'true',
                                'from'     => 'rate',
                                'where'    => 'transport_id = ' . $data->id,
                            ))->queryAll();

                            return count($users);
                        }
                    ), 
                    array (
                        'header'=>'Фирма-победитель',
                        'filter'=>false,
                        'type'=>'raw',
                        'value' => function($data){
                            $label = 'Нет ставок';
                            if(!empty($data->rate_id)) {
                                $rate = Rate::model()->findByPk($data->rate_id);
                                $ferryman = User::model()->findByPk($rate->user_id);
                                if($ferryman) $label = $ferryman->company;
                            }

                            return $label;
                        }
                    ), 
                    array (
                        'header'=>'Лучшая ставка',
                        'filter'=>false,
                        'type'=>'raw',
                        'value' => function($data) {
                            $label = '';
                            $rate = Rate::model()->findByPk($data->rate_id);
                            if ($rate) {
                                $ferrymanField = UserField::model()->findByAttributes(array('user_id'=>$rate->user_id));

                                $currency = ' €';
                                if (!$data->currency) {
                                   $currency = ' руб.';
                                } else if($data->currency == 1) {
                                   $currency = ' $';
                                }

                                $label = number_format(floor($rate->price), 0, '.', ' ') . $currency;
                                if($ferrymanField->with_nds && $data->type == Transport::RUS_TRANSPORT) {
                                    $price = ceil($rate->price + $rate->price * Yii::app()->params['nds']);
                                    if($price%10 != 0) $price -= $price%10;
                                    $label .= '<br> (c НДС: '. number_format($price, 0, '.', ' ') . ' '. $currency . ')';
                                }
                            }


                            return $label;
                        }
                    ), 
                    array (
                        'header'=>'Начальная ставка',
                        'name'=>'start_rate',
                        'type'=>'raw',
                        'value' => function($data) {
                            $currency = ' €';
                            if (!$data->currency) {
                               $currency = ' руб.';
                            } else if($data->currency == 1) {
                               $currency = ' $';
                            }
                            return number_format($data->start_rate, 0, '.', ' ').$currency;
                        }
                    )
                )
            ));
        ?>
        </div>
        <div id="tabs-draft">
        <?php
//        $this->widget('zii.widgets.CListView', array(
//            'dataProvider'=>$dataDraft,
//            'itemView'=>'_item', // представление для одной записи
//            'ajaxUpdate'=>false, // отключаем ajax поведение
//            'emptyText'=>'Нет перевозок',
//            'template'=>'{sorter} {items} {pager}',
//            'sorterHeader'=>'',
//            'itemsTagName'=>'ul',
//            'sortableAttributes'=>array('t_id', 'date_close', 'location_from', 'location_to', 'num_rates'=>'Кол-во ставок', 'num_users'=>'Кол-во фирм', 'win' => 'Фирма-победитель', 'price'=>'Лучшая ставка', 'start_rate'=>'Начальная ставка'),
//            'pager'=>array(
//                'class'=>'LinkPager',
//                'header'=>false,
//            ),
//        ));
            $this->widget('zii.widgets.grid.CGridView', array(
                'filter'=>$dataDraft,
                'dataProvider'=>$dataDraft->search(),
                'template'=>'{items}{pager}{summary}',
                'summaryText'=>'Элементы {start}—{end} из {count}.',
                'pager' => array(
                    'class' => 'LinkPager',
                    //'header' => false,
                ),
                'columns' => array(
                    't_id',
                    array(
                        'name'=>'date_close',
                        'value'=>'date("Y-m-d H:i", strtotime($data->date_close))',
                    ),               
                    array (
                        'name'=>'location_from',
                        'type'=>'raw',
                        'value'=>'CHtml::link($data->location_from, array("edittransport","id"=>$data->id))',
                    ),                  
                    array (
                        'name'=>'location_to',
                        'type'=>'raw',
                        'value'=>'CHtml::link($data->location_to, array("edittransport","id"=>$data->id))',
                    ), 
                    array (
                        'header'=>'Кол-во ставок',
                        'filter'=>false,
                        'type'=>'raw',
                        'value' => function($data){
                            return Rate::model()->countByAttributes(array(
                                'transport_id'=> $data->id
                            ));
                        }
                    ), 
                    array (
                        'header'=>'Кол-во фирм',
                        'filter'=>false,
                        'type'=>'raw',
                        'value' => function($data){
                            $users = Yii::app()->db->createCommand(array(
                                'select'   => 'user_id',
                                'distinct' => 'true',
                                'from'     => 'rate',
                                'where'    => 'transport_id = ' . $data->id,
                            ))->queryAll();

                            return count($users);
                        }
                    ), 
                    array (
                        'header'=>'Фирма-победитель',
                        'filter'=>false,
                        'type'=>'raw',
                        'value' => function($data){
                            $label = 'Нет ставок';
                            if(!empty($data->rate_id)) {
                                $rate = Rate::model()->findByPk($data->rate_id);
                                $ferryman = User::model()->findByPk($rate->user_id);
                                if($ferryman) $label = $ferryman->company;
                            }

                            return $label;
                        }
                    ), 
                    array (
                        'header'=>'Лучшая ставка',
                        'filter'=>false,
                        'type'=>'raw',
                        'value' => function($data) {
                            $label = '';
                            $rate = Rate::model()->findByPk($data->rate_id);
                            if ($rate) {
                                $ferrymanField = UserField::model()->findByAttributes(array('user_id'=>$rate->user_id));

                                $currency = ' €';
                                if (!$data->currency) {
                                   $currency = ' руб.';
                                } else if($data->currency == 1) {
                                   $currency = ' $';
                                }

                                $label = number_format(floor($rate->price), 0, '.', ' ') . $currency;
                                if($ferrymanField->with_nds && $data->type == Transport::RUS_TRANSPORT) {
                                    $price = ceil($rate->price + $rate->price * Yii::app()->params['nds']);
                                    if($price%10 != 0) $price -= $price%10;
                                    $label .= '<br> (c НДС: '. number_format($price, 0, '.', ' ') . ' '. $currency . ')';
                                }
                            }


                            return $label;
                        }
                    ), 
                    array (
                        'header'=>'Начальная ставка',
                        'name'=>'start_rate',
                        'type'=>'raw',
                        'value' => function($data) {
                            $currency = ' €';
                            if (!$data->currency) {
                               $currency = ' руб.';
                            } else if($data->currency == 1) {
                               $currency = ' $';
                            }
                            return number_format($data->start_rate, 0, '.', ' ').$currency;
                        }
                    )
                )
            ));
        ?>
        </div>
        <div id="tabs-del">
        <?php
        $this->widget('zii.widgets.grid.CGridView', array(
            'filter'=>$dataDel,
            'dataProvider'=>$delProvider,
            'template'=>'{items}{pager}{summary}',
            'summaryText'=>'Элементы {start}—{end} из {count}.',
            'pager' => array(
                'class' => 'LinkPager',
                //'header' => false,
            ),
            'columns' => array(
                array(
                    'name'=>'del_date',
                    'value'=>'date("Y-m-d H:i", strtotime($data->del_date))',
                ),
                'del_reason',
                't_id',
                array (
                    'name'=>'location_from',
                    'type'=>'raw',
                    'value'=>'CHtml::link($data->location_from, array("edittransport","id"=>$data->id))',
                ),                  
                array (
                    'name'=>'location_to',
                    'type'=>'raw',
                    'value'=>'CHtml::link($data->location_to, array("edittransport","id"=>$data->id))',
                ), 
                array(
                    'name'=>'date_close',
                    'header'=>'Плановое закрытие',
                    'value'=>'date("Y-m-d H:i", strtotime($data->date_close))',
                )
            ),
        ));
        ?>
        </div>
    </div>
</div>
<script>
    $(function() {
        var activeTab = parseInt(sessionStorage.getItem('transportActive'));
        if(isNaN(activeTab)) {
            $("#tabs").tabs({active: 0});
        } else $("#tabs").tabs({active: activeTab});
        $( "#tabs").tabs();
        $('li.ui-state-default.ui-corner-top > a').click(function(){
            var active = $("#tabs").tabs("option", "active");
            sessionStorage.setItem('transportActive', active);
        });
        
        var activeType = parseInt(sessionStorage.getItem('transportType'));
        if(!isNaN(activeType)) $('#type-transport').val(activeType);
        $('#type-transport').change(function(){
            sessionStorage.setItem('transportType', this.value);
            document.location.href = "<?php echo Yii::app()->getBaseUrl(true) ?>/admin/transport/index/transportType/" + this.value;
        });
    });
</script>