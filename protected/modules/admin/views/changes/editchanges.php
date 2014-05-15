<?php
    $close_text = 'Закрыть';
    $user = Yii::app()->db_auth->createCommand()
        ->from('user')
        ->where('id = '.$id)
        ->queryRow()
    ;
    $header_form = '"'.$user[surname].' '.$user[name].' '.$user[secondname] . '"';
?>
<div class="total">
    <div class="left">
        <h1>История редактирования</h1>
        <div class="header-form">
            <?php echo $header_form; ?>
        </div>
        <div>Для того, чтобы вернуться к списку пользователей нажмите кнопку "<?php echo $close_text?>"
        </div>
    </div>
    <div class="right">
        <div class="buttons">
            <?php
                echo CHtml::button($close_text,array('id'=>'close-changes', 'class'=>'btn-admin')); 
            ?>
            <div style="clear:both"></div>
        </div>
        <?php
            if(count($data->getData())){
                $this->widget('zii.widgets.grid.CGridView', array(
                    'dataProvider'=>$data,
                    'id' => 'grid-changes',
                    'columns'=>array(
                        'date' => array(
                            'name' => 'date',
                            'value' => 'date("d.m.Y H:i", strtotime($data->date))',
                        ),
                        'description',
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