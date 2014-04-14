<h1>Компании</h1>
<div class="create-user">
    <?php
        echo CHtml::link('Создать компанию', '/admin/user/createuser/', array('class' => 'btn-admin btn-create'));
        echo CHtml::dropDownList('type-status', $type, array(
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
            echo '<div class="trDelMessage success">'.$mess.'</div>';
        }
    ?>
    <div id="user-wrapper">
        <div class="u-header">
            Список компаний
        </div>
        <div id="u-content">
            <?php $this->widget('zii.widgets.CListView', array(
                'dataProvider'=>$data,
                'itemView'=>'user/_item', // представление для одной записи
                'ajaxUpdate'=>false, // отключаем ajax поведение
                'emptyText'=>'Нет пользователей',
                'template'=>'{sorter} {items} {pager}',
                'sorterHeader'=>'',
                'itemsTagName'=>'ul',
                'sortableAttributes'=>array('company', 'inn', 'status', 'email'),
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
        var activeStatus = parseInt(sessionStorage.getItem('userStatus'));
        if(!isNaN(activeStatus)) $('#type-status').val(activeStatus);
        else $('#type-status').val(5);
        
        $('#type-status').change(function() {
            sessionStorage.setItem('userStatus', this.value);
            document.location.href = "<?php echo Yii::app()->getBaseUrl(true) ?>/admin/user/index/status/" + this.value;
        });
    });
</script>