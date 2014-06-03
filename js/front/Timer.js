var Timer = function(){};
Timer.prototype = {
  init: function(serverDate, initDate, id, status, transportId) {
    this.dateNow = new Date(serverDate);
    this.endDate = new Date(initDate); // дата и время от которых идет обратный отсчет
    //console.log(this.dateNow);
    //console.log(this.endDate);

    this.transportId = transportId;
    this.status = status;
    this.str = '#' + id;
    if ($(this.str).length > 0) {
        this.container = document.getElementById(id);
        this.numOfDays = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ]; // установили количество дней для месяцев
        this.borrowed = 0;   //заимствованные
        this.years = 0, this.months = 0, this.days = 0;
        this.hours = 0, this.minutes = 0, this.seconds = 0;
        this.updateNumOfDays(); // устанавливает количество дней в феврале текущего года
        this.updateCounter();
    }
  },
  // устанавливает количество дней в феврале текущего года
  updateNumOfDays: function() {
    var dateNow = this.dateNow;
    var currYear = dateNow.getFullYear();
    if ( (currYear % 4 == 0 && currYear % 100 != 0 ) || currYear % 400 == 0 ) {
        this.numOfDays[1] = 29; //кол-во дней в феврале высокосного года
    }
    var self = this;
    setTimeout(function(){self.updateNumOfDays();}, (new Date((currYear + 1), 0, 1) - dateNow)); // количество дней в феврале будет проверено через год 1 января //////(было 2 февраля)
  },
  datePartDiff: function(now, then, MAX){ //cur_seconds, end_seconds, max
    var diff = then - now - this.borrowed;
    this.borrowed = 0;
    if ( diff > -1 ) return diff; // разница > или = 0
    this.borrowed = 1;
    return (MAX + diff);
  },
  calculate: function(){
    var futureDate = this.endDate;
    this.dateNow.setSeconds(this.dateNow.getSeconds() + 1);
    var currDate = this.dateNow;
    this.seconds = this.datePartDiff(currDate.getSeconds(), futureDate.getSeconds(), 60);
    this.minutes = this.datePartDiff(currDate.getMinutes(), futureDate.getMinutes(), 60);
    this.hours = this.datePartDiff(currDate.getHours(), futureDate.getHours(), 24);
    this.days = this.datePartDiff(currDate.getDate(), futureDate.getDate(), this.numOfDays[futureDate.getMonth()]);
    this.months = this.datePartDiff(currDate.getMonth(), futureDate.getMonth(), 12);
    this.years = this.datePartDiff(currDate.getFullYear(), futureDate.getFullYear(),0);
  },
  addLeadingZero: function(value){
    return value < 10 ? ("0" + value) : value;
  },
  
  formatTime: function(){
    this.seconds = this.addLeadingZero(this.seconds);
    this.minutes = this.addLeadingZero(this.minutes);
    this.hours = this.addLeadingZero(this.hours);
  },
  
  updateCounter: function() {
      if ($(this.str).length > 0) {
          this.calculate();
          this.formatTime();
          var years = months = days = hours = minutes = seconds = '';
          if(this.years > 0) {
              var title = 'лет';
              if(this.years == 1) {
                  title = 'год';
              } else if(this.years == 2 || this.years == 3 || this.years == 4) {
                  title = 'года';
              }
              years = "<span class='t-year'><strong>" + this.years + "</strong> " + title + " </span>";
          }
          if(this.months > 0) {
              var title = 'месяцев';
              var modulo = this.months%10;
              if(this.months == 1) {
                  title = 'месяц';
              } else if(this.months == 2 || this.months == 3 || this.months == 4) {
                  title = 'месяца';
              }
              months = "<span class='t-month'><strong>" + this.months + "</strong> " + title + " </span>";
          }
          if(this.days > 0) {
              var title = 'дней';
              var modulo = this.days%10;
              var intPart = Math.floor(this.days/10);
              if(modulo == 1 && intPart != 1) {
                  title = 'день';
              } else if((modulo == 2 || modulo == 3 || modulo == 4) && intPart != 1) {
                  title = 'дня';
              }
              days = "<span class='t-days'><strong>" + this.days + "</strong> " + title + " </span>";
          }

          this.container.innerHTML = years + months + days + ' <span class="t-time">' + this.hours + ':' + this.minutes + ':' + this.seconds + '</span>';

          if(typeof rateList.data !== "undefined" && typeof rateList.data.status !== "undefined") this.status = parseInt(rateList.data.status);
          if ( this.endDate > this.dateNow && this.status ) { //проверка не обнулился ли таймер
              var self = this;
              setTimeout(function(){self.updateCounter();}, 1000);
          } else {
              /*this.container.innerHTML = '<span class="t-closed">Перевозка закрыта</span>';
              if($('.r-submit').length) {
                  $('.r-submit').addClass('disabled');
                  $('.rate-wrapper').slideUp("slow");
              }*/
              /********************/
              // открыть 2
              var id = this.container.getAttribute('id');
              //alert($('#'+id).hasClass('processing'));
              //var id = this.container.getAttribute('id');
              //console.log($('#'+id).hasClass('processing'));
              
              if($('#'+id).hasClass('processing') == 'true') checkForAdditionalTimer(this.transportId, this.status, this.container);
              else timerForProcessing(this.transportId, this.status, this.container);
          }
       }
    }
};

function checkForAdditionalTimer(transportId, status, container)
{
    var id = container.getAttribute('id');
    $.ajax({
         type: 'POST',
         url: '/transport/checkForAdditionalTimer',
         dataType: 'json',
         data:{
             id: transportId,
         },
         success: function(response) {
            $('#'+id).removeClass('processing');
            if(response.end) {
                var timer = new Timer();
                timer.init(response.now, response.end, id, status, transportId);
                $('#'+id).addClass('add-t');
            } else {
                $('#'+id).removeClass('add-t');
                // Hide transport from the list
                var parent = $('#'+id).parent().parent().parent();
                if(parent.hasClass('transport')) parent.addClass('hide');

                container.innerHTML = '<span class="t-closed">Перевозка закрыта</span>';
                if($('.r-submit').length) {
                    $('.r-submit').addClass('disabled');
                    $('.rate-wrapper').slideUp("slow");
                }
            }
    }});
}

function timerForProcessing(transportId, status, container)
{
    var limit = 60;
    var id = container.getAttribute('id');
    $('#'+id).addClass('processing');
    
    for (var i = limit; i > 0; i--) {
        if(limit > 0){
            var text = limit+' секунд';
            var modulo = limit%10;
            if(modulo == 1) text = limit+' секундa';
            else if(modulo == 2 || modulo == 3 || modulo == 4) text = limit+' секунды';
            else text = limit+' секунд';
            container.innerHTML = '<span class="t-time">'+text+'</span>';
            setTimeout(function(){timerForProcessing(transportId, status, container);}, 1000);
        } else {
            checkForAdditionalTimer(transportId, status, container);
        }
    }
    
    /*setTimeout(function(){
        limit -= 1;
        if(limit > 0){
            var text = limit+' секунд';
            var modulo = limit%10;
            if(modulo == 1) text = limit+' секундa';
            else if(modulo == 2 || modulo == 3 || modulo == 4) text = limit+' секунды';
            else text = limit+' секунд';
            container.innerHTML = '<span class="t-time">'+text+'</span>';
        } else {
            
            checkForAdditionalTimer(transportId, status, container);
        }
    }, 1000);*/
}