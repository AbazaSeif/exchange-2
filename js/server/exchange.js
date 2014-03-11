var io = require('socket.io').listen(3000);
var allSockets = [];
function deleteFromArray(element) {
    position = allSockets.indexOf(element);
    allSockets.splice(position, 1);
}

io.sockets.on('connection', function (socket) {
    var fs = require("fs");
    //var file = "d:/server/domains/data/exchange.db";
    //var file = "/../../../data/exchange.db";
    var file = "/var/www/vhosts/lbr.ru/httpdocs/data/exchange.db";
    var sqlite3 = require("sqlite3").verbose();
    var db = new sqlite3.Database(file);
    var arr = [];
    var name = [];
    var i = 0;

    socket.on('disconnect', function() {
        deleteFromArray(socket.id);
    });
	
    socket.on('init', function (id, minNotyfy) 
    {
        allSockets[id] = socket.id;
        /*
		db.each("SELECT site_deadline, site_before_deadline, site_transport_create_1, site_transport_create_2  FROM user_field WHERE user_id = " + id, function(err, option) {
            if(option.site_deadline)  seachNewEvents(id, 1);
            if(option.site_before_deadline) seachNewEvents(id, 2, minNotyfy);
            if(option.site_transport_create_1) seachNewEvents(id, 3);
            if(option.site_transport_create_2) seachNewEvents(id, 4);
        });
		*/
		
		/*seachNewEvents(id, 1);
		seachNewEvents(id, 2, minNotyfy);
		seachNewEvents(id, 3);
		seachNewEvents(id, 4);
		*/
		seachNewEvents(id, minNotyfy);
        updateEventsCount(id);	
    });
	
    /* ----- Update count of messages in frontend ----- */
	
    /*function seachNewEvents(id, type, minNotyfy)
    {
        setTimeout(function() {
            db.each('SELECT id, transport_id FROM user_event WHERE status=1 and user_id = ' + id + ' and event_type = ' + type, function(err, event) {
                db.each('SELECT location_from, location_to FROM transport WHERE id = ' + event.transport_id, function(err, transport) {	              
                    if (id in allSockets) { // user online
                        var message = 'Перевозка "' + transport.location_from + ' &mdash; ' + transport.location_to + '" была закрыта';
                        if(type == 2) message = 'Перевозка "' + transport.location_from + ' &mdash; ' + transport.location_to + '" будет закрыта через ' + minNotyfy + ' минут';
                        if(type == 3) message = 'Создана новая международная перевозка "' + transport.location_from + ' &mdash; ' + transport.location_to + '"';
                        if(type == 4) message = 'Создана новая региональная перевозка "' + transport.location_from + ' &mdash; ' + transport.location_to + '"';

                        io.sockets.socket(socket.id).emit('onlineEvent', {
                            msg : message
                        });
                        var stmt = "UPDATE user_event set status=0 WHERE id = " + event.id;
                        db.run(stmt);
                        seachNewEvents(id, type, minNotyfy);
                    }
                });
            });
        }, 2000);
    }*/
	
	function seachNewEvents(id, minNotyfy)
    {
        setTimeout(function() {
            db.each('SELECT id, transport_id, type FROM user_event WHERE status = 1 and status_online = 1 and user_id = ' + id, function(err, event) {
                db.each('SELECT location_from, location_to FROM transport WHERE id = ' + event.transport_id, function(err, transport) {	              
                    if (id in allSockets) { // user online
                        var message = 'Перевозка "' + transport.location_from + ' &mdash; ' + transport.location_to + '" была закрыта';
                        if(event.type == 2) message = 'Перевозка "' + transport.location_from + ' &mdash; ' + transport.location_to + '" будет закрыта через ' + minNotyfy + ' минут';
                        if(event.type == 3) message = 'Создана новая международная перевозка "' + transport.location_from + ' &mdash; ' + transport.location_to + '"';
                        if(event.type == 4) message = 'Создана новая региональная перевозка "' + transport.location_from + ' &mdash; ' + transport.location_to + '"';

                        io.sockets.socket(socket.id).emit('onlineEvent', {
                            msg : message
                        });
                        
                        var stmt = "UPDATE user_event set status_online = 0 WHERE id = " + event.id;
                        db.run(stmt);
                        seachNewEvents(id, minNotyfy);
                    }
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
        }, 2000);
    }
    
    /* ----- Rates ----- */
	
	function getDateTime() 
	{
	    //process.env.TZ = 'Moscow';
		
		var date = new Date();
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
    
    /* Load all rates when open transport page in the first time  */
    socket.on('loadRates', function (id, t_id) {
        db.serialize(function() {
            db.each("SELECT user_id, price, date FROM rate WHERE transport_id = " + t_id + " order by date asc, price desc", function(err, row) {
                arr[i] = new Array (row.user_id, row.price, row.date);
		        i++;
            }, function(err, rows) {	
                for(var j = 0; j < rows; j++) {
                    var k = 0;
                    db.each("SELECT id, name, surname FROM user WHERE id = " + arr[j][0], function(err, user) {
                        io.sockets.socket(socket.id).emit('loadRates', {
                            price : arr[k][1],
			    date  : arr[k][2],
                            name  : user.name,
			    surname : user.surname,
                        });
                        k++;
                    });
                }
            });
        });
    });
	
    socket.on('setRate', function (data) {
		//db.each("SELECT min(price) as price FROM rate WHERE transport_id = " + data.transportId, function(err, rate) {
            //console.log('===== ' + rate.price);
			//if(data.price < rate.price || rate.price == null) {
                db.each("SELECT rate_id, location_from, location_to FROM transport WHERE id = " + data.transportId, function(err, row) { 
					if(row.rate_id != 'null') {
                        db.each("SELECT user_id FROM rate WHERE id = " + row.rate_id, function(err, user) {
                            //db.each("SELECT site_kill_rate FROM user_field WHERE user_id = " + user.user_id, function(err, option) {
                                //if(option.site_kill_rate) {
                                    if (user.user_id in allSockets) { // user online
                                        io.sockets.socket(allSockets[user.user_id]).emit('onlineEvent', {
                                            msg : 'Вашу ставку для перевозки "' + row.location_from + ' &mdash; ' + row.location_to + '" перебили'
                                        });
                                    } 
									
									var stmt = db.prepare("INSERT INTO user_event(user_id, transport_id, status, type, event_type) VALUES (?, ?, ?, ?, ?)");
									stmt.run(user.user_id, data.transportId, 1, 1, 5);
									stmt.finalize();
                                    
                                //}
                            //});
                        });
                    }
                });
				//var curDate = new Date();
				var time = getDateTime();/*curDate.getFullYear() + '-' + (curDate.getMonth()+1) + '-'
					+ curDate.getDate() + ' ' + curDate.getHours() + ':' + curDate.getMinutes() + ':' 
					+ curDate.getSeconds()
				;*/
                //var time = new Date().getTime();
				//console.log('==== ' + time);
				
                var stmt = db.prepare("INSERT INTO rate(transport_id, date, price, user_id) VALUES (?, ?, ?, ?)");
                stmt.run(data.transportId, time, data.price, data.userId);
                stmt.finalize();
                
                db.each("SELECT id FROM rate WHERE transport_id = " + data.transportId + " and price = " + data.price + " and user_id = " + data.userId, function(err, row) {
                    var stmt = "UPDATE transport SET rate_id = " + row.id + " WHERE id = " + data.transportId;
                    db.run(stmt);
                });
                
                // to sender
                io.sockets.socket(socket.id).emit('setRate', {
                    name : data.name,
                    surname : data.surname,
                    price : data.price,
                    date: time,
					transportId : data.transportId
                });
                // to all other
                socket.broadcast.emit('setRate', {
                    name : data.name,
                    surname : data.surname,
                    price : data.price,
                    date: time,
					transportId : data.transportId
                });
            /*} else {
                io.sockets.socket(socket.id).emit('errorRate', {
                    price : data.price
                });
            }*/
        //});
		/*
        db.each("SELECT user_id FROM rate WHERE transport_id = " + data.transportId + " and price = " + data.price, function(err, rate) {
        }, function(err, rows) {
            if(rows == 0) {
                db.each("SELECT rate_id, location_from, location_to FROM transport WHERE id = " + data.transportId, function(err, row) { 
					if(row.rate_id != 'null') {
                        db.each("SELECT user_id FROM rate WHERE id = " + row.rate_id, function(err, user) {
                            db.each("SELECT site_kill_rate FROM user_field WHERE user_id = " + user.user_id, function(err, option) {
                                if(option.site_kill_rate) {
                                    if (user.user_id in allSockets) { // user online
                                        io.sockets.socket(allSockets[user.user_id]).emit('onlineEvent', {
                                            msg : 'Вашу ставку для перевозки "' + row.location_from + ' &mdash; ' + row.location_to + '" перебили'
                                        });

                                        
                                    } 
                                        var stmt = db.prepare("INSERT INTO user_event(user_id, transport_id, status, type, event_type) VALUES (?, ?, ?, ?, ?)");
                                        stmt.run(user.user_id, data.transportId, 1, 1, 5);
                                        stmt.finalize();
                                    
                                }
                            });
                        });
                    }
                });
                
                var stmt = db.prepare("INSERT INTO rate(transport_id, date, price, user_id) VALUES (?, ?, ?, ?)");
                stmt.run(data.transportId, data.date, data.price, data.userId);
                stmt.finalize();
                
                db.each("SELECT id FROM rate WHERE transport_id = " + data.transportId + " and price = " + data.price + " and user_id = " + data.userId, function(err, row) {
                    var stmt = "UPDATE transport SET rate_id = " + row.id + " WHERE id = " + data.transportId;
                    db.run(stmt);
                });
                

                io.sockets.socket(socket.id).emit('setRate', {
                    name : data.name,
                    surname : data.surname,
                    price : data.price,
                    date: data.date,
					transportId : data.transportId
                });
                
                socket.broadcast.emit('setRate', {
                    name : data.name,
                    surname : data.surname,
                    price : data.price,
                    date: data.date,
					transportId : data.transportId
                });
            } else {
                io.sockets.socket(socket.id).emit('errorRate', {
                    price : data.price
                });
            }
        });*/
    }); 
});