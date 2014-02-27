<h1>
    <?php echo $title ?>
</h1>
<?php
    $this->widget('zii.widgets.CListView', array(
        'dataProvider' => $data,
        'cssFile'      => false,
        'itemView'     => '_view',
        'ajaxUpdate'   => false,
        'emptyText'    => 'Нет перевозок',
        'itemsTagName' => 'div',
        'template'     => '{sorter}{items}{pager}',
        'htmlOptions'  => array('class'=>'transports'),
        'sortableAttributes' => array('location_from', 'location_to', 'l', 'date_from'),
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

if($data->pagination->pageCount!=0) { 
    $c = ($data->pagination->pageCount+4);
    ?>
    <style>
    #search-index .pager ul.yiiPager li{
        width: <?php echo 100/($c>14?14:$c); ?>%;
    }
    </style>
<?php } ?>
<script>
$(document).ready(function(){
    $('.t-timer').each(function(){
       if(parseInt($(this).attr('status'))){
           var timer = new Timer();
           timer.init($(this).attr('now'), $(this).attr('end'), $(this).attr('id'), $(this).attr('status'));
       } else {
           $('#' + $(this).attr('id')).html('<span class="t-closed">Перевозка закрыта</span>');
       }
    });
    $('.description').dotdotdot();
    $('.description-50').dotdotdot();
});
</script>


