<h1>Перевозки</h1>
<div class="create-transport">
    <?php
        echo CHtml::link('Создать перевозку', '/admin/transport/createtransport/', array('class' => 'btn-admin btn'));
        /*if ($mess = Yii::app()->user->getFlash('message')){
            echo '<div class="trMessage success">'.$mess.'</div>';
        }*/
    ?>
</div>
<div style="clear: both"></div>
<div class="right">
    <?php 
        if ($mess = Yii::app()->user->getFlash('message')){
            echo '<div class="trDelMessage success">'.$mess.'</div>';
        }
    ?>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Активные</a></li>
            <li><a href="#tabs-2">Архивные</a></li>
        </ul>
        <div id="tabs-1">
            <?php
                $this->widget('zii.widgets.CListView', array(
                    'dataProvider'=>$dataActive,
                    'itemView'=>'_item', // представление для одной записи
                    'ajaxUpdate'=>false, // отключаем ajax поведение
                    'emptyText'=>'Нет перевозок',
                    'template'=>'{sorter} {items} {pager}',
                    'sorterHeader'=>'',
                    'itemsTagName'=>'ul',
                    'sortableAttributes'=>array('t_id', 'date_close', 'location_from', 'location_to', 'win' => 'Фирма-победитель', 'price'=>'Лучшая ставка'),
                    'pager'=>array(
                        'class'=>'CLinkPager',
                        'header'=>false,
                        'prevPageLabel'=>'<',
                        'nextPageLabel'=>'>',
                        'lastPageLabel'=>'>>',
                        'firstPageLabel'=>'<<'
                    ),
                ));
            ?>
        </div>
        <div id="tabs-2">
        <?php
        $this->widget('zii.widgets.CListView', array(
            'dataProvider'=>$dataArchive,
            'itemView'=>'_item', // представление для одной записи
            'ajaxUpdate'=>false, // отключаем ajax поведение
            'emptyText'=>'Нет перевозок',
            'template'=>'{sorter} {items} {pager}',
            'sorterHeader'=>'',
            'itemsTagName'=>'ul',
            'sortableAttributes'=>array('t_id', 'date_close', 'location_from', 'location_to', 'win' => 'Фирма-победитель', 'price'=>'Лучшая ставка'),
            'pager'=>array(
                'class'=>'CLinkPager',
                'header'=>false,
                'prevPageLabel'=>'<',
                'nextPageLabel'=>'>',
                'lastPageLabel'=>'>>',
                'firstPageLabel'=>'<<'
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
    });
</script>