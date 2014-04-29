<h1>Компании</h1>
<div class="create-user">
    <?php
        echo CHtml::link('Создать компанию', '/admin/user/createuser/', array('class' => 'btn-admin btn-create'));
        $dropDownStatus = User::$userStatus;
        $dropDownStatus[] = 'Все';
        echo CHtml::dropDownList('type-status', $type, $dropDownStatus);     
        echo CHtml::label('Статус', 'type-status');
    ?>
</div>
<div style="clear: both"></div>
<div class="right">
    <?php
        if ($mess = Yii::app()->user->getFlash('message')){
            echo '<div class="trDelMessage success">'.$mess.'</div>';
        }
    ?>
    <div id="user-wrapper">
        <div class="u-header">
            Список компаний
        </div>
        <div id="u-content">
            <?php $this->widget('zii.widgets.CListView', array(
                'dataProvider'=>$data,
                'itemView'=>'user/_item', // представление для одной записи
                'ajaxUpdate'=>false, // отключаем ajax поведение
                'emptyText'=>'Нет пользователей',
                'template'=>'{sorter} {items} {pager}',
                'sorterHeader'=>'',
                'itemsTagName'=>'ul',
                'sortableAttributes'=>array('company', 'inn', 'status', 'email'),
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
    </div>
</div>

<script>
    $(function() {
        var activeStatus = parseInt(sessionStorage.getItem('userStatus'));
        if(!isNaN(activeStatus)) $('#type-status').val(activeStatus);
        else $('#type-status').val(5);
        
        $('#type-status').change(function() {
            sessionStorage.setItem('userStatus', this.value);
            document.location.href = "<?php echo Yii::app()->getBaseUrl(true) ?>/admin/user/index/status/" + this.value;
        });
        
        /********************************************************/
        var element = '<div class="u-block hide">' +
                  '<div>' +
                        '<div class="width-30">' + 
                            '<label>Статус:</label>' +
                        '</div>' +
                        '<div class="width-60">' + 
                            '<select id="u-block-status">' +
                              '<option value="<?php echo User::USER_NOT_CONFIRMED?>">Не подтвержден</option>' +
                              '<option value="<?php echo User::USER_ACTIVE?>">Активен</option>' +
                              '<option value="<?php echo User::USER_WARNING?>">Предупрежден</option>' +
                              '<option value="<?php echo User::USER_TEMPORARY_BLOCKED?>">Временно заблокирован</option>' +
                              '<option value="<?php echo User::USER_BLOCKED?>">Заблокирован</option>' +
                            '</select>'+
                        '</div>' +
                  '</div>' +
                  '<div>' +
                        '<div class="width-30">' + 
                            '<label>До даты:</label>' +
                        '</div>' +
                        '<div class="width-60">' + 
                            '<input id="u-block-to" type="text"/>' +
                        '</div>' +
                  '</div>' +
                  '<div>' +
                        '<div class="width-30">' + 
                            '<label>Причина:</label>' +
                        '</div>' +
                        '<div class="width-60">' + 
                            '<textarea id="u-block-reason">' +
                            '</textarea>' +
                        '</div>' +
                  '</div>' +
                  '<div>' +
                        '<div class="width-90">' + 
                            '<input id="block-submit" class="btn-admin" type="button" value="Подтвердить"/>'+
                            '<input id="block-cancel" class="btn-admin" type="button" value="Закрыть"/>'+
                        '</div>' +
                  '</div>' +
                  '<div class="hide" id="u-block-id">' +
                  '</div>' +
            '</div>';
        $( "body" ).append( element );

        $('.block-images img').unbind('click').click(function(e) {
            var pos = $(this).position();
            var status = $(this).attr('status');
            $('#u-block-status').val(status);
            $('.u-block').css('left', pos.left - 250);
            $('.u-block').css('top', e.pageY + 20);
            $('#u-block-id').val($(this).attr('userId'));
            $('#u-block-reason').css('border-color', '#cccccc');
            $('#u-block-to').css('border-color', '#cccccc');
            if(status == <?php echo User::USER_NOT_CONFIRMED ?> || status == <?php echo User::USER_ACTIVE ?>) {
                $('#u-block-to').parent().parent().addClass('hide');
                $('#u-block-reason').parent().parent().addClass('hide');
            } else {
                $('#u-block-reason').parent().parent().removeClass('hide');
                if(status == <?php echo User::USER_TEMPORARY_BLOCKED ?>) $('#u-block-to').parent().parent().removeClass('hide');
                else $('#u-block-to').parent().parent().addClass('hide');
            }
            
            $.ajax({
                type: 'POST',
                url:  '/admin/user/updateStatus',
                dataType: 'json',
                data:{
                    id: $(this).attr('userId'),
                },
                success: function(response) {
                    $('#u-block-reason').val(response.message);
                    $('#u-block-to').val(response.date);
                    $('.u-block').removeClass('hide');
            }});
        });
        
        $('#u-block-status').change(function() {
            var status = $(this).val();
            if(status != <?php echo User::USER_NOT_CONFIRMED ?> && status != <?php echo User::USER_ACTIVE ?>) {
                $('#u-block-reason').parent().parent().removeClass('hide');
                $('img[userid=' + $('#u-block-id').val() + ']').attr('src', '/images/ico-blocked.png');
                if(status == <?php echo User::USER_TEMPORARY_BLOCKED ?>) {
                    $('#u-block-to').val('<?php echo date('d-m-Y', strtotime('+5 days')) ?>');
                    $('#u-block-to').parent().parent().removeClass('hide');
                }
                else $('#u-block-to').parent().parent().addClass('hide');
            } else {
                $('#u-block-to').parent().parent().addClass('hide');
                $('#u-block-reason').parent().parent().addClass('hide');
                $('img[userid=' + $('#u-block-id').val() + ']').attr('src', '/images/ico-no-blocked.png');
            }
        });
        
        $('#block-cancel').click(function() {
            $('.u-block').addClass('hide');
        });

        // Клик оказался в пределах окна
        //$('.u-block').click(function(event){event.stopPropagation();});
        $('#ui-datepicker-div').click(function(){
            console.log(55);
            //event.stopPropagation();
        });
        //$('body').click(function() { $('.u-block').addClass('hide'); });
        
        $('#block-submit').click(function() {
            var status = $('#u-block-status').val();
            //console.log(status);
            var reason = $.trim($('#u-block-reason').val());
            var date = $.trim($('#u-block-to').val());
            var flag = true;
            if(status == <?php echo User::USER_NOT_CONFIRMED ?> || status == <?php echo User::USER_ACTIVE ?>){
                flag = true;
                $('#u-block-reason').css('border-color', '#cccccc');
            } else {
                if(!reason) {
                    $('#u-block-reason').css('border-color', 'red');
                    flag = false;
                }
                if(status == <?php echo User::USER_TEMPORARY_BLOCKED ?> && !date) {
                    $('#u-block-to').css('border-color', 'red');
                    flag = false;
                }
            }
            if(flag){
                $.ajax({
                    type: 'POST',
                    url:  '/admin/user/updateStatus',
                    dataType: 'json',
                    data:{
                        id: $('#u-block-id').val(),
                        status: status,
                        reason: reason,
                        date: date,
                    },
                    success: function(response) {
                        if(response.message != 'date'){
                            var element = $('img[userid=' + $('#u-block-id').val() + ']');
                            element.parent().parent().parent().find('.u-status').text(response.message);
                            element.attr('status', status);
                            $('.u-block').addClass('hide');
                        } else {
                            $('#u-block-to').css('border-color', 'red');
                            flag = false;
                        }
                }});
            }
        });
        
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
		
        $( "#u-block-to" ).datepicker({
            dateFormat: 'dd-mm-yy',
        });
    });
</script>