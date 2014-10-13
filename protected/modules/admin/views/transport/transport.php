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
    <div id="tabs">
        <ul>
            <li><a href="#tabs-active">Активные</a></li>
            <li><a href="#tabs-archive">Архивные</a></li>
            <li><a href="#tabs-draft">Черновики</a></li>
            <li><a href="#tabs-del">Удаленные</a></li>
        </ul>
        <div id="tabs-active">
            <?php
                $this->widget('zii.widgets.CListView', array(
                    'dataProvider'=>$dataActive,
                    'itemView'=>'_item', // представление для одной записи
                    'ajaxUpdate'=>false, // отключаем ajax поведение
                    'emptyText'=>'Нет перевозок',
                    'template'=>'{sorter} {items} {pager}',
                    'sorterHeader'=>'',
                    'itemsTagName'=>'ul',
                    'sortableAttributes'=>array('t_id', 'date_close', 'location_from', 'location_to', 'num_rates'=>'Кол-во ставок', 'num_users'=>'Кол-во фирм', 'win' => 'Фирма-победитель', 'price'=>'Лучшая ставка', 'start_rate'=>'Начальная ставка'),
                    'pager'=>array(
                        'class'=>'LinkPager',
                        'header'=>false,
                        'prevPageLabel'=>'<',
                        'nextPageLabel'=>'>',
                        'lastPageLabel'=>'В конец >>',
                        'firstPageLabel'=>'<< В начало',
                        'maxButtonCount' => '5'
                    ),
                ));
            ?>
        </div>
        <div id="tabs-archive">
        <?php
        $this->widget('zii.widgets.CListView', array(
            'dataProvider'=>$dataArchive,
            'itemView'=>'_item', // представление для одной записи
            'ajaxUpdate'=>false, // отключаем ajax поведение
            'emptyText'=>'Нет перевозок',
            'template'=>'{sorter} {items} {pager}',
            'sorterHeader'=>'',
            'itemsTagName'=>'ul',
            'sortableAttributes'=>array('t_id', 'date_close', 'location_from', 'location_to', 'num_rates'=>'Кол-во ставок', 'num_users'=>'Кол-во фирм', 'win' => 'Фирма-победитель', 'price'=>'Лучшая ставка', 'start_rate'=>'Начальная ставка'),
            'pager'=>array(
                'class'=>'LinkPager',
                'header'=>false,
                'prevPageLabel'=>'<',
                'nextPageLabel'=>'>',
                'lastPageLabel'=>'В конец >>',
                'firstPageLabel'=>'<< В начало',
                'maxButtonCount' => '5'
            ),
        ));
        ?>
        </div>
        <div id="tabs-draft">
        <?php
        $this->widget('zii.widgets.CListView', array(
            'dataProvider'=>$dataDraft,
            'itemView'=>'_item', // представление для одной записи
            'ajaxUpdate'=>false, // отключаем ajax поведение
            'emptyText'=>'Нет перевозок',
            'template'=>'{sorter} {items} {pager}',
            'sorterHeader'=>'',
            'itemsTagName'=>'ul',
            'sortableAttributes'=>array('t_id', 'date_close', 'location_from', 'location_to', 'num_rates'=>'Кол-во ставок', 'num_users'=>'Кол-во фирм', 'win' => 'Фирма-победитель', 'price'=>'Лучшая ставка', 'start_rate'=>'Начальная ставка'),
            'pager'=>array(
                'class'=>'LinkPager',
                'header'=>false,
                'prevPageLabel'=>'<',
                'nextPageLabel'=>'>',
                'lastPageLabel'=>'В конец >>',
                'firstPageLabel'=>'<< В начало',
                'maxButtonCount' => '5'
            ),
        ));
        ?>
        </div>
        <div id="tabs-del">
        <?php
        $this->widget('zii.widgets.CListView', array(
            'dataProvider'=>$dataDel,
            'itemView'=>'_itemdeleted', // представление для одной записи
            'ajaxUpdate'=>false, // отключаем ajax поведение
            'emptyText'=>'Нет перевозок',
            'template'=>'{sorter} {items} {pager}',
            'sorterHeader'=>'',
            'itemsTagName'=>'ul',
            'sortableAttributes'=>array('t_id', 'date_close', 'location_from', 'location_to', 'del_date'=>'Дата удаления', 'del_reason'=>'Причина удаления и автор'),
            'pager'=>array(
                'class'=>'LinkPager',
                'header'=>false,
                'prevPageLabel'=>'<',
                'nextPageLabel'=>'>',
                'lastPageLabel'=>'В конец >>',
                'firstPageLabel'=>'<< В начало',
                'maxButtonCount' => '5'
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