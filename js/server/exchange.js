var io = require('socket.io').listen(3000);
var allSockets = [];

function deleteFromArray(element) {
    position = allSockets.indexOf(element);
    allSockets.splice(position, 1);
}

io.sockets.on('connection', function (socket) {
    var fs = require("fs");
    //var file = "d:/server/domains/data/exchange.db"; 
    var file = "/var/www/vhosts/lbr.ru/httpdocs/data/exchange.db";
    var sqlite3 = require("sqlite3").verbose();
    var db = new sqlite3.Database(file);
    var arr = [];
    var name = [];
    var i = 0;
    var labelForHiddenCompanyNames = '****';

    socket.on('init', function (id, minNotyfy)
    {
        allSockets[id] = socket.id;
        seachNewEvents(id, minNotyfy);
        updateEventsCount(id);	
    });
	
    socket.on('disconnect', function() {
        deleteFromArray(socket.id);
    });
	
    /* ----- Update count of messages in frontend and search for online messages ----- */

    function seachNewEvents(id, minNotyfy)
    {
        setTimeout(function() {
            db.each('SELECT id, transport_id, type FROM user_event WHERE status = 1 and status_online = 1 and user_id = ' + id, function(err, event) {
                db.each('SELECT location_from, location_to FROM transport WHERE id = ' + event.transport_id, function(err, transport) {	              
                    if (id in allSockets) { // user online
                        var message = 'Перевозка "<a href="http://exchange.lbr.ru/transport/description/id/' + event.transport_id + '">' + transport.location_from + ' &mdash; ' + transport.location_to + '</a>" была закрыта';
                        if(event.type == 2) message = 'Перевозка "<a href="http://exchange.lbr.ru/transport/description/id/' + event.transport_id + '">' + transport.location_from + ' &mdash; ' + transport.location_to + '</a>" будет закрыта через ' + minNotyfy + ' минут';
                        if(event.type == 3) message = 'Создана новая международная перевозка "<a href="http://exchange.lbr.ru/transport/description/id/' + event.transport_id + '">' + transport.location_from + ' &mdash; ' + transport.location_to + '</a>"';
                        if(event.type == 4) message = 'Создана новая региональная перевозка "<a href="http://exchange.lbr.ru/transport/description/id/' + event.transport_id + '">' + transport.location_from + ' &mdash; ' + transport.location_to + '</a>"';
                        
                        io.sockets.socket(socket.id).emit('onlineEvent', {
                            msg : message
                        });
                    }
					
                    var stmt = "UPDATE user_event set status_online = 0 WHERE id = " + event.id;
                    db.run(stmt);
                    seachNewEvents(id, minNotyfy);
                });
            });
        }, 2000);
    }
	
    function updateEventsCount(id)
    {
        setTimeout(function() {
            db.each("SELECT status FROM user_event WHERE user_id = " + id + " and status = 1", function(err, row) {
            }, function(err, rows) {
                io.sockets.socket(socket.id).emit('updateEvents', {
                    count : rows
                });
            });
            updateEventsCount(id)
        }, 200);
    }
    
    /* ----- Rates ----- */
	
    function getDateTime(inputDate) 
    {
        var date = new Date();
        if (typeof inputDate != 'undefined') date = new Date(inputDate);
        
        var hour = date.getHours();
        hour = (hour < 10 ? "0" : "") + hour;
        var min  = date.getMinutes();
        min = (min < 10 ? "0" : "") + min;
        var sec  = date.getSeconds();
        sec = (sec < 10 ? "0" : "") + sec;
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        month = (month < 10 ? "0" : "") + month;
        var day  = date.getDate();
        day = (day < 10 ? "0" : "") + day;
		
        return year + "-" + month + "-" + day + " " + hour + ":" + min + ":" + sec;
    }
	
    function checkForAdditionalTimer(data) 
    {
        var interval = 3; 
        var maxInterval = 30;
        var newClose = '';

        var transportDateClose = new Date(data.dateClose);
        if(data.dateCloseNew) {
            transportDateClose = new Date(data.dateCloseNew);
        } 

        var rateTime = new Date();
        rateTime.setMinutes(rateTime.getMinutes() + interval);
        //rateTime.setHours(rateTime.getHours() + 1); // !!!! убрать 

        var transportDateCloseMax = new Date(data.dateClose);
        transportDateCloseMax.setMinutes(transportDateCloseMax.getMinutes() + maxInterval);

        if(rateTime.valueOf() >= transportDateClose.valueOf() && rateTime.valueOf() <= transportDateCloseMax.valueOf() ) {
                transportDateClose.setMinutes(transportDateClose.getMinutes() + interval);
                newClose = getDateTime(transportDateClose);
                var stmt = "UPDATE transport SET date_close_new = '" + newClose + "' WHERE id = " + data.transportId;
                db.run(stmt);
        }
        return newClose;
    }
    
    function showOnlineMessages(data) 
    {
	var allow = true;
	var interval = 10;
	var now = new Date();
        now.setMinutes(now.getMinutes() + interval);
	//now.setHours(now.getHours() + 1); // !!!! убрать 
		
	var transportDateClose = new Date(data.dateClose);
        if(now.valueOf() >= transportDateClose.valueOf()) {
	    allow = false;
        }
        
        return allow;
    }
    
    /* Load all rates when open transport page in the first time  */
    socket.on('loadRates', function (id, t_id, show) {
        db.serialize(function() {
            if(show){
	            db.each("SELECT rate.user_id, rate.price, rate.date, user.company as company FROM rate JOIN user WHERE user.id = rate.user_id and rate.transport_id = " + t_id + " order by date", function(err, row) {
			arr[i] = new Array (row.user_id, row.price, row.date, row.company);
	                i++;
	            }, function(err, rows) {
	                io.sockets.socket(socket.id).emit('loadRates', {
	                    arr  : arr,
	                    rows : arr.length,
	                });
	            });
            } else {
                db.each("SELECT rate.user_id, rate.price, rate.date, user.company as company FROM rate JOIN user WHERE user.id = rate.user_id and rate.transport_id = " + t_id + " order by date", function(err, row) {
					var name = labelForHiddenCompanyNames;
					if(row.user_id == id) name = row.company;
					arr[i] = new Array (row.user_id, row.price, row.date, name);
					i++;
				}, function(err, rows) {
					io.sockets.socket(socket.id).emit('loadRates', {
					   arr  : arr,
					   rows : arr.length,
					});
				});
            }
        });
    });
	
    socket.on('setRateToServer', function (data) {
        db.each("SELECT start_rate, date_close, status, type, rate_id, location_from, location_to FROM transport WHERE id = " + data.transportId, function(err, row) { 
            var allow = true;
            if(parseInt(row.status) == 1) {
                if(parseInt(data.price) <= parseInt(row.start_rate)) {
                    var dateCloseNew = ''; //checkForAdditionalTimer(data);
                    var time = data.timedate; //getDateTime();
                    if(!time) {
                        time = getDateTime();
                        if(new Date(time) > new Date(row.date_close)){
                            allow = false;
                            io.sockets.socket(socket.id).emit('closeRate', {
                                response : 'Ставка имеет недопустимый параметр времени - нажмите сочетание клавиш Ctrl+F5 и сделайте ставку еще раз.',
                            });
                        }
                    } 
                    if (allow) {
                        if(row.rate_id) { // not null		
                            // check if it's min rate
                            db.each("SELECT min(price) as price, user_id FROM rate WHERE transport_id = " + data.transportId + " group by transport_id order by date desc", function(err, min) {
                                var showOnlineMessage = showOnlineMessages(data);
                                if(min.price > data.price && showOnlineMessage) {
                                    var stmt = db.prepare("INSERT INTO rate(transport_id, date, price, user_id) VALUES (?, ?, ?, ?)");
                                    stmt.run(data.transportId, time, data.price, data.userId);
                                    stmt.finalize();

                                    db.each("SELECT id FROM rate WHERE transport_id = " + data.transportId + " and price = " + data.price + " and user_id = " + data.userId, function(err, row) {
                                        var stmt = "UPDATE transport SET rate_id = " + row.id + " WHERE id = " + data.transportId;
                                        db.run(stmt);
                                    });

                                    // online message only if this rate is the minimal of all
                                    db.each("SELECT user_id FROM rate WHERE id = " + row.rate_id, function(err, user) {
                                        if (user.user_id in allSockets) { // user online
                                                io.sockets.socket(allSockets[user.user_id]).emit('onlineEvent', {
                                                        msg : 'Вашу ставку для перевозки ' + '"<a href="http://exchange.lbr.ru/transport/description/id/' + data.transportId + '">' + row.location_from + ' &mdash; ' + row.location_to + '</a>" перебили'
                                                });
                                        }

                                        var stmt = db.prepare("INSERT INTO user_event(user_id, transport_id, status, status_online, type, event_type, prev_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                                        stmt.run(user.user_id, data.transportId, 1, 0, 1, 5, min.user_id);
                                        stmt.finalize();
                                    });
                                } else {
                                    var stmt = db.prepare("INSERT INTO rate(transport_id, date, price, user_id) VALUES (?, ?, ?, ?)");
                                    stmt.run(data.transportId, time, data.price, data.userId);
                                    stmt.finalize();
                                }
                            });
                        } else { //first rate
                            var stmt = db.prepare("INSERT INTO rate(transport_id, date, price, user_id) VALUES (?, ?, ?, ?)");
                            stmt.run(data.transportId, time, data.price, data.userId);
                            stmt.finalize();

                            db.each("SELECT id FROM rate WHERE transport_id = " + data.transportId + " and price = " + data.price + " and user_id = " + data.userId, function(err, row) {
                                var stmt = "UPDATE transport SET rate_id = " + row.id + " WHERE id = " + data.transportId;
                                db.run(stmt);
                            });
                        }

                        // to sender
                        io.sockets.socket(socket.id).emit('setRate', {
                                company : data.company,
                                price : data.price,
                                date: time,
                                dateCloseNew: dateCloseNew,
                                transportId : data.transportId
                        });
                        // to all other
                        socket.broadcast.emit('setRate', {
                                company : labelForHiddenCompanyNames,
                                price : data.price,
                                date: time,
                                dateCloseNew: dateCloseNew,
                                transportId : data.transportId
                        });
                    }
                } else {
                    io.sockets.socket(socket.id).emit('errorRate', {
                        price : row.start_rate,
                    });
                }
            } else {
                io.sockets.socket(socket.id).emit('closeRate', {
                    response : 'Перевозка была закрыта, ставки больше не принимаются.',
                });
            }
        }, function(err, rows) {
            if (rows == 0) {
                io.sockets.socket(socket.id).emit('closeRate', {
                    response : 'Перевозка была удалена. Пожалуйста перезагрузите страницу.',
                });
            }
        });
    });
});
