(function($) {
    $.onlineEvent = function(obj) {
        var obj = $.extend({
            time: 5000, 
            speed: 'slow', 
            msg: null, 
            id: null, 
            className: null, 
            evented: false,
            position:{ top:0,right:0 } 
        }, obj);

        var message = $('#online-event');
        //console.log('111 = ' + !message.length);		
        // if not exists
        /*if (!message.length) {
            $('body').prepend('<div id="online-event"></div>'); 
            var message = $('#online-event');
        }*/
        
        message.css('position', 'fixed').css({ right:'auto', left:'auto', top:'auto', bottom:'auto'}).css(obj.position);
        var event = $('<div class="event"></div>');
        message.append(event); 
        event.click(function(){ 
            event.fadeOut(obj.speed,function(){ 
                $(this).remove();
            });
        });
        if (obj.className) event.addClass(obj.className); 
        event.html(obj.msg);
        
       /* var exit = $('<div class="event-exit"></div>'); 
        event.prepend(exit);
        exit.click(function(){
            event.fadeOut(obj.speed,function(){
                $(this).remove();
            });
        });*/
        setTimeout(function(){ 
            event.fadeOut(obj.speed,function(){ 
                $(this).remove();
            });
        }, obj.time);
     };
})(jQuery);
