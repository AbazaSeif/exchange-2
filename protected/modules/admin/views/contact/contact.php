<h1>Контактные лица</h1>
<div class="create-user">
    <?php
        echo CHtml::link('Создать контактное лицо', '/admin/contact/createcontact/', array('class' => 'btn-admin btn-create'));
        $dropDownStatus = User::$userStatus;
        $dropDownStatus[] = 'Все';
        echo CHtml::dropDownList('type-c-status', $type, $dropDownStatus);     
        echo CHtml::label('Статус', 'type-c-status');
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
            <div class="query-field">
                <input type="text" id="u-search" placeholder='Поиск по "Названию компании" / Email ...' />
                <ul class="quick-result"></ul>
            </div>
            <?php echo CHtml::submitButton('Искать',array('class'=>'btn-admin btn-search')); ?>
            <div style="display: block; clear: both;"></div>
        </div>
        <div id="c-content">
            <?php $this->widget('zii.widgets.CListView', array(
                'dataProvider'=>$data,
                'itemView'=>'_item', // представление для одной записи
                'ajaxUpdate'=>false, // отключаем ajax поведение
                'emptyText'=>'Нет пользователей',
                'template'=>'{summary} {sorter} {items} {pager}',
                'summaryText'=>'Показано {start}-{end} из {count}',
                'sorterHeader'=>'',
                'itemsTagName'=>'ul',
                'sortableAttributes'=>array('company', 'status', 'email'),
                'pager'=>array(
                    'class'=>'LinkPager',
                    'header'=>false,
                    'prevPageLabel'=>'<',
                    'nextPageLabel'=>'>',
                    'lastPageLabel'=>'В конец >>',
                    'firstPageLabel'=>'<< В начало',
                    'maxButtonCount' => '5'
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
        
        <?php if (!empty($input)):?>
            $('#u-search').val('<?php echo $input?>'); 
        <?php endif; ?>
        
        $('#type-c-status').change(function() {
            sessionStorage.setItem('contactStatus', this.value);
            var path = "<?php echo Yii::app()->getBaseUrl(true) ?>/admin/contact/index/status/" + this.value;
            var input = $('#u-search').val();
            if($.trim(input))path += "/input/" + input;
            document.location.href = path;
            //document.location.href = "<?php //echo Yii::app()->getBaseUrl(true) ?>/admin/contact/index/status/" + this.value;
        });
        
        $('.btn-admin.btn-search').click(function(){
            var path = '';
            var status = parseInt(sessionStorage.getItem('contactStatus'));
            var input = $('#u-search').val();
            
            if(!isNaN(status)) path = "/status/" + status;
            if($.trim(input))path += "/input/" + input;
            document.location.href = "<?php echo Yii::app()->getBaseUrl(true) ?>/admin/contact/index" + path;
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
        /**** Quick results ******************************************/
        $('#u-search').focus(function(){
            $('#u-search').blur(function(){
                $('.quick-result').fadeOut(200);
            });
            $('.quick-result').fadeIn(200);
            var ajax = new AjaxQuickSearch(1);
        });
    });
</script>