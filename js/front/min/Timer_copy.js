var Timer = function(){};
Timer.prototype = {
  init: function(serverDate, initDate, id, status, transportId) {
    this.dateNow = new Date(serverDate);
    this.endDate = new Date(initDate); // дата и время от которых идет обратный отсчет
    this.transportId = transportId;
    this.status = status;
    this.str = '#' + id;
    this.minUpdate = 10000;
    if ($(this.str).length > 0) {
        this.container = document.getElementById(id);
        this.numOfDays = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ]; // установили количество дней для месяцев
        this.borrowed = 0;   //заимствованные
        this.years = 0, this.months = 0, this.days = 0;
        this.hours = 0, this.minutes = 0, this.seconds = 0;
        this.updateNumOfDays(); // устанавливает количество дней в феврале текущего года
        this.updateCounter();
        var _this = this;
        // каждые 60 сек
        this.updateInterval = setInterval(function(){_this.reloadCounter();}, this.minUpdate);
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
  reloadCounter: function(){
        var _this = this;
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/transport/getCurTime',
            cache: false,
            data:{
                endDate: this.endDate,
            },
            success: function(response) {
                _this.dateNow = new Date(response.date);
                var futureDate = _this.endDate;
                _this.dateNow.setSeconds(_this.dateNow.getSeconds() + 1);
                var currDate = _this.dateNow;
                _this.seconds = _this.datePartDiff(currDate.getSeconds(), futureDate.getSeconds(), 60);
                _this.minutes = _this.datePartDiff(currDate.getMinutes(), futureDate.getMinutes(), 60);
                _this.hours = _this.datePartDiff(currDate.getHours(), futureDate.getHours(), 24);
                _this.days = _this.datePartDiff(currDate.getDate(), futureDate.getDate(), _this.numOfDays[futureDate.getMonth()]);
                _this.months = _this.datePartDiff(currDate.getMonth(), futureDate.getMonth(), 12);
                _this.years = _this.datePartDiff(currDate.getFullYear(), futureDate.getFullYear(),0);

                if(parseInt(response.minUpdate) != this.minUpdate) {
                    var self = _this;
                    _this.minUpdate = parseInt(response.minUpdate);
                    clearInterval(_this.updateInterval);
                    _this.updateInterval = setInterval(function(){self.reloadCounter();}, _this.minUpdate);  
                }
            }
        });
    
  },
  updateCounter: function(){
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
              $(".ui-dialog-content").dialog( "close" );
              if($('.r-submit').length) {
                  $('.r-submit').addClass('disabled');
                  $('.rate-wrapper').slideUp("slow");
              }    
              this.container.innerHTML = '<span class="t-closed"><img class="small-loading" src="/images/loading-small.gif"/>Обработка результатов</span>';
              
              $('#t-container').removeClass('open');
              var _this = this;
              clearInterval(this.updateInterval);
              console.log('stop');
              if(this.endDate < this.dateNow) {
                  refreshIntervalId = setInterval(function(){_this.addCloseLabel(_this.container, _this.transportId, refreshIntervalId);}, 5000);  
              } else { // this.endDate == this.dateNow
                  setTimeout(function(){_this.addCloseLabelWithDelay(_this.container, _this.transportId, _this.refreshIntervalId);}, 120000);
              }
              /********************/
              // Доп время
              // checkForAdditionalTimer(this.transportId, this.status, this.container);
          }
       }
    },  
    addCloseLabelWithDelay: function(container, transportId, refreshIntervalId) {
        var _this = this;
        refreshIntervalId = setInterval(function(){_this.addCloseLabel(container, transportId, refreshIntervalId);}, 5000);
    },
    addCloseLabel: function(container, transportId, refreshIntervalId) {
        var containerId = container.getAttribute('id');
        var index = containerId.indexOf('counter-');
        if(index > -1) id = containerId.substring(8);
        else id = transportId;
        $.ajax({
            type: 'POST',
            url: '/transport/checkForTransportStatus',
            dataType: 'json',
            data:{
                id: id,
            },
            success: function(response) {
               if(response == 0) {
                   if(containerId == 't-container') $('#'+containerId).removeClass('open');
                   container.innerHTML = '<span class="t-closed">Перевозка закрыта</span>';
                   if(typeof refreshIntervalId != 'undefined') clearInterval(refreshIntervalId);
                   /* hide transport from the list */
                   var parent = $('#'+containerId).parent().parent().parent();
                   if(parent.hasClass('transport')) parent.addClass('hide');
                   /* end hide transport */
               }
        }});
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
            if(response.end) {
                var timer = new Timer();
                timer.init(response.now, response.end, id, status, transportId);
                $('#'+id).addClass('add-t');
            } else {
                var label = $('#'+id);
                label.removeClass('add-t');
                if(id == 't-container') label.removeClass('open');
                /* hide transport from the list */
                var parent = label.parent().parent().parent();
                if(parent.hasClass('transport')) parent.addClass('hide');
                /* end hide transport */
                container.innerHTML = '<span class="t-closed">Перевозка закрыта</span>';
                if($('.r-submit').length) {
                    $('.r-submit').addClass('disabled');
                    $('.rate-wrapper').slideUp("slow");
                }
            }
    }});
}