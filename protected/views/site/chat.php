<h2>Перевозка <?php echo '"' . $transportInfo['location_from'] . '-' . $transportInfo['location_to'] . '"' ?></h2>
<dl class="article-info">
    <dd class="create">Создана <?php echo date('d.m.Y H:i', strtotime($transportInfo['date_published'])) ?></dd>
    <!--div id="user_identifier"><?php echo rand(5, 15) ?></div-->
</dl>
<div class="transport-info">
    <span>Дата отправки: <?php echo date('d.m.Y H:i', strtotime($transportInfo['date_from'])) ?></span> 
</div>
<div id='chat'></div>
<?php 
//'rateData' => $dataProvider, 'transportData' => $transportInfo
//, date('d.m.Y H:i', strtotime($data->date_from)), 
//date('d.m.Y H:i', strtotime($data->date_to))
    $this->widget('YiiChatWidget',array(
        'chat_id'=>$transportInfo['id'],                   // a chat identificator
        'identity'=>1,                      // the user, Yii::app()->user->id ?
        'selector'=>'#chat',                // were it will be inserted
        'minPostLen'=>2,                    // min and
        'maxPostLen'=>10,                   // max string size for post
        'model'=>new ChatHandler(),
        'data'=>'any data',                 // data passed to the handler
        // success and error handlers, both optionals.
        'onSuccess'=>new CJavaScriptExpression(
            "function(code, text, post_id){   }"),
        'onError'=>new CJavaScriptExpression(
            "function(errorcode, info){  }"),
    ));
