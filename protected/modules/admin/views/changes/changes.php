<h1>История редактирования</h1>
<div class="create-user">
    <?php
        $dropDownStatus = array(
            0 => 'По имени, фамилии, отчеству',
            1 => 'По дате редактирования',
        );        
        echo CHtml::dropDownList('type-status', $type, $dropDownStatus);     
        echo CHtml::label('Сортировать', 'type-status');
    ?>
</div>
<div style="clear: both"></div>
<div class="right">
    <div id="user-wrapper" class="changes">
        <div class="u-header">
            <div class="query-field">
                <input type="text" id="u-search" placeholder='Поиск по Фамилии / Имени / Отчеству / Email ...' />
                <ul class="quick-result"></ul>
            </div>
            <?php echo CHtml::submitButton('Искать',array('class'=>'btn-admin btn-search')); ?>
            <div style="display: block; clear: both;"></div>
        </div>
        <div id="u-content">
            <?php $this->widget('zii.widgets.CListView', array(
                'dataProvider'=>$data,
                'itemView'=>'/changes/_item', // представление для одной записи
                'ajaxUpdate'=>false, // отключаем ajax поведение
                'emptyText'=>'Нет пользователей',
                'template'=>'{summary} {sorter} {items} {pager}',
                'summaryText'=>'Показано {start}-{end} из {count}',
                'sorterHeader'=>'',
                'itemsTagName'=>'ul',
                'sortableAttributes'=>array(
                    'surname'=>'Фамилия',
                    'name'=>'Имя',
                    'secondname'=>'Отчество',
                    'last_edit'=>'Последнее редактирование'
                ),
                'pager'=>array(
                    'class'=>'LinkPager',
                    'header'=>false,
                    'prevPageLabel'=>'<',
                    'nextPageLabel'=>'>', //'<img src="images/pagination/left.png">',
                    'lastPageLabel'=>'В конец >>',
                    'firstPageLabel'=>'<< В начало',
                    'maxButtonCount' => '5'
                ),
            )); ?>
        </div>
    </div>
</div>
<script>
    $(function() {
        var showChanges = parseInt(sessionStorage.getItem('showChanges'));
        if(!isNaN(showChanges)) $('#type-status').val(showChanges);
        
        <?php if (!empty($input)):?>
            $('#u-search').val('<?php echo $input?>'); 
        <?php endif; ?>
                
        $('.btn-admin.btn-search').click(function(){
            $("#type-status").val(0);
            sessionStorage.setItem('showChanges', 0);
            var path = '';
            var input = $('#u-search').val();

            if($.trim(input)) path += "/input/" + input;
            document.location.href = "<?php echo Yii::app()->getBaseUrl(true) ?>/admin/changes/index" + path;
        });
    
        $('#type-status').on('change', function() {
            sessionStorage.setItem('showChanges', this.value);
            var path = "<?php echo Yii::app()->getBaseUrl(true) ?>/admin/changes/index/";
            if(this.value == 1) path = "<?php echo Yii::app()->getBaseUrl(true) ?>/admin/changes/dateOrder/";
            document.location.href = path;
        });
        /**** Quick results ******************************************/
        $('#u-search').focus(function(){
            $('#u-search').blur(function(){
                $('.quick-result').fadeOut(200);
            })
            $('.quick-result').fadeIn(200);
            var ajax = new AjaxQuickSearch(2);
        });
    });
</script>