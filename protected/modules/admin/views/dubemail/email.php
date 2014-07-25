<h1>Проверка на повтор Email</h1>
<div id="check-dublicate-wrapper">
    <?php $this->widget('zii.widgets.CListView', array(
        'dataProvider'=>$data,
        'itemView'=>'_item', // представление для одной записи
        'ajaxUpdate'=>false, // отключаем ajax поведение
        'emptyText'=>'Нет повторений',
        'template'=>'{summary} {sorter} {items} {pager}',
        'summaryText'=>'Показано {start}-{end} из {count}',
        'sorterHeader'=>'',
        'itemsTagName'=>'ul',
        //'sortableAttributes'=>array('company', 'inn', 'status', 'email'),
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

