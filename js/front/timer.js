var Timer = function(){};
Timer.prototype = {
  //initialize: function(initDate, id){
  init: function(serverDate, initDate, id){
    var dateNow = new Date(); // on pk
    var dateNowServer = new Date(serverDate); // on server
	this.timeDiff = (dateNowServer - dateNow)/1000; // сек, временная разница между сервером и клиентом 
	
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
	//console.log(dateNow);
    dateNow.setSeconds(dateNow.getSeconds() + this.timeDiff);
    //console.log('posle = ' + dateNow);
    //console.log(' = ' + dateNow.getHours());
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
		    years = "<strong>" + this.years + "</strong><small>" + title + "</small>";
		}
		if(this.months > 0) {
		    var title = 'месяцев';
			if(this.months == 1) {
			    title = 'месяц';
			} else if(this.months == 2 || this.months == 3 || this.months == 4) {
			    title = 'месяца';
			}
		    months = "<strong>" + this.months + "</strong> <small>" + title + "</small>";
		}
		if(this.days > 0) {
		    var title = 'дней';
			if(this.days == 1) {
			    title = 'день';
			} else if(this.days == 2 || this.days == 3 || this.days == 4) {
			    title = 'дня';
			}
		    days = "<strong>" + this.days + "</strong> <small>" + title + "</small>";
		}
		
		this.countainer.innerHTML = years + months + days + ' ' + this.hours + ':' + this.minutes + ':' + this.seconds;
		var currDate = new Date();
		currDate.setSeconds(currDate.getSeconds() + this.timeDiff);
		if ( this.endDate > currDate) { //проверка не обнулился ли таймер
			var self = this;
			setTimeout(function(){self.updateCounter();}, 1000);
		} else {
			this.countainer.innerHTML = 'Перевозка закрыта';
		}
	}
   }
};
//});

/*window.onload = function() {
	var myClassObject = new Timer();
    myClassObject.init('January 02, 2100 00:00:00', 'counter');
}*/
