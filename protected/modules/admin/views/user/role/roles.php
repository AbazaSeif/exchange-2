<h1>Роли</h1>
<div class="total">
    <div class="left">
    <div class="create-button">
    <?php
        echo CHtml::ajaxLink('Создать', '/admin/user/createrole/', array('update'=>'.right'), array('class'=>'btn-green btn'));
    ?>   
    </div>
    <?php
    $this->widget('zii.widgets.CListView', array(
        'dataProvider'=>$data,
        'itemView'=>'role/_item', // представление для одной записи
        'ajaxUpdate'=>false, // включаем ajax поведение
        'emptyText'=>'Нет ролей',
        'template'=>'{sorter} {items}',
        'sorterHeader'=>'',
        'itemsTagName'=>'ul',
        'sortableAttributes'=>array('name'),
        'pager'=>array(
            'class'=>'CLinkPager',
            'header'=>false,
        ),
    ));
    ?>
    </div>
    <div class="right">
        <?php
        if ($mess = Yii::app()->user->getFlash('message')){
            echo '<div class="message success">'.$mess.'</div>';
        }
        if ($view){
            echo $view;
        }
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