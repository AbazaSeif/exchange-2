<?php
$lastRate = '';
$priceStep = Transport::INTER_PRICE_STEP;
if (!empty($transportInfo['rate_id'])) $lastRate = $this->getPrice($transportInfo['rate_id']);
if (!$lastRate) $lastRate = $transportInfo['start_rate'];
else $lastRate -= $priceStep;

$now = date('Y m d H:i:s', strtotime('now'));
$end = date('Y m d H:i:s', strtotime($transportInfo['date_to'] . ' -' . Yii::app()->params['hoursBefore'] . ' hours'));

?>
<div class="transport-one">
<h1><?php echo $transportInfo['location_from'] . ' &mdash; ' . $transportInfo['location_to']; ?></h1>
<span class="t-o-published">Опубликована <?php echo date('d.m.Y H:i', strtotime($transportInfo['date_published'])) ?></span>
<div class="t-o-info">
<label>Основная информация</label>
<span>Место загрузки: <?php echo $transportInfo['location_from'] ?></span>
<span>Место разгрузки: <?php echo $transportInfo['location_to'] ?></span>
<span>Дата отправки: <?php echo date('d.m.Y', strtotime($transportInfo['date_from'])) ?></span>
<span>Дата прибытия: <?php echo date('d.m.Y', strtotime($transportInfo['date_to'])) ?></span>
<span>Описание: <?php echo $transportInfo['description'] ?>
        </div>        
<?php if (!Yii::app()->user->isGuest): ?>
         <div id="timer"></div>
<div>Текущая ставка: <span id="last-rate"><?php echo $lastRate ?></span></div>
<?php endif; ?>
</div>
<div class="rate-btns">
        <input id="rate-price" init="<?php echo ($lastRate<0)? ($lastRate + $priceStep) : $lastRate; ?>" type="text" size="1" value="<?php echo ($lastRate<0)? ($lastRate + $priceStep) : $lastRate; ?>" disabled="<?php echo ($lastRate<0)? 'disabled' : '' ?>"/>
        <div class="up disabled"></div>
        <div class="down <?php echo (($lastRate - $priceStep)<0)?'disabled':''?>"></div>
</div>
<div id="rate-btn" class="btn-green btn big <?php echo (($lastRate - $priceStep)<0)?'disabled':''?>">ОK</div>
<div class="clear"></div>
<div id="data"></div>

<?php
/*if (!Yii::app()->user->isGuest) {
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
"function(code, text, post_id){ }"),
'onError' => new CJavaScriptExpression(
"function(errorcode, info){ }"),
));
}
}*/
?>
</div>
<script>

$(document).ready(function(){
var myClassObject = new Timer();
myClassObject.init('<?php echo $now ?>', '<?php echo $end ?>', 'timer');
        
        var btnUp = $("div.rate-btns").find('div.up');        
        var btnDown = $("div.rate-btns").find('div.down');
        var btnSend = $("#rate-btn");
        var priceStep = <?php echo Transport::INTER_PRICE_STEP ?>;
        //var initValue = parseInt($("#rate-price").attr('init'));
        var posts = $("#data");
        //$("#rate-price")
        btnUp.click(function(){
         var element = $('#rate-price');
                if(!$(this).hasClass('disabled')){
                        var curRate = element.val();
                        var newRate = parseInt(curRate) + priceStep;
                        if(newRate <= $("#rate-price").attr('init')) element.val(newRate);
                        
                        //button style
                        if (parseInt($("#rate-price").attr('init')) == element.val()) $(this).addClass('disabled');
                 else $(this).removeClass('disabled');
}
        });

        btnDown.click(function(){
                if($("#rate-price").val() < $("#rate-price").attr('init'))
                 $('div.up').removeClass('disabled');
         if(!$(this).hasClass('disabled')){
                        var element = $('#rate-price');
                        var curRate = element.val();
                        var newRate = parseInt(curRate) - priceStep;
                        if(newRate > 0) element.val(newRate);
                }
        });

        btnSend.click(function(){
         if(!$(this).hasClass('disabled')){
                 updateCounter(posts, $('#rate-price').val());
                }
        });
        
        // !!! включить
        updateCounter(posts);
        //setInterval(function(){updateCounter(posts);}, 15000);
});

function updateCounter(posts, price) {
price = typeof price !== 'undefined' ? price : '';
        var currentScroll = $('#data').scrollTop();
        //console.log(currentScroll);
        var startCount = $('#data').find('.post').length;
        //var height = $('#data:first').outerHeight();
        //console.log(height);
$.ajax({
                type: 'POST',
                url: '/transport/updateRatesPrice',
                dataType: 'json',
                data:{
                 id: <?php echo $transportInfo['id']; ?>,
                        newRate: price,
                },
                success: function(rates) {
                        $.each( rates.all, function( key, value ) {
                                add(value, posts);
                        });
                        
                        $("#rate-price").attr('init', rates.price);
                        //$('#data').scrolBottom(currentScroll);
                        $('#last-rate').html(rates.price);
                        
                        /*--------------*/
/*var countNewElements = $('#data').find('.post').length - startCount;
                        console.log($('#data').find('.post').length);
                        console.log(countNewElements);
                        if(countNewElements>0){
                                //console.log($('#data').find('.post').outerHeight());
                                console.log('=');
                        }*/
        }});
}

function add(post, posts){
        posts.prepend("<div id='"+post.id+"' class='post'>"
                + "<div class='track'></div>"
                + "<div class='text'>111</div>"
         + "</div>")
        ;
        var p = posts.find(".post[id='"+post.id+"']");
        p.find('.track').html("<div class='time'>" + post.time + "</div>"
                + "<div class='owner'>" + post.name + ' ' + post.surname + "</div>")
        ;
        p.find('.text').html(post.price);
}

/*
var scroll = function(){
        //window.location = '#'+posts.find('.post:last').attr('id');
        var h=0;
        
        //posts.find('.post').each(function(k){
                //h += $(this).outerHeight();
        //});
        
        //posts.scrollTop(h);
}*/
</script>