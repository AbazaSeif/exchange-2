<h1>История редактирования</h1>
<div class="total">
    <div class="left">
    <div class="create-button">
     <?php
     echo CHtml::ajaxLink('Создать', '/admin/user/createuser/', array('update'=>'.right'), array('class'=>'btn-green btn'));
     ?>   
    </div>
    <?php
//    $group_array = UserGroup::getUserGroupArray();
//    $group_array['group_id'] = 'Все';
//    echo CHtml::radioButtonList('drop_group', '', $group_array,
//    array('onchange'=>"$.fn.yiiListView.update('yw0', {url: '/admin/user/?group_id='+$('#drop_group input:checked').val()})"));
    ?>
    <?php
    $this->widget('zii.widgets.CListView', array(
        'dataProvider'=>$data,
        'itemView'=>'/changes/_item', // представление для одной записи
        'ajaxUpdate'=>false, // отключаем ajax поведение
        'emptyText'=>'Нет пользователей',
        'template'=>'{sorter} {items} {pager}',
        'sorterHeader'=>'',
        'itemsTagName'=>'ul',
        //'sortableAttributes'=>array('group_id','surname','name'),
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
    <div class="right">
        <?php 
        if ($mess = Yii::app()->user->getFlash('message')){
            echo '<div class="message success">'.$mess.'</div>';
        }
        if ($view){
            echo $view;
        }?>
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