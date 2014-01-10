<div class="office-menu">
	<ul class="menuMainTop">
		<li>
			<a href="/site/officeuser/">
				<span>Ставки</span>
			</a>
		</li>
		<li>
			<a href="/site/officeuseroption/">
				<span>Настройки</span>
			</a>
		</li>
	</ul>
</div>
<h2>User</h2>

<?php
    $this->widget('zii.widgets.CListView', array(
        'dataProvider' => $data,
        'itemView'     => '_view',
        'ajaxUpdate'   => false, 
        'emptyText'    => 'Нет перевозок',
        'itemsTagName' => 'ul',
        'summaryText'  => 'Показано {start}&mdash;{end} из {count}',
        'template'     => '<div class="mainPagerContainer"><div class="sorting">{summary}{sorter}</div></div>{items}{pager}',
        'sortableAttributes'=>array(
            'date_published' => 'По дате публикации'
        ),
        'pager'      => array(
            'class'  => 'LinkPager',
            'header' => false,
            'firstPageLabel' => 'В начало',
            'prevPageLabel'  => 'Назад',
            'nextPageLabel'  => 'Вперёд',
            'lastPageLabel'  => 'В конец',
        )
    )); 


