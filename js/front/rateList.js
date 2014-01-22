var rateList = {
    init : function(){
        this.container = $("#rates");
        var element = $( "#rate-price" );
        $( "btn-up" ).click(function(){
            if(!$(this).hasClass('disabled')){
                var newRate = parseInt(element.val()) + rateList.data.priceStep;
                if(newRate <= element.attr('init')) element.val(newRate);

                //button style
                if (parseInt(element.attr('init')) == element.val()) $(this).addClass('disabled');
                else $(this).removeClass('disabled');
            }
        });

        $( "btn-down" ).click(function() {
            if(element.val() <= element.attr('init')) 
                $( "btn-up" ).removeClass('disabled');
                
            if(!$(this).hasClass('disabled')) {
                var newRate = parseInt(element.val()) - rateList.data.priceStep; 
                if(newRate > 0) element.val(newRate);
            }
        });

        $( "#rate-btn" ).click(function() {
            if(!$(this).hasClass('disabled')) {
                rateList.update(this.container, $( "#rate-price" ).val());
            }
        });

        rateList.update(this.container);
    },
    update : function(posts, price) {
        if (this.container.length > 0) {
            price = typeof price !== 'undefined' ? price : '';
            //var currentScroll = this.container.scrollTop();
            //var startCount = this.container.find('.rate-one').length;
            
            $.ajax({
                type: 'POST',
                url: '/transport/updateRates',
                dataType: 'json',
                data:{
                    id: this.data.transportId,
                    newRate: price, 
                },
                success: function(rates) {
                    $.each( rates.all, function( key, value ) {
                        rateList.add(value);
                    });
                    
                    if(rates.price){
                        $("#rate-price").attr('init', rates.price);
                        $('#last-rate').html(rates.price + rateList.data.currency);
                    }
            }});
        }
    },
    add : function(rate) {
        var newElement = "<div id='" + rate.id + "' class='rate-one'>" +
            "<div class='r-o-container'>" +
                "<div class='r-o-time'>" + rate.time + "</div>" +
                "<div class='r-o-user'>" + rate.name + ' ' + rate.surname + "</div>" +
            "</div>" +
            "<div class='r-o-price'>" + rate.price + rateList.data.currency + "</div>" +
            "</div>"
        ;
        this.container.prepend(newElement);
    }
};