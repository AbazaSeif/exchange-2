var rateList = {
    init : function(){
        this.container = $("#rates");
        var element = $( "#rate-price" );
        
        //rateList.data.socket.on('init', function (data) {
            //var element = rateList.createElement(data.date, data.name, data.price, data.surname);
            // $('#rates').prepend(element);
        //});
        
        rateList.data.socket.on('setRate', function (data) {
            var initPrice = parseInt($('#rate-price').attr('init'));
            if(data.transportId == rateList.data.transportId){
                var element = rateList.createElement(initPrice, data.date, data.name, data.price, data.surname);
                //console.log($('#rates'));
                //$('#rates').append(element);
                $('#rates').prepend(element);
                //$(element).appendTo('#rates');
            }
        });
        
        rateList.data.socket.on('loadRates', function (data) {
            //console.log(data.date);
            var obj = {
                price: data.price,
                time: data.date,
                name: data.name,
                surname: data.surname,
                //userId: rateList.data.userId,
                //transportId: rateList.data.transportId
            };
            rateList.add(obj);
        });
        
        rateList.data.socket.on('errorRate', function (data) {
            var error = $('#t-error');
            error.css('display', 'block');
            error.html('Ставка с ценой "' + data.price + '" уже была сделана');
        });
        
        rateList.data.socket.on('onlineEvent', function (data) {
            //$.onlineEvent({ msg:'Вашу ставку для перевозки "' + data.name + '" перебили',className: 'classic', sticked:true, position:{right:0,bottom:0}, time:2000});
            //console.log(111);
            $.onlineEvent({ msg : data.msg, className : 'classic', sticked:true, position:{right:0,bottom:0}, time:10000});
        });
        
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
            $('#addRate').dialog('close');
            $('.r-submit').addClass('disabled');
            $('#rate-up').addClass('disabled');  
            
            if(rateList.data.defaultRate) $('#rates').html('');
            $('#t-error').html('');
            
            var price = parseInt($('#rate-price').val());
            var price = price*100/(100 + rateList.data.nds*100);
            $(this).attr('init', price);
            var time = getTime();
            var obj = {
                price: price,
                date: time,
                name: rateList.data.name,
                surname: rateList.data.surname,
                userId: rateList.data.userId,
                transportId: rateList.data.transportId
            };
            
            //rateList.add(obj);
            //console.log(getTime());
            //rateList.update(this.container, price, rateList.data.name);
            
            rateList.data.socket.emit('setRate',{
                transportId: rateList.data.transportId,
                date: time,
                userId: rateList.data.userId,
                name : rateList.data.name, 
                surname: rateList.data.surname,
                price : price,
            });   
        });
        
        $('#rate-price').blur(function(){
            var inputVal = parseInt($(this).val());

            if(inputVal < parseInt($(this).attr('init'))){ 
                var kratnoe = rateList.data.priceStep;
                var residue = inputVal % kratnoe;
                if(residue != 0){
                    if(residue < (kratnoe/2) && (inputVal - residue) > 0) $(this).val(inputVal - residue);
                    else $(this).val(inputVal - residue + kratnoe);
                    inputVal = parseInt($(this).val());
                }

                if(inputVal - kratnoe < kratnoe){
                    $('#rate-down').addClass('disabled');
                }
                
                if(inputVal < parseInt($(this).attr('init'))){
                    $('#rate-up').removeClass('disabled');
                    $('.r-submit').removeClass('disabled');
                }
            } else {
                $(this).val($(this).attr('init'));
                if(!rateList.data.defaultRate) $('.r-submit').addClass('disabled');
            }
        });

        $(document).keypress(function(e) {
            if (e.which == 13) {
                $( "#rate-price" ).trigger('blur');
            }
        });

        //rateList.load(this.container);        
    },
    // ----
    //update : function(posts, price) {
    update : function(posts, price, userName) {
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
                    if(rates.all.length) {
                        rateList.data.socket.emit('setRate',{
                             userName : userName, 
                             price : price
                        });   
                    } else {
                        rateList.container.html('<span>Нет предложений</span>');
                    }
            }});
        }
    },
    load : function(posts) {
        if (this.container.length > 0) {
            $.ajax({
                type: 'POST',
                url: '/transport/updateRates',
                dataType: 'json',
                data:{
                    id: this.data.transportId,
                    newRate: '',
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
                        var initPrice = parseInt($('#rate-price').attr('init'));
                        $.each( rates.all, function( key, value ) {
                            rateList.add(value, initPrice);
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
    add : function(rate, initPrice) {
        var time = '';
        var id = 0;
        var price = parseInt(rate.price);
        price = Math.ceil(price + price * this.data.nds);
        if (typeof rate.id !=="undefined") id = rate.id;
        
        /*
        if (typeof rate.time !=="undefined") {
            time = "<div class='r-o-time'>" + rate.time + "</div>";
        }
        */
        var element = this.createElement(initPrice, rate.time, rate.name, price, rate.surname, id);
        
        this.container.prepend(element);
    },
    createElement : function(initPrice, date, name, price, surname, id) {
        if(initPrice < price){
            $('#rate-price').attr('init', price);
        }
        var newElement = "<div class='rate-one'>";
        if(typeof id !== 'undefined'){
            newElement = "<div id='" + id + "' class='rate-one'>";
        } 
        
        newElement += "<div class='r-o-container'>" + 
                "<span>" + date + "</span>" + 
                "<div class='r-o-user'>" + name + ' ' + surname + "</div>" +
            "</div>" +
            "<div class='r-o-price'>" + price + rateList.data.currency + "</div>"
            
        ;
        newElement += "</div>";
        
        return newElement;
    },
    getContainerHeight : function(){
        var h=0;
        this.container.find('.rate-one').each(function(k){
            h += $(this).outerHeight();
        });
        return h;
    }
};