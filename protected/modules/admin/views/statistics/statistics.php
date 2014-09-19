<h1>Статистика архивных перевозок</h1>

<!--div>Сейчас на бирже всего перевозок: <?php echo Transport::model()->count();?> (Активных: <?php echo Transport::model()->count('status=1');?>, Архивных: <?php echo Transport::model()->count('status=0');?>, Черновиков: <?php echo Transport::model()->count('status=2');?>)</div-->

<?php $form = $this->beginWidget('CActiveForm', array(
        //'id'=>'statistics-form',
        //'action'=>'/admin/statistics/getexcel/',
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
<div class="statistics">
    <div class="info">
        <ul class="info-list">
            <li>Выберите поля</li>
            <li>
                <span><?php echo $form->labelEx($model, 'type');?></span>
                <span><?php echo $form->dropDownList($model, 'type', array(0 => 'Все', 1 => 'Международные', 2 => 'Региональные'));?></span>
            </li>
            <li>
                <span>Период</span>
                <span><?php echo $form->textField($model, 'date_from'); ?> - <?php echo $form->textField($model, 'date_to'); ?></span>
            </li>
            <li><?php echo CHtml::button('Скачать Excel', array('class'=>'btn-admin')); ?>
            </li>
        </ul>
    </div>
    <?php //echo CHtml::link('Скачать Excel', '/admin/statistics/', array('class'=>'btn-admin')); ?>
    
</div>
<?php $this->endWidget(); ?> 
<script>
    $.datepicker.regional['ru'] = {
            closeText: 'Закрыть',
            prevText: '&#x3c;Пред',
            nextText: 'След&#x3e;',
            currentText: 'Сегодня',
            monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
            monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
            dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
            dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
            dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
            dateFormat: 'dd.mm.yy',
            firstDay: 1,
            isRTL: false,
        };
        $.datepicker.setDefaults($.datepicker.regional['ru']);
	
        $( "#StatisticsForm_date_from" ).datepicker({
            dateFormat: 'dd-mm-yy',
        });
        
        $( "#StatisticsForm_date_to" ).datepicker({
            dateFormat: 'dd-mm-yy',
        });
        
        $( ".statistics .btn-admin" ).click(function() {
            window.location.replace('/admin/statistics/getExcel/from/17-09-2014/to/19-09-2014/type/0');
        });
</script>
