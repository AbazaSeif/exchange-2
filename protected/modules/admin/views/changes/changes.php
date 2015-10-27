<h1>История редактирования</h1>
<div class="grid-wrapper">
    <?php
    $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'changesListGrid',
        'emptyText'=>'Нет информации',
        //'filter'=>$model,
        'dataProvider'=>$data,
        'template'=>'{items}{pager}{summary}',
        'summaryText'=>'Элементы {start}—{end} из {count}.',
        'pager' => array(
            'class' => 'LinkPager',
        ),
        /*'afterAjaxUpdate'=>"function(id,data){ $('.description').dotdotdot({
                ellipsis	: '... ',
                wrap	: 'letter',
            });
        }",*/
        'columns' => array(
            array(
                'name'=>'id',
                'type'=>'raw',
                'filter'=>false,
                'value'=>'CHtml::link($data->id, array("showchanges","id"=>$data->id))'
            ),
            array(
                'name'=>'date',
                //'type'=>'raw',
                //'filter'=>false,
                'value'=>'date("Y-m-d H:i", strtotime($data->date))',
            ),
            array(
                'name'=>'user',
                'header'=> 'Пользователь',
                //'filter' => $filter,
                'type'=>'raw',
                'value'=>'Changes::getAuthUser($data->user_id)',
            ),
            array(
                'name'=>'description',
                'type'=>'raw',
                'filter'=>false,
                'value'=>function($data){
                    return '<div class="description">'.htmlspecialchars($data->description).'</div>';
                },
            )
        ),
    ));

?>
</div>
<script>
//$(document).ready(function(){
//    $('.description').dotdotdot({
//        ellipsis	: '... ',
//        wrap		: 'letter',
//    });
//});
</script>
