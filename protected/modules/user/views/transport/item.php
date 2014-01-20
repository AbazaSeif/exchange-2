<?php
$lastRate = '';
if (!empty($transportInfo['rate_id']))
    $lastRate = $this->getPrice($transportInfo['rate_id']);
?>
<div class="transport-one">
    <h1><?php echo $transportInfo['location_from'] . ' &mdash; ' . $transportInfo['location_to']; ?></h1>
    <span class="t-o-published">Опубликована <?php echo date('d.m.Y H:i', strtotime($transportInfo['date_published'])) ?></span>
    <div class="t-o-info">
        <label>Основная информация</label>
        <span>Место загрузки: <?php echo date('d.m.Y', strtotime($transportInfo['location_from'])) ?></span> 
        <span>Место разгрузки: <?php echo date('d.m.Y', strtotime($transportInfo['location_to'])) ?></span> 
        <span>Дата отправки: <?php echo date('d.m.Y', strtotime($transportInfo['date_from'])) ?></span> 
        <span>Дата прибытия: <?php echo date('d.m.Y', strtotime($transportInfo['date_to'])) ?></span> 
        <span>Описание: <?php echo $transportInfo['description'] ?></div> 
    <?php if (!Yii::app()->user->isGuest): ?>
        <div>Текущая ставка: <?php echo ($lastRate) ? $lastRate : $transportInfo['start_rate'] ?></div> 
<?php endif; ?>
</div>

<?php
if (!Yii::app()->user->isGuest) {
    if (Yii::app()->user->checkAccess('transport')) {
        echo '<div id="chat"></div>';
        $this->widget('YiiChatWidget', array(
            'chat_id' => $transportInfo['id'],
            'identity' => Yii::app()->user->_id,
            'selector' => '#chat',
            'minPostLen' => 2, // min and
            'maxPostLen' => 10, // max string size for post
            'model' => new ChatHandler(),
            'data' => 'any data', // data passed to the handler
            'onSuccess' => new CJavaScriptExpression(
                    "function(code, text, post_id){   }"),
            'onError' => new CJavaScriptExpression(
                    "function(errorcode, info){  }"),
        ));
    }
}
?>
</div>