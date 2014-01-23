var rateList = {
    init : function(){
        this.container = $("#rates");
        var element = $( "#rate-price" );
        $( "#rate-up" ).click(function(){
            if(!$(this).hasClass('disabled')){
                $( "#rate-down" ).removeClass('disabled');
                var newRate = parseInt(element.val()) + rateList.data.priceStep;
                if(newRate <= element.attr('init')) element.val(newRate);

                if (parseInt(element.attr('init')) == element.val()) $(this).addClass('disabled');
                else $(this).removeClass('disabled');
            }
        });

        $( "#rate-down" ).click(function() {
            if(!$(this).hasClass('disabled')) {
                var newRate = parseInt(element.val()) - rateList.data.priceStep; 
                if(newRate > 0) element.val(newRate);
                else $(this).addClass('disabled');
                
                if(element.val() <= element.attr('init')) 
                     $( "#rate-up" ).removeClass('disabled');
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
                    if(rates.all.length){
                        rateList.container.html('');
                        $.each( rates.all, function( key, value ) {
                            rateList.add(value);
                        });
                        
                        if(rates.price){
                            var value = parseInt(rates.price) - parseInt(rateList.data.priceStep);
                            var prevValue = value - parseInt(rateList.data.priceStep);
                            var price = $("#rate-price");
                            if(price.val() > value && value > 0) price.val(value);
                            if(prevValue < 0) $( "#rate-down" ).addClass('disabled');
                            price.attr('init', value);
                            $('#last-rate').html(rates.price + rateList.data.currency);
                        }
                    } else {
                        rateList.container.html('Сделайте ваше предложение');
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