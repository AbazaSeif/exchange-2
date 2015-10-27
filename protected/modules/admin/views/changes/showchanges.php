<div class="total show-changes">
    <h1>История редактирования пользователя <?php echo $user; ?></h1>
    <div class="buttons">
        <?php
            echo CHtml::button('Закрыть',array('id'=>'close-changes', 'class'=>'btn-admin')); 
        ?>
        <div style="clear:both"></div>
    </div>
    <div class="grid-wrapper">
    <?php
        if(count($data->getData())){
            $this->widget('zii.widgets.grid.CGridView', array(
                'dataProvider'=>$data,
                //'id' => 'grid-changes',
                'template'=>'{items}{pager}{summary}',
                'summaryText'=>'Элементы {start}—{end} из {count}.',
                'columns'=>array(
                    'date' => array(
                        'name' => 'date',
                        'value' => 'date("Y-m-d H:i", strtotime($data->date))',
                    ),
                    'description',
                ),
                'pager'=>array(
                    'class'=>'LinkPager',
                )
            )); 
        } else {
            echo '<div>Пусто</div>';
        }
    ?>
    </div>
</div>
<script>
    $(document).ready(function(){ 
        var showChanges = parseInt(sessionStorage.getItem('showChanges'));
        $('#close-changes').click(function(){
            var path = "<?php echo Yii::app()->getBaseUrl(true) ?>/admin/changes/index/";
            if(!isNaN(showChanges)) {
                $("#type-status").val(showChanges);
                if(showChanges == 1) path = "<?php echo Yii::app()->getBaseUrl(true) ?>/admin/changes/dateOrder/";
            }
            document.location.href = path;  
        });
    });
</script>