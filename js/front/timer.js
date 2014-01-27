var Timer = function(){};
Timer.prototype = {
  init: function(serverDate, initDate, id, status){
    var dateNow = new Date(); // время на ПК
    var dateNowServer = new Date(serverDate); // время на сервере
	this.timeDiff = (dateNowServer - dateNow)/1000; // сек, временная разница между сервером и клиентом 
	this.status = status;
    this.endDate = new Date(initDate); // дата и время от которых идет обратный отсчет
	this.str = '#' + id;
	
	if ($(this.str).length > 0) {
		this.countainer = document.getElementById(id);
		this.numOfDays = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ]; // установили количество дней для месяцев
		this.borrowed = 0;   //заимствованные
		this.years = 0, this.months = 0, this.days = 0;
		this.hours = 0, this.minutes = 0, this.seconds = 0;
		this.updateNumOfDays(); // устанавливает количество дней в феврале текущего года
		this.updateCounter();
	}
  },
  updateNumOfDays: function() {
    var dateNow = new Date(); // on pk
    dateNow.setSeconds(dateNow.getSeconds() + this.timeDiff);
	var currYear = dateNow.getFullYear();
    if ( (currYear % 4 == 0 && currYear % 100 != 0 ) || currYear % 400 == 0 ) {
        this.numOfDays[1] = 29; //кол-во дней в феврале высокосного года
    }
    var self = this;
    setTimeout(function(){self.updateNumOfDays();}, (new Date((currYear + 1), 0, 1) - dateNow)); // количество дней в феврале будет проверено через год 1 января //////(было 2 февраля)
  },
  datePartDiff: function(then, now, MAX){ //cur_seconds, end_seconds, max
    var diff = now - then - this.borrowed;
    this.borrowed = 0;
    if ( diff > -1 ) return diff; // разница > или = 0
    this.borrowed = 1;
    return (MAX + diff);
  },
  calculate: function(){
    var futureDate = this.endDate;
    var currDate = new Date();
	currDate.setSeconds(currDate.getSeconds() + this.timeDiff);
    this.seconds = this.datePartDiff(currDate.getSeconds(), futureDate.getSeconds(), 60);
    this.minutes = this.datePartDiff(currDate.getMinutes(), futureDate.getMinutes(), 60);
    this.hours = this.datePartDiff(currDate.getHours(), futureDate.getHours(), 24);
    this.days = this.datePartDiff(currDate.getDate(), futureDate.getDate(), this.numOfDays[futureDate.getMonth()]); //0 ?????????
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
			if(this.months == 1) {
			    title = 'месяц';
			} else if(this.months == 2 || this.months == 3 || this.months == 4) {
			    title = 'месяца';
			}
		    months = "<span class='t-month'><strong>" + this.months + "</strong> " + title + " </span>";
		}
		if(this.days > 0) {
		    var title = 'дней';
			if(this.days == 1) {
			    title = 'день';
			} else if(this.days == 2 || this.days == 3 || this.days == 4) {
			    title = 'дня';
			}
		    days = "<span class='t-days'><strong>" + this.days + "</strong> " + title + " </span>";
		}
		
		this.countainer.innerHTML = years + months + days + ' <span class="t-time">' + this.hours + ':' + this.minutes + ':' + this.seconds + '</span>';
		var currDate = new Date();
		currDate.setSeconds(currDate.getSeconds() + this.timeDiff);

        if(typeof rateList.data !== "undefined" && typeof rateList.data.status !== "undefined") this.status = parseInt(rateList.data.status);
        if ( this.endDate > currDate && this.status ) { //проверка не обнулился ли таймер
			var self = this;
            setTimeout(function(){self.updateCounter();}, 1000);
		} else {
			this.countainer.innerHTML = '<span class="t-closed">Перевозка закрыта</span>';
		}
	}
   }
};