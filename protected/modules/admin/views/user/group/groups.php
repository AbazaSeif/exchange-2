<h1>Группы</h1>
<div class="total">
    <div class="left">
    <div class="create-button">
     <?
     echo CHtml::ajaxLink('Создать', '/admin/user/creategroup/', array('update'=>'.right'), array('class'=>'btn-green btn'));
     ?>   
    </div>
    <?php
    $this->widget('zii.widgets.CListView', array(
        'dataProvider'=>$data,
        'itemView'=>'group/_item', // представление для одной записи
        'ajaxUpdate'=>true, // отключаем ajax поведение
        'emptyText'=>'Нет групп',
        'template'=>'{sorter} {items}',
        'sorterHeader'=>'',
        'itemsTagName'=>'ul',
        'sortableAttributes'=>array('id','name'),
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
        ?>
    </div>
</div>
<script>
    $(document).ready(function(){
        var start = new AjaxContentLoader();
        start.init('.left', '.ajax', '.right' ,false);
    });
</script>