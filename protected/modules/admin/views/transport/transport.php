<h1>Перевозки</h1>
<!--div class="total">
    <div class="left">
    <div class="create-button">
     <?php
     echo CHtml::ajaxLink('Создать', '/admin/transport/createtransport/', array('update'=>'.right'), array('class'=>'btn-green btn'));
     ?>   
    </div>
    <?php
    $this->widget('zii.widgets.CListView', array(
        'dataProvider'=>$data,
        'itemView'=>'_item', // представление для одной записи
        'ajaxUpdate'=>false, // отключаем ajax поведение
        'emptyText'=>'Нет перевозок',
        'template'=>'{sorter} {items} {pager}',
        'sorterHeader'=>'',
        'itemsTagName'=>'ul',
        'sortableAttributes'=>array('location_from', 'location_to'),
        'pager'=>array(
            'class'=>'CLinkPager',
            'header'=>false,
            'prevPageLabel'=>'<',
            'nextPageLabel'=>'>',
            'lastPageLabel'=>'>>',
            'firstPageLabel'=>'<<'
        ),
    ));
    ?>
    </div>
    <div class="right">
        <?php
        if ($mess = Yii::app()->user->getFlash('message')){
            echo '<div class="message success">'.$mess.'</div>';
        }
        if ($view){
            echo $view;
        }
        ?>
    </div>
</div>
<?php if($data->pagination->pageCount!=0) { 
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
        var start = new AjaxContentLoader();
        start.init('.left', '.ajax', '.right' ,false);
    });
</script-->
<div id="tabs">
<ul>
<li><a href="#tabs-1">Активные</a></li>
<li><a href="#tabs-2">Архивные</a></li>
<li><a href="#tabs-3">Создать перевозку</a></li>
</ul>
<div id="tabs-1">
<p>Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.</p>
</div>
<div id="tabs-2">
<p>Morbi tincidunt, dui sit amet facilisis feugiat, odio metus gravida ante, ut pharetra massa metus id nunc. Duis scelerisque molestie turpis. Sed fringilla, massa eget luctus malesuada, metus eros molestie lectus, ut tempus eros massa ut dolor. Aenean aliquet fringilla sem. Suspendisse sed ligula in ligula suscipit aliquam. Praesent in eros vestibulum mi adipiscing adipiscing. Morbi facilisis. Curabitur ornare consequat nunc. Aenean vel metus. Ut posuere viverra nulla. Aliquam erat volutpat. Pellentesque convallis. Maecenas feugiat, tellus pellentesque pretium posuere, felis lorem euismod felis, eu ornare leo nisi vel felis. Mauris consectetur tortor et purus.</p>
</div>
<div id="tabs-3">
    <div class="form">
        <?php 
        $newTransport = new Transport;
        $form = $this->beginWidget('CActiveForm', array(
                'id'=>'transport-form',
                'action'=>$action,
                'enableClientValidation' => true,        
                'clientOptions'=>array(
                    'validateOnSubmit'=>true,
                    'validateOnChange' => true,
                    'afterValidate'=>'js:function( form, data, hasError ) 
                    {     
                        if( hasError ){
                            return false;
                        }
                        else{
                            return true;
                        }
                    }'
                ),
            ));
        ?>
        <div class="buttons">
        <?php  
            //echo CHtml::button('Закрыть перевозку',array('onclick'=>'$(".total .right").html(" ");','class'=>'btn'));
            //echo CHtml::submitButton($submit_text,array('id'=>'but_'.$name,'class'=>'btn btn-green')); 
        ?>
        </div>
        <div class="field">
        <?php echo $form->error($newTransport, 'type');
            echo $form->labelEx($newTransport, 'type');
            echo $form->dropDownList($newTransport, 'type', Transport::$group); ?>
        </div>
        <div class="field">
        <?php  echo $form->error($newTransport, 'location_from'); 
            echo $form->labelEx($newTransport, 'location_from');
            echo $form->textField($newTransport, 'location_from');
        ?>    
        </div>
        <div class="field">
        <?php  echo $form->error($newTransport, 'location_to'); 
            echo $form->labelEx($newTransport, 'location_to');
            echo $form->textField($newTransport, 'location_to');?>    
        </div>
        <div class="field">
        <?php echo $form->error($newTransport, 'start_rate'); 
            echo $form->labelEx($newTransport, 'start_rate');
            echo $form->textField($newTransport, 'start_rate');
        ?>    
        </div>
        <div class="field">
        <?php echo $form->error($newTransport, 'currency');
            echo $form->labelEx($newTransport, 'currency');
            echo $form->dropDownList($newTransport, 'currency', Transport::$currencyGroup); ?>
        </div>
        <div class="field">
        <?php  echo $form->error($newTransport, 'description'); 
            echo $form->labelEx($newTransport, 'description');
            echo $form->textArea($newTransport, 'description');?>    
        </div>
        <div class="field">
        <?php  echo $form->error($newTransport, 'auto_info'); 
            echo $form->labelEx($newTransport, 'auto_info');
            echo $form->textArea($newTransport, 'auto_info');?>    
        </div>
        <div class="field">
        <?php echo $form->error($newTransport, 'status');
            echo $form->labelEx($newTransport, 'status');
            echo $form->dropDownList($newTransport, 'status', Transport::$status); ?>
        </div>
        <div class="field">
            <?php
                echo CHtml::label('Часовой Пояс', 'timer_label');
                echo CHtml::textField('timer_label', 'Московское время', array('disabled'=>true));
            ?>
        </div>
        <div class="field">
            <?php
                echo CHtml::label('Время закрытия заявки', 'timer_deadline');
                $value = date("d-m-Y H:i", strtotime('now' . "-" . Yii::app()->params['hoursBefore'] . " hours"));
                echo CHtml::textField('timer_deadline', $value, array('disabled'=>true));
            ?>
        </div>
        <div class="field">
        <?php  echo $form->error($newTransport, 'date_from'); 
            echo $form->labelEx($newTransport, 'date_from');
            $newTransport->date_from = date("d-m-Y H:i", strtotime('now'));
            echo $form->textField($newTransport, 'date_from', array('onchange'=>'updateFieldTimerDeadline();')); ?>    
        </div>
        <div class="field">
        <?php echo $form->error($newTransport, 'date_to'); 
            echo $form->labelEx($newTransport, 'date_to');
            $newTransport->date_to = date("d-m-Y H:i", strtotime('now'));
            echo $form->textField($newTransport, 'date_to'); ?>    
        </div>

    <?php $this->endWidget(); ?> 
    </div>
</div>
</div>

<script>
$(function() {
$( "#tabs" ).tabs();
});
</script>