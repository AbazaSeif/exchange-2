var rateList = {
    init : function(){
        this.container = $("#rates");
        var element = $( "#rate-price" );
        $( "#rate-up" ).on('click', function() {
            if(!$(this).hasClass('disabled')){
                $( "#rate-down" ).removeClass('disabled');
                var newRate = parseInt(element.val()) + rateList.data.priceStep;
                if(newRate <= element.attr('init')) element.val(newRate);

                if (parseInt(element.attr('init')) == element.val()) $(this).addClass('disabled');
                else $(this).removeClass('disabled');
            }
        });
        
        $( "#rate-up" ).mousedown(function(e) {
            clearTimeout(this.downTimer);
            this.downTimer = setInterval(function() {
                $( "#rate-up" ).trigger('click');                
            }, 1000);
        }).mouseup(function(e) {
            clearInterval(this.downTimer);
        });

        $( "#rate-down" ).on('click', function() {
            if(!$(this).hasClass('disabled')) {
                if(element.val() <= element.attr('init')) 
                    $( "#rate-up" ).removeClass('disabled');
                var newRate = element.val() - rateList.data.priceStep; 
                if(newRate > 0) element.val(newRate);
                if( (newRate - rateList.data.priceStep) <= 0 ) {
                    $(this).addClass('disabled');
                }
            }
        });
        
        $( "#rate-down" ).mousedown(function(e) {
            clearTimeout(this.downTimer);
            this.downTimer = setInterval(function() {
                $( "#rate-down" ).trigger('click');                
            }, 1000);
        }).mouseup(function(e) {
            clearInterval(this.downTimer);
        });

        $( "#rate-btn" ).click(function() {
            if(!$(this).hasClass('disabled')) {
                $('#t-error').html('');
                rateList.update(this.container, $( "#rate-price" ).val());
            }
        });

        rateList.update(this.container);
    },
    update : function(posts, price) {
        if (this.container.length > 0) {
            price = typeof price !== 'undefined' ? price : '';
            $.ajax({
                type: 'POST',
                url: '/transport/updateRates',
                dataType: 'json',
                data:{
                    id: this.data.transportId,
                    newRate: price,
                    step: this.data.step,
                },
                success: function(rates) {
                    if(rates.error) {
                        var error = $('#t-error');
                        error.css('display', 'block');
                        error.html('Ставка с ценой "' + $( "#rate-price" ).val() + '" уже была сделана');
                    }
                    
                    if(rates.all.length) {
                        var container = $("#rates");
                        var height = 49;
                        var count = 0;
                        var scrollBefore = container.scrollTop();
                        if(scrollBefore) count = scrollBefore/height;
                        
                        rateList.container.html('');
                        $.each( rates.all, function( key, value ) {
                            rateList.add(value);
                        });

                        if(scrollBefore){
                            container.scrollTop(height * (count + 1));
                        }
                        
                        if(rates.price){
                            var value = parseInt(rates.price) - parseInt(rateList.data.priceStep);
                            var prevValue = value - parseInt(rateList.data.priceStep);
                            var price = $("#rate-price");
                            if(price.val() > value && value > 0) {
                                price.val(value);
                                $( "#rate-up" ).addClass('disabled');
                            }
                            if(prevValue < 0) $( "#rate-down" ).addClass('disabled');
                            price.attr('init', value);
                            
                            $('#last-rate').html('<span>' + rates.price + rateList.data.currency + '</span>');
                            if(value <= 0) {
                                $('#rate-btn').addClass('disabled');
                                $('.rate-btns').slideUp("slow");
                                $('#rate-btn').slideUp("slow");
                                $('#t-container').html('<span class="t-closed">Перевозка закрыта</span>');
                                rateList.data.status = true;
                            }
                        }
                    } else {
                        rateList.container.html('<span>Нет предложений</span>');
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
    },
    getContainerHeight : function(){
        var h=0;
        this.container.find('.rate-one').each(function(k){
            h += $(this).outerHeight();
        });
        return h;
    }
};