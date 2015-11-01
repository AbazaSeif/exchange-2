$(window).load(function(){
    $(".items").mCustomScrollbar({
        scrollButtons:{
            enable:true
        }
    });
});
    
(function($){
    $('#dialog-connect').live('click', function() {
        $("#modalDialog").dialog("open");
    });

    $('.ui-widget-overlay').live('click', function() {
        $(".ui-dialog-content").dialog( "close" );
    });

    $( "#abordRateBtn" ).live('click', function() {
        $(".ui-dialog-content").dialog( "close" );
    });

    $( "#errorRate .btn" ).live('click', function() {
        $(".ui-dialog-content").dialog( "close" );
    });

    $( "#errorStatus .btn" ).live('click', function() {
        $(".ui-dialog-content").dialog( "close" );
    });
    $( "#closeRate .btn" ).live('click', function() {
        $(".ui-dialog-content").dialog( "close" );
    });
    $( "#errorSocket .btn" ).live('click', function() {
        $(".ui-dialog-content").dialog( "close" );
    });
})(jQuery);

