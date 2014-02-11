var rateList = {
    init : function(){
        this.container = $("#rates");
        var element = $( "#rate-price" );
        $( "#rate-up" ).on('click', function() {
            if(!$(this).hasClass('disabled')){
                $( "#rate-down" ).removeClass('disabled');
                var price = $('#rate-price');
                if(parseInt(price.val()) < parseInt(price.attr('init'))){
                   $( ".r-submit" ).removeClass('disabled');
                } else $( ".r-submit" ).addClass('disabled');
                var newRate = parseInt(element.val()) + rateList.data.priceStep + rateList.data.priceStep * rateList.data.nds;
                if(newRate <= element.attr('init')) element.val(newRate);
                
                if (parseInt(element.attr('init')) == element.val()) {
                    $(this).addClass('disabled');
                    $( ".r-submit" ).addClass('disabled');
                }
                else $(this).removeClass('disabled');
            }
        });
        
        $( "#rate-up" ).mousedown(function(e) {
            clearTimeout(this.downTimer);
            this.downTimer = setInterval(function() {
                $( "#rate-up" ).trigger('click');                
            }, 150);
        }).mouseup(function(e) {
            clearInterval(this.downTimer);
        });

        $( "#rate-down" ).on('click', function() {
            if(!$(this).hasClass('disabled')) {
                $( "#rate-up" ).removeClass('disabled');                
                var step = rateList.data.priceStep + rateList.data.priceStep * rateList.data.nds;
                var newRate = element.val() - step; 
                if(newRate > 0) element.val(newRate);
                
                var price = $('#rate-price');
                if(parseInt(price.val()) < parseInt(price.attr('init'))){
                   $( ".r-submit" ).removeClass('disabled');
                } else {
                   //console.log(parseInt(price.val()) +'>'+ parseInt(price.attr('init')));
                   $( ".r-submit" ).addClass('disabled');
                }
                
                if( (newRate - step) <= 0 ) {
                    $(this).addClass('disabled');
                }
            }
        });
        
        $( "#rate-down" ).mousedown(function(e) {
            clearTimeout(this.downTimer);
            this.downTimer = setInterval(function() {
                $( "#rate-down" ).trigger('click');                
            }, 150);
        }).mouseup(function(e) {
            clearInterval(this.downTimer);
        });

        $( ".r-submit" ).click(function() {
            if(!$(this).hasClass('disabled')) {
                $('#setPriceVal').text(parseInt($( "#rate-price" ).val()));
                $("#addRate").dialog("open");
            }
        });
        
        $('#setRateBtn').live('click', function() {
            $("#addRate").dialog("close");
            $('.r-submit').addClass('disabled');
            console.log(rateList.data.defaultRate);
            if(rateList.data.defaultRate) $('#rates').html('');
            $('#t-error').html('');
            var price = parseInt($( "#rate-price" ).val());
            var price = price*100/(100 + rateList.data.nds*100);
            var obj = {
                price: price,
                name: rateList.data.name,
                surname: rateList.data.surname,
            };
            rateList.add(obj);
            rateList.update(this.container, price);
        });
        
        $('#rate-price').blur(function(){
            if(parseInt($(this).val()) < parseInt($(this).attr('init'))){
                $('.r-submit').removeClass('disabled');
            } else {
                $('.r-submit').addClass('disabled');
            }
        });

        $(document).keypress(function(e) {
            if (e.which == 13) {
                $( "#rate-price" ).trigger('blur');
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
                            var value = parseInt(rates.price);// - (rateList.data.priceStep + rateList.data.priceStep * rateList.data.nds);
                            if(rateList.data.nds){
                               value += value * rateList.data.nds;
                            }
                            var step = rateList.data.priceStep + rateList.data.priceStep * rateList.data.nds;
                            
                            var price = $("#rate-price");
                            if(price.val() > value && value > 0) {
                                price.val(value);
                                price.attr('init', value);
                                $( "#rate-up" ).addClass('disabled');
                            }
                            
                            var prevValue = value - (rateList.data.priceStep + rateList.data.priceStep * rateList.data.nds);         
                            if(prevValue < 0) $( "#rate-down" ).addClass('disabled');
                            $('#last-rate').html('<span>' + rates.price + rateList.data.currency + '</span>');

                            if(prevValue <= 0) {
                                $('.r-submit').addClass('disabled');
                                $('.r-block').slideUp("slow");
                                $('.r-submit').slideUp("slow");
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
        var time = '';
        var id = 0;
        var price = parseInt(rate.price);
        price = Math.ceil(price + price * this.data.nds);
        if (typeof rate.id !=="undefined") id = rate.id;
        
        if (typeof rate.id !=="undefined"){
            time = "<div class='r-o-time'>" + rate.time + "</div>";
        }
        var newElement = "<div id='" + id + "' class='rate-one'>" + 
            "<div class='r-o-container'>" + 
                time +
                "<div class='r-o-user'>" + rate.name + ' ' + rate.surname + "</div>" +
            "</div>" +
            "<div class='r-o-price'>" + price + rateList.data.currency + "</div>" +
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