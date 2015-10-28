module.exports = function() {
  return {
    init: function(dateNow, endDate) {
       this.dateNow = new Date(dateNow);
       this.endDate = new Date(endDate); // дата и время от которых идет обратный отсчет
       this.numOfDays = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ]; // установили количество дней для месяцев
       this.borrowed = 0;   //заимствованные
       this.years = 0, this.months = 0, this.days = 0;
       this.hours = 0, this.minutes = 0, this.seconds = 0;
       this.updateNumOfDays(); // устанавливает количество дней в феврале текущего года
       //return this.updateCounter();
    },
    updateNumOfDays: function() {
      var dateNow = this.dateNow;
      var currYear = dateNow.getFullYear();
      if ( (currYear % 4 == 0 && currYear % 100 != 0) || currYear % 400 == 0 ) {
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
      this.days = this.datePartDiff(currDate.getDate(), futureDate.getDate(), this.numOfDays[currDate.getMonth()]);
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
    updateCounter: function(){
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
                years = this.years + title;
            }
            if(this.months > 0) {
                var title = 'месяцев';
                var modulo = this.months%10;
                if(this.months == 1) {
                    title = 'месяц';
                } else if(this.months == 2 || this.months == 3 || this.months == 4) {
                    title = 'месяца';
                }
                months = this.months + title;
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
                days = this.days + title;
            }

            var total = years + months + days + ' ' + this.hours + ':' + this.minutes + ':' + this.seconds;

            if ( this.endDate > this.dateNow ) { //проверка не обнулился ли таймер
                var self = this;
                setTimeout(function(){self.updateCounter();}, 1000);
            }

            return total;
      }
  };
}



////module.exports = function(){
//  return {
//    init: function(serverDate, initDate) {
//      this.dateNow = new Date(serverDate);
//      this.endDate = new Date(initDate); // дата и время от которых идет обратный отсчет
//      this.minUpdate = 1000;
//      if ($(this.str).length > 0) {
//          this.numOfDays = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ]; // установили количество дней для месяцев
//          this.borrowed = 0;   //заимствованные
//          this.years = 0, this.months = 0, this.days = 0;
//          this.hours = 0, this.minutes = 0, this.seconds = 0;
//          this.updateNumOfDays(); // устанавливает количество дней в феврале текущего года
//          return this.updateCounter();
//      }
//    },
//    // устанавливает количество дней в феврале текущего года
//    updateNumOfDays: function() {
//      var dateNow = this.dateNow;
//      var currYear = dateNow.getFullYear();
//      if ( (currYear % 4 == 0 && currYear % 100 != 0) || currYear % 400 == 0 ) {
//          this.numOfDays[1] = 29; //кол-во дней в феврале высокосного года
//      }
//      var self = this;
//      setTimeout(function(){self.updateNumOfDays();}, (new Date((currYear + 1), 0, 1) - dateNow)); // количество дней в феврале будет проверено через год 1 января //////(было 2 февраля)
//    },
//    datePartDiff: function(now, then, MAX){ //cur_seconds, end_seconds, max
//      var diff = then - now - this.borrowed;
//      this.borrowed = 0;
//      if ( diff > -1 ) return diff; // разница > или = 0
//      this.borrowed = 1;
//      return (MAX + diff);
//    },
//    calculate: function(){
//      var futureDate = this.endDate;
//      this.dateNow.setSeconds(this.dateNow.getSeconds() + 1);
//      var currDate = this.dateNow;
//      this.seconds = this.datePartDiff(currDate.getSeconds(), futureDate.getSeconds(), 60);
//      this.minutes = this.datePartDiff(currDate.getMinutes(), futureDate.getMinutes(), 60);
//      this.hours = this.datePartDiff(currDate.getHours(), futureDate.getHours(), 24);
//      this.days = this.datePartDiff(currDate.getDate(), futureDate.getDate(), this.numOfDays[currDate.getMonth()]);
//      this.months = this.datePartDiff(currDate.getMonth(), futureDate.getMonth(), 12);
//      this.years = this.datePartDiff(currDate.getFullYear(), futureDate.getFullYear(),0);
//    },
//    addLeadingZero: function(value){
//      return value < 10 ? ("0" + value) : value;
//    },
//    formatTime: function(){
//      this.seconds = this.addLeadingZero(this.seconds);
//      this.minutes = this.addLeadingZero(this.minutes);
//      this.hours = this.addLeadingZero(this.hours);
//    },
//    updateCounter: function(){
//         if ($(this.str).length > 0) {
//            this.calculate();
//            this.formatTime();
//            var years = months = days = hours = minutes = seconds = '';
//            if(this.years > 0) {
//                var title = 'лет';
//                if(this.years == 1) {
//                    title = 'год';
//                } else if(this.years == 2 || this.years == 3 || this.years == 4) {
//                    title = 'года';
//                }
//                years = this.years + title;
//            }
//            if(this.months > 0) {
//                var title = 'месяцев';
//                var modulo = this.months%10;
//                if(this.months == 1) {
//                    title = 'месяц';
//                } else if(this.months == 2 || this.months == 3 || this.months == 4) {
//                    title = 'месяца';
//                }
//                months = this.months + title;
//            }
//            if(this.days > 0) {
//                var title = 'дней';
//                var modulo = this.days%10;
//                var intPart = Math.floor(this.days/10);
//                if(modulo == 1 && intPart != 1) {
//                    title = 'день';
//                } else if((modulo == 2 || modulo == 3 || modulo == 4) && intPart != 1) {
//                    title = 'дня';
//                }
//                days = this.days + title;
//            }
//
//            var total = years + months + days + ' ' + this.hours + ':' + this.minutes + ':' + this.seconds;
//
//            if ( this.endDate > this.dateNow ) { //проверка не обнулился ли таймер
//                var self = this;
//                setTimeout(function(){self.updateCounter();}, 1000);
//            }
//
//            return total;
//         }
//      }
//   }
//};