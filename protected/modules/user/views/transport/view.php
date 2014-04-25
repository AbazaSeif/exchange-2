<h1>
    <?php echo $title ?>
</h1>
<?php
    $this->widget('zii.widgets.CListView', array(
        'dataProvider' => $data,
        'cssFile'      => false,
        'itemView'     => '_view',
        'ajaxUpdate'   => false,
        'emptyText'    => 'Нет перевозок',
        'itemsTagName' => 'div',
        'template'     => '{sorter}{items}{pager}',
        'htmlOptions'  => array('class'=>'transports'),
        'sortableAttributes' => array('location_from', 'location_to', 'l', 'date_close'=>'Время до закрытия заявки'),
        'sorterHeader' => '',
        'pager'        => array(
            'header'   => false,
            'firstPageLabel' => 'В начало',
            'prevPageLabel'  => 'Назад',
            'nextPageLabel'  => 'Вперёд',
            'lastPageLabel'  => 'В конец',
            'cssFile'        => false
        )
    )); 

if($data->pagination->pageCount!=0) { 
    $c = ($data->pagination->pageCount+4);
    ?>
    <style>
    #search-index .pager ul.yiiPager li{
        width: <?php echo 100/($c>14?14:$c); ?>%;
    }
    </style>
<?php } ?>
<script>
$(document).ready(function(){
    $('.t-timer').each(function(){
       if(parseInt($(this).attr('status'))){
           var timer = new Timer();
           timer.init($(this).attr('now'), $(this).attr('end'), $(this).attr('id'), $(this).attr('status'));
       } else {
           $('#' + $(this).attr('id')).html('<span class="t-closed">Перевозка закрыта</span>');
       }
    });
    $('.description').dotdotdot();
    $('.description-50').dotdotdot();
});
</script>
<!--br/>
<div id="content_1" style="width:260px; height:500px; overflow:auto;">
		<p>Lorem ipsum dolor sit amet. Aliquam erat volutpat. Maecenas non tortor nulla, non malesuada velit.</p>
   		<p>Aliquam erat volutpat. Maecenas non tortor nulla, non malesuada velit. Nullam felis tellus, tristique nec egestas in, luctus sed diam. Suspendisse potenti. </p>
   		<p>Consectetur adipiscing elit. Nulla consectetur libero consectetur quam consequat nec tincidunt massa feugiat. Donec egestas mi turpis. Fusce adipiscing dui eu metus gravida vel facilisis ligula iaculis. Cras a rhoncus massa. Donec sed purus eget nunc placerat consequat.</p>
   		<p>Cras venenatis condimentum nibh a mollis. Duis id sapien nibh. Vivamus porttitor, felis quis blandit tincidunt, erat magna scelerisque urna, a faucibus erat nisl eget nisl. Aliquam consequat turpis id velit egestas a posuere orci semper. Mauris suscipit erat quis urna adipiscing ultricies. In hac habitasse platea dictumst. Nulla scelerisque lorem quis dui sagittis egestas.</p> 
		<p>Etiam sed massa felis, aliquam pellentesque est.</p>
    	<p>Nam eu arcu at purus tincidunt pharetra ultrices at ipsum. Mauris urna nunc, vulputate quis gravida in, pharetra id mauris. Ut sit amet mi dictum nulla lobortis adipiscing quis a nulla. Etiam diam ante, imperdiet vel scelerisque eget, venenatis non eros. Praesent ipsum sem, eleifend ut gravida eget, tristique id orci. Nam adipiscing, sem in mattis vulputate, risus libero adipiscing risus, eu molestie mi justo eget nulla.</p> 
		<p>Cras venenatis metus et urna egestas non laoreet orci rutrum. Pellentesque ullamcorper dictum nisl a tincidunt. Quisque et lacus quam, sed hendrerit mi. Mauris pretium, sapien et malesuada pulvinar, lorem leo viverra leo, et egestas mi nisl quis odio. </p>
		<p>Aliquam erat volutpat. Sed urna arcu, tempus eu vulputate adipiscing, consectetur et orci. Vivamus congue, nunc vitae fringilla convallis, libero massa lacinia lorem, id convallis mauris elit ut leo. Nulla vel odio sem. Duis lorem urna, congue vitae rutrum sed, tincidunt vel tortor. In hac habitasse platea dictumst. Nunc vitae enim ante, vitae facilisis massa. Etiam sagittis sapien at nibh fermentum consectetur convallis lacus blandit.</p>
    	<p>the end.</p>
	</div-->

