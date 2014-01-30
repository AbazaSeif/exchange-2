<h1>Перевозки</h1>
<div class="total">
    <div class="left">
    <div class="create-button">
     <?php
     echo CHtml::ajaxLink('Создать', '/admin/transport/createtransport/', array('update'=>'.right'), array('class'=>'btn-green btn'));
     ?>   
    </div>
    <?php
    $this->widget('zii.widgets.CListView', array(
        'dataProvider'=>$data,
        'itemView'=>'_item', // представление для одной записи
        'ajaxUpdate'=>false, // отключаем ajax поведение
        'emptyText'=>'Нет перевозок',
        'template'=>'{sorter} {items} {pager}',
        'sorterHeader'=>'',
        'itemsTagName'=>'ul',
        //'sortableAttributes'=>array('group_id','surname'),
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
<?php if($data->pagination->pageCount!=0) { 
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
        var start = new AjaxContentLoader();
        start.init('.left', '.ajax', '.right' ,false);
    });
</script>