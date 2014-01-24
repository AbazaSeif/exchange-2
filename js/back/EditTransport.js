function ЕditTransport() {
    this.initCalendar = function() {
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
        $( "#date_from" ).datetimepicker({
            dateFormat: 'yy-mm-dd',
            timeFormat: 'HH:mm',
        });

        $( "#date_to" ).datetimepicker({
            dateFormat: 'yy-mm-dd',
            timeFormat: 'HH:mm',
        });
    };
    
    this.initRateEditor = function(){
        $("#rates-all").on('dblclick', 'li span.price', function () {     
            $(this).parent().addClass("clicked");
            var origPrice = $(this).text();
            $(this).attr('pval', origPrice)
            $(this).text("");
            $('<input>', {
                type: 'text',
                value: origPrice,
            }).appendTo(this).focus();
        });
        
        $("#rates-all").on('focusout', 'li span.price > input', function () {
            var newVal = $(this).val();
            if (newVal == '') newVal = $(this).parent().attr('pval');
            $(this).parent().text(newVal);
            $(this).remove(); 
        });
        
        /* press Enter button*/
        $(document).keypress(function(e) {
            if(e.which == 13) {
                var li = $( "li.clicked" );
                li.find( "input" ).focusout();
                li.removeClass("clicked");
            }
        });
    };
}
    