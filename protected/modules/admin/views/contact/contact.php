<h1>Контактные лица</h1>
<div class="create-user">
    <?php
        echo CHtml::link('Создать контактное лицо', '/admin/contact/createcontact/', array('class' => 'btn-admin btn-create'));
        echo CHtml::dropDownList('type-c-status', $type, array(
            0=>'Не подтвержден',
            1=>'Активный',
            2=>'Предупрежден',
            3=>'Временно заблокирован',
            4=>'Заблокирован',
            5=>'Все',
        ));     
        echo CHtml::label('Статус', 'type-status');
    ?>
</div>
<div style="clear: both"></div>
<div class="right">
    <?php 
        if ($mess = Yii::app()->user->getFlash('message')){
            echo '<div class="cDelMessage success">'.$mess.'</div>';
        }
    ?>
    <div id="contact-wrapper">
        <div class="c-header">
            Список контактных лиц
        </div>
        <div id="c-content">
            <?php $this->widget('zii.widgets.CListView', array(
                'dataProvider'=>$data,
                'itemView'=>'_item', // представление для одной записи
                'ajaxUpdate'=>false, // отключаем ajax поведение
                'emptyText'=>'Нет пользователей',
                'template'=>'{sorter} {items} {pager}',
                'sorterHeader'=>'',
                'itemsTagName'=>'ul',
                'sortableAttributes'=>array('company', 'status', 'email'),
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
</div>

<script>
    $(function() {
        var activeStatus = parseInt(sessionStorage.getItem('contactStatus'));
        if(!isNaN(activeStatus)) $('#type-c-status').val(activeStatus);
        else $('#type-c-status').val(5);
        
        $('#type-c-status').change(function() {
            sessionStorage.setItem('contactStatus', this.value);
            document.location.href = "<?php echo Yii::app()->getBaseUrl(true) ?>/admin/contact/index/status/" + this.value;
        });
    });
</script>