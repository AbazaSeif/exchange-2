<h1>История редактирования</h1>
<div class="create-user">
    <?php
        $dropDownStatus = array(
            0 => 'Показать все',
            1 => 'По дате редактирования',
        );        
        echo CHtml::dropDownList('type-status', $type, $dropDownStatus);     
        //echo CHtml::label('Сортировать по', 'type-status');
    ?>
</div>
<div style="clear: both"></div>
<div class="right">
    <?php
       /* if ($mess = Yii::app()->user->getFlash('message')){
            echo '<div class="trDelMessage success">'.$mess.'</div>';
        }*/
    ?>
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
        $('.btn-admin.btn-search').click(function(){
            var path = '';
            var input = $('#u-search').val();

            if($.trim(input)) path += "/input/" + input;
            document.location.href = "<?php echo Yii::app()->getBaseUrl(true) ?>/admin/changes/index" + path;
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