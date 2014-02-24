<h1>Сообщения</h1>
<?php
    $this->widget('zii.widgets.CListView', array(
        'dataProvider' => $data,
        'cssFile'      => false,
        'itemView'     => '_event',
        'ajaxUpdate'   => false,
        'emptyText'    => 'Нет cобытий',
        'itemsTagName' => 'div',
        'template'     => '{sorter}{items}{pager}',
        'htmlOptions'  => array('class'=>'events'),
        //'sortableAttributes' => array('date_published','date_to', 'date_from'),
        'sorterHeader' => '',
        'pager'        => array(
            'header'   => false,
            'firstPageLabel' => 'В начало',
            'prevPageLabel'  => 'Назад',
            'nextPageLabel'  => 'Вперёд',
            'lastPageLabel'  => 'В конец',
            'cssFile'        => false
        )
    ));
    
    UserEvent::model()->updateAll(array('status' => 0), 'status = 1');
?>
<script>
    $(document).ready(function() {
        $(".event-new").mouseover(function() {
            $(this).removeClass("event-new");
        });
    });
</script>