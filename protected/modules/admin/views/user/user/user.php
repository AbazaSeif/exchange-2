<h1>Компании</h1>
<div class="create-user">
    <?php
        echo CHtml::link('Создать компанию', '/admin/user/createuser/', array('class' => 'btn-admin btn-create'));
        $dropDownStatus = User::$userStatus;
        $dropDownStatus[] = 'Все';
        echo CHtml::dropDownList('type-status', $type, $dropDownStatus);     
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
            <input type="text" id="u-search" placeholder="Поиск по названию компании" />
            <?php //echo CHtml::link('Искать', '/admin/user/search/', array('class' => 'btn-admin btn-search'))?>
            <?php echo CHtml::submitButton('Искать',array('class'=>'btn-admin btn-search')); ?>
            <div style="display: block; clear: both;"></div>
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
        
        $('.btn-admin.btn-search').click(function(){
            var input = $('#u-search').val();
            if($.trim(input))document.location.href = "<?php echo Yii::app()->getBaseUrl(true) ?>/admin/user/search/input/" + input;
        });
        
        editStatus.data = {
            userNotConfirmed : '<?php echo User::USER_NOT_CONFIRMED?>',
            userActive : '<?php echo User::USER_ACTIVE?>',
            userWarning : '<?php echo User::USER_WARNING?>',
            userTemporaryBlocked : '<?php echo User::USER_TEMPORARY_BLOCKED?>',
            userBlocked : '<?php echo User::USER_BLOCKED?>',
            nextDate : '<?php echo date('d-m-Y', strtotime('+5 days')) ?>'
        };
        editStatus.init();
        editStatus.loadInfo();
    });
</script>