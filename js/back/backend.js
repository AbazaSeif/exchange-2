$(function() {
    // Delete transport
    $("#delete-transport").click(function() {
        $("#delTr").dialog("open");
    });
   
    $('.ui-widget-overlay').live('click', function() {
        $('#delete-reason').val('');
        $('#delTr').dialog('close');
    });
    
    $('#abordDelTr').live('click', function() {
        $('#delete-reason').val('');
        $('#delTr').dialog('close');
    });
    
    $('#setDelTr').live('click', function() {
        var reasonField = $('#delete-reason');
        var reason = $.trim(reasonField.val());
        if(!reason) {
            reasonField.css('border-color', 'red');
        } else {
            $.redirect('/admin/transport/deletetransport/', {'id': reasonField.attr('trId'), 'reason': reasonField.val()});
        }
    });    
});