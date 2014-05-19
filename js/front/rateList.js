var rateList = {
    init : function(){
        this.container = $("#rates");
        var element = $( "#rate-price" );
        if(typeof(rateList.data.socket) !== 'undefined') {
            rateList.data.socket.on('setRate', function (data) {
                if(data.dateCloseNew)rateList.data.dateCloseNew = data.dateCloseNew;
                
                var initPrice = parseInt($('#rate-price').attr('init'));
                if (data.transportId == rateList.data.transportId) {
                    var price = data.price;
                    if(rateList.data.nds) {
                        price = Math.ceil(price * (100 + rateList.data.nds*100) / 100);
                    }
                    
                    var element = rateList.createElement(initPrice, data.date, price, '', data.company);
                    $('#rates').prepend(element);
                }
            });
            
            rateList.data.socket.on('loadRates', function (data) {
                $("#r-preloader").css('display', 'none');
                for(var j = 0; j < data.rows; j++) {
                    //
                    var obj = {
                        price: data.arr[j][1],
                        time: data.arr[j][2],
                        company: data.arr[j][3],
                        with_nds: 0,
                    };
                    rateList.add(obj);
                }
            });
            
            rateList.data.socket.on('errorRate', function (data) {
                $('#maxRateVal').text(parseInt(data.price));
                $("#errorRate").dialog("open");
            });
            
           /* rateList.data.socket.on('error', function (data) {
                $('#text').text('Произошла ошибка, пожалуйста перезагрузите страницу');
                $("#errorSocket").dialog("open");
            });*/

            /****** Сообщение *********/
            
            $( "#rate-up" ).on('click', function() {
                var newRate = parseInt(element.val()) + rateList.data.priceStep;
                if(newRate <= element.attr('init')) {
                    element.val(newRate);
                    if($('#rate-down').hasClass('disabled'))$('#rate-down').removeClass('disabled');
                }
                if(newRate + rateList.data.priceStep > element.attr('init')) $(this).addClass('disabled');
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
                var step = rateList.data.priceStep;
                var newRate = element.val() - step;
                if(newRate > 0) {
                    element.val(newRate);
                    if($('#rate-up').hasClass('disabled'))$('#rate-up').removeClass('disabled');
                }
                if( (newRate - step) <= 0 ) {
                    $(this).addClass('disabled');
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
                    $.ajax({
                        type: 'POST',
                        url: '/user/transport/checkStatus',
                        dataType: 'json',
                        data:{
                            //id: this.data.transportId,
                        },
                        success: function(response) {
                            if(response.allow) { 
                                $('#setPriceVal').text(parseInt($( "#rate-price" ).val()));
                                $("#addRate").dialog("open");
                            } else {
                                $('#curStatus').text(response.status);
                                $("#errorStatus").dialog("open");
                            }
                    }});
                   // $('#setPriceVal').text(parseInt($( "#rate-price" ).val()));
                   // $("#addRate").dialog("open");
                }
            });

            $('#setRateBtn').live('click', function() {
                //if(!troubleWithSocket) {
                    $('#addRate').dialog('close');

                    if(rateList.data.defaultRate) $('#rates').html('');
                    //$('#t-error').html('');

                    var price = parseInt($('#rate-price').val());
                    if(rateList.data.nds) {
                        price = price * 100/(100 + rateList.data.nds*100);
                    }

                    $(this).attr('init', price);

                    var time = getTime();

                    rateList.data.socket.emit('setRate',{
                        transportId: rateList.data.transportId,
                        dateClose : rateList.data.dateClose,
                        dateCloseNew : rateList.data.dateCloseNew,
                        userId: rateList.data.userId,
                        company: rateList.data.company,
                        price : price,
                    }); 
                //}
            });

            $('#rate-price').blur(function() {
                var inputVal = parseInt($(this).val());
                var maxVal = $(this).attr('init');
                var kratnoe = rateList.data.priceStep;
                if(inputVal > maxVal) $(this).val(maxVal);
                if(inputVal <= 0) $(this).val(kratnoe);
                
                var residue = inputVal % kratnoe;
                if(residue != 0) {
                    if((inputVal - residue) > 0) $(this).val(inputVal - residue);
                    else $(this).val(kratnoe);
                    inputVal = parseInt($(this).val());
                }

                if((parseInt($(this).val()) - kratnoe) <= 0) $('#rate-down').addClass('disabled');
                else $('#rate-down').removeClass('disabled');
                if((parseInt($(this).val()) + kratnoe) > $(this).attr('init')) {
                    $('#rate-up').addClass('disabled');
                } else $('#rate-up').removeClass('disabled');
            });

            $(document).keypress(function(e) {
                if (e.which == 13) {
                    $( "#rate-price" ).trigger('blur');
                }
            });
        } else { // load with ajax rates for admin and logist
            rateList.load(this.container);
        }        
    },
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
                        //rateList.container.after('<div id="no-rates">Нет предложений</div>');
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
                data: {
                    id: this.data.transportId,
                    newRate: '',
                    step: this.data.step,
                },
                success: function(rates) {
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
                        
                        if(rates.price) {
                            var value = parseInt(rates.price);// - (rateList.data.priceStep + rateList.data.priceStep * rateList.data.nds);
                            if(rateList.data.nds){
                               value += value * rateList.data.nds;
                            }
                            var step = rateList.data.priceStep + rateList.data.priceStep * rateList.data.nds;
                            
                            var price = $("#rate-price");
                        }
                    } else {
                        rateList.container.after('<div id="no-rates">Нет предложений</div>');
                    }
            }});
        }
    },
    add : function(rate, initPrice) {
        var time = '';
        var id = 0;
        var price = parseInt(rate.price);
        price = Math.ceil(price + price * this.data.nds);

        if (rate.id) id = rate.id;
        var element = this.createElement(initPrice, rate.time, price, id, rate.company, parseInt(rate.with_nds), parseInt(rate.price));
        
        this.container.prepend(element);
    },
    createElement : function(initPrice, date, price, id, company, nds, ratePrice) {
        var companyName = company;
        var pos = companyName.indexOf("(");
        if(pos > -1) companyName = companyName.substring(0, pos);
        if(initPrice < price) {
            $('#rate-price').attr('init', price);
        }
        var newElement = "<div class='rate-one'>";
        
        if(id) {
            newElement = "<div id='" + id + "' class='rate-one'>";
        }
        
        newElement += "<div class='r-o-container'>" + 
                "<span>" + date + "</span>" + 
                "<div class='r-o-user'>" + companyName;
        
        newElement += "</div>" +
            "</div>"
        ;
        
        if(nds){
            var withNds = Math.ceil(ratePrice + ratePrice * rateList.data.ndsValue);
            newElement += "<div class='price-container'>" + 
                "<div class='r-o-price'>" + price + rateList.data.currency + 
                "</div>" +
                "<div class='r-o-nds'>" + '(c НДС: ' + withNds + rateList.data.currency + ') '+ 
                "</div>" +
            "</div>";
        } else {
            newElement += "<div class='r-o-price'>" + price + rateList.data.currency + "</div>";
        }
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