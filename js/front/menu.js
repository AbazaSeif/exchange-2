var menu = {
    init : function(){
        //this.updateCounter();
        //setInterval(function(){menu.updateCounter()}, 5000);
        
        //null - если не активен пункт меню
		
        var activeElement = parseInt(sessionStorage.getItem('menu'));
        var activeSubElement = parseInt(sessionStorage.getItem('submenu'));
        
        if(activeElement != 'null') {
             if (this.countSubmenuElem != 'null') {
                if(activeElement >= this.countSubmenuElem) activeElement = activeElement + this.countSubmenuElem;
             }
             $('.user-menu li').eq(activeElement).find('a:first').addClass('menu-active');
        }
        
        if(activeSubElement != 'null') {
            activeSubElement = parseInt(activeSubElement) - 2;
            $('#submenu li').eq(activeSubElement).find('a:first').addClass('menu-active');
        } 
        
        $( "#submenu>li>a" ).click(function() {
            if(!$(this).hasClass('menu-active')) {
                $("a.menu-active").removeClass('menu-active');
                $(this).addClass('menu-active');
                sessionStorage.setItem('menu', null);
                sessionStorage.setItem('submenu', $(this).parents("li").index());
            }
        });
        
        $( ".user-menu>li>a" ).click(function() {
            if(!$(this).hasClass('menu-active')) {
                $( "a.menu-active" ).removeClass('menu-active');
                $(this).addClass('menu-active');
                sessionStorage.setItem('submenu', null);
                if(!$(this).hasClass('exit') && !$(this).hasClass('admin')) {
                    sessionStorage.setItem('menu', $(this).parents("li").index());
                } else {
                    sessionStorage.setItem('menu', null);
                }
            }
        });
    },
	
    /*updateCounter : function(){
        $.ajax({
            url: '/user/updateEventCounter',
            success: function(data){
            //$('#event-counter').html(data);
        }});
    }*/
};