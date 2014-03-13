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
        /*$( "#Transport_date_from" ).datepicker({
            dateFormat: 'dd-mm-yy',
        });

        $( "#Transport_date_to" ).datepicker({
            dateFormat: 'dd-mm-yy',
        });
		*/
		
		$( "#Transport_date_from" ).datetimepicker({
            dateFormat: 'dd-mm-yy',
            timeFormat: 'HH:mm',
        });

        $( "#Transport_date_to" ).datetimepicker({
            dateFormat: 'dd-mm-yy',
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
            var parent = $(this).parent();
            if (newVal == '') newVal = parent.attr('pval');
            parent.text(newVal);
            parent.next().val(newVal);
            $(this).remove(); 
        });
        
        $("#rates-all").on('click', 'li span.del-row', function () {
            $(this).parent().remove();
        });
        /*************************************/
        $("#points-all").on('dblclick', 'li span.p-point', function () {     
            $(this).parent().addClass("clicked");
            var origPrice = $(this).text();
            $(this).attr('pval', origPrice)
            $(this).text("");
            $('<input>', {
                type: 'text',
                value: origPrice,
            }).appendTo(this).focus();
        });
        
        $("#points-all").on('focusout', 'li span.p-point > input', function () {
            var newVal = $(this).val();
            var parent = $(this).parent();
            if (newVal == '') newVal = parent.attr('pval');
            parent.text(newVal);
            parent.next().val(newVal);
            $(this).remove(); 
        });
        
        $("#points-all").on('click', 'li span.del-row', function () {
            $(this).parent().remove();
        });
        
        /* press Enter button*/
        $(document).keypress(function(e) {
            if(e.which == 13) {
                var element = $( ".clicked" );
                element.find( "input" ).focusout();
                element.removeClass("clicked");
            }
        });
    };
}
    