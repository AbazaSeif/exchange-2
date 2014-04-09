<?php
    Yii::app()->clientScript->registerCssFile('/css/back/users.css');
    Yii::app()->clientScript->registerScriptFile('/js/back/AjaxContentLoader.js');
    Yii::app()->clientScript->registerScriptFile('/js/back/users.js');
?>
<h1>Компании</h1>
<div class="total">
    <div class="left">
    <div class="create-button">
     <?php
         echo CHtml::ajaxLink('Создать пользователя', '/admin/user/createuser/', array('update'=>'.right'), array('class'=>'btn-admin btn-create'));
     ?>   
    </div>
    <?php $this->widget('zii.widgets.CListView', array(
        'dataProvider'=>$data,
        'itemView'=>'user/_item', // представление для одной записи
        'ajaxUpdate'=>false, // отключаем ajax поведение
        'emptyText'=>'Нет пользователей',
        'template'=>'{sorter} {items} {pager}',
        'sorterHeader'=>'',
        'itemsTagName'=>'ul',
        'sortableAttributes'=>array('company'),
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
        } else if ($mess = Yii::app()->user->getFlash('error')){
            echo '<div class="message error">'.$mess.'</div>';
        }
        if ($view){
            echo $view;
        }else{ ?>
        <div class="faq">
            <h2>Как изменить пароль?</h2>
            <p>Для того, чтобы изменить пароль, откройте страницу редактирования и введите новый пароль в одноименное поле. Если поле оставить пустым, пароль останется прежним.</p>
            <br>
            
        </div>
        <?php } ?>
    </div>
</div>
<style>
.left .list-view .pager ul.yiiPager li.page{
    width: <?php //echo 100/$data->pagination->pageCount; ?>%;
}
</style>