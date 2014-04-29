var editStatus = {
    init : function(){
        var element = '<div class="u-block hide">' +
                  '<div>' +
                        '<div class="width-30">' + 
                            '<label>Статус:</label>' +
                        '</div>' +
                        '<div class="width-60">' + 
                            '<select id="u-block-status">' +
                              '<option value="'+editStatus.data.userNotConfirmed+'">Не подтвержден</option>' +
                              '<option value="'+editStatus.data.userActive+'">Активен</option>' +
                              '<option value="'+editStatus.data.userWarning+'">Предупрежден</option>' +
                              '<option value="'+editStatus.data.userTemporaryBlocked+'">Временно заблокирован</option>' +
                              '<option value="'+editStatus.data.userBlocked+'">Заблокирован</option>' +
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
            '</div>'
        ;
        $( "body" ).append( element );
                
        this.addCalendarEditor();
        this.updateData();
        
        $('#block-cancel').click(function() {
            $('.u-block').addClass('hide');
        });
        
        $('#block-submit').click(function() {
            var status = $('#u-block-status').val();
            var reason = $.trim($('#u-block-reason').val());
            var date = $.trim($('#u-block-to').val());
            var flag = true;
            if(status == editStatus.data.userNotConfirmed || status == editStatus.data.userActive){
                flag = true;
                $('#u-block-reason').css('border-color', '#cccccc');
            } else {
                if(!reason) {
                    $('#u-block-reason').css('border-color', 'red');
                    flag = false;
                }
                if(status == editStatus.data.userTemporaryBlocked && !date) {
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
    },
    addCalendarEditor : function() { 
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
            isRTL: false
        };
        $.datepicker.setDefaults($.datepicker.regional['ru']);
		
        $( "#u-block-to" ).datepicker({
            dateFormat: 'dd-mm-yy'
        });
    },
    loadInfo : function() { 
    //this.loadInfo = function(userNotConfirmed, userActive, userTemporaryBlocked) {
        $('.block-images img').unbind('click').click(function(e) {
            var pos = $(this).position();
            var status = $(this).attr('status');
            $('#u-block-status').val(status);
            $('.u-block').css('left', pos.left - 250);
            $('.u-block').css('top', e.pageY + 20);
            $('#u-block-id').val($(this).attr('userId'));
            $('#u-block-reason').css('border-color', '#cccccc');
            $('#u-block-to').css('border-color', '#cccccc');
            if(status === editStatus.data.userNotConfirmed || status === editStatus.data.userActive) {
                $('#u-block-to').parent().parent().addClass('hide');
                $('#u-block-reason').parent().parent().addClass('hide');
            } else {
                $('#u-block-reason').parent().parent().removeClass('hide');
                if(status === editStatus.data.userTemporaryBlocked) $('#u-block-to').parent().parent().removeClass('hide');
                else $('#u-block-to').parent().parent().addClass('hide');
            }
            
            $.ajax({
                type: 'POST',
                url:  '/admin/user/updateStatus',
                dataType: 'json',
                data:{
                    id: $(this).attr('userId')
                },
                success: function(response) {
                    $('#u-block-reason').val(response.message);
                    $('#u-block-to').val(response.date);
                    $('.u-block').removeClass('hide');
            }});
        });
    },
    updateData : function() { 
        $('#u-block-status').change(function() {
            var status = $(this).val();
            if(status != editStatus.data.userNotConfirmed && status != editStatus.data.userActive) {
                $('#u-block-reason').parent().parent().removeClass('hide');
                if(status == editStatus.data.userTemporaryBlocked) {
                    $('#u-block-to').val(editStatus.data.nextDate);
                    $('#u-block-to').parent().parent().removeClass('hide');
                } else $('#u-block-to').parent().parent().addClass('hide');
                
                if(status == editStatus.data.userWarning) $('img[userid=' + $('#u-block-id').val() + ']').attr('src', '/images/ico-no-blocked.png');
                else $('img[userid=' + $('#u-block-id').val() + ']').attr('src', '/images/ico-blocked.png');
            } else {
                $('#u-block-to').parent().parent().addClass('hide');
                $('#u-block-reason').parent().parent().addClass('hide');
                
                $('img[userid=' + $('#u-block-id').val() + ']').attr('src', '/images/ico-no-blocked.png');
            }
        });
    }
};