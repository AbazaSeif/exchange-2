<?php
    $this->widget('zii.widgets.CListView', array(
        'dataProvider' => $data,
		//'rates'        => $rates,
        'itemView'     => '_view',
        'ajaxUpdate'   => false,
        'emptyText'    => 'Нет перевозок',
        'itemsTagName' => 'ul',
        'summaryText'  => 'Показано {start}&mdash;{end} из {count}',
        'template'     => '<div class="mainPagerContainer"><div class="sorting">{summary}{sorter}</div></div>{items}{pager}',
        'sortableAttributes'=>array(
            /*'status' => 'По статусу',*/
            'date_published' => 'По дате публикации'
        ),
        'pager'        => array(
            'class'  => 'LinkPager',
            'header' => false,
            'firstPageLabel' => 'В начало',
            'prevPageLabel'  => 'Назад',
            'nextPageLabel'  => 'Вперёд',
            'lastPageLabel'  => 'В конец',
        )
    )); 


