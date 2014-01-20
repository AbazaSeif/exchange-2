<?php
    $lastRate = '';
	if(!empty($transportInfo['rate_id'])) $lastRate = $this->getPrice($transportInfo['rate_id']);
?>
<div>Перевозка <?php echo '"' . $transportInfo['location_from'] . '-' . $transportInfo['location_to'] . '"' ?></div>
<div>Создана <?php echo date('d.m.Y H:i', strtotime($transportInfo['date_published'])) ?></div>
<div class="transport-info">
    <div>Дата отправки: <?php echo date('d.m.Y H:i', strtotime($transportInfo['date_from'])) ?></div> 
	<div>Дата прибытия: <?php echo date('d.m.Y H:i', strtotime($transportInfo['date_to'])) ?></div> 
	<div>Описание: <?php echo $transportInfo['description'] ?></div> 
	<?php if(!Yii::app()->user->isGuest): ?>
	<div>Текущая ставка: <span id="last-rate"><?php echo ($lastRate) ? $lastRate : $transportInfo['start_rate'] ?></span></div> 
    <?php endif;?>
</div>
<div id="data">
<?php //$this->renderPartial('ajaxList', array('data'=>$data)); ?>
</div>
<?php
//echo CHtml::ajaxButton('update', '/user/transport/updateRates', array('update' => '#data'));
if(!Yii::app()->user->isGuest){
    echo '<div id="chat"></div>';
	$this->widget('YiiChatWidget',array(
		'chat_id'=>$transportInfo['id'],
		'identity'=>Yii::app()->user->_id,
		'selector'=>'#chat',               
		'minPostLen'=>2,                    // min and
		'maxPostLen'=>10,                   // max string size for post
		'model'=>new ChatHandler(),
		'data'=>'any data',                 // data passed to the handler
		'onSuccess'=>new CJavaScriptExpression(
			"function(code, text, post_id){   }"),
		'onError'=>new CJavaScriptExpression(
			"function(errorcode, info){  }"),
	));
}
?>
<script>
$(document).ready(function(){
setInterval(function(){
updateCounter();
<?php //echo CHtml::ajax(array('url'=>'/user/transport/updateRates', 'update' => '#data', 'type'=>'post')); ?>
}, 5000);
});

function updateCounter(){
    $.ajax({
		type: 'POST',
		url: '/user/transport/updateRatesPrice',
		dataType: 'json',
		data:{
		    id: <?php echo $transportInfo['id']; ?>
		},
		success: function(data){
		    //var json = $.parseJSON(data);
			$('#last-rate').html(data);
			//$('#last-rate').html(data.price);
	}});
}
</script>
