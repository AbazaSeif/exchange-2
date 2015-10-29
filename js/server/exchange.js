// *** Database ***
var sqlite3 = require("sqlite3").verbose();
//var file = "d:/server/domains/data/exchange.db"; 
var file = "/var/www/vhosts/lbr.ru/httpdocs/data/exchange.db";
var db = new sqlite3.Database(file);

// *** Socket ***
var io = require('socket.io').listen(3001);
var allSockets = [];

function deleteFromArray(element) {
    position = allSockets.indexOf(element);
    allSockets.splice(position, 1);
}

// *** Timer for all transports ***
var timer = require('/var/www/vhosts/lbr.ru/httpdocs/exchange/js/server/timer.js');
var Timer = timer();

function tick() {
    db.each('SELECT id, date_close FROM transport WHERE status = 1', function(err, transport) {
        var end = new Date(transport.date_close);
        end.setTime(end.getTime() - (1 * 1000));
        
        if(new Date() < end){
            var time = Timer.init(transport.date_close);

            io.sockets.emit('timer', {
                access: 1,
                time: time,
                transportId: transport.id
            });
        } else {
            io.sockets.emit('timer', {
                access: 0,
                transportId: transport.id
            });
        }
    });
}
setInterval(tick, 1000);

// *** Update count of messages in frontend and search for online messages ***
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
    }, 1000);
}

function updateEventsCount()
{
    db.each("SELECT count(*) count, user_id FROM user_event WHERE status = 1 GROUP BY user_id", function(err, row) {
        if( allSockets[row.user_id] !== 'undefined' ) {
            io.sockets.socket(allSockets[row.user_id]).emit('updateEvents', {
                count : row.count
            });
        }
    });
}
setInterval(updateEventsCount, 1000);

function showOnlineMessages(data) 
{
    var allow = true;
    var now = new Date();
    var transportDateClose = new Date(data.dateClose);
    
    if(now.valueOf() > transportDateClose.valueOf()) {
        allow = false;
    }

    return allow;
}

// *** User connection ***
io.sockets.on('connection', function (socket) {
    var arr = [];
    var name = [];
    var i = 0;
    var labelForHiddenCompanyNames = '****';

    socket.on('init', function (id)
    {
        allSockets[id] = socket.id;	
    });
	
    socket.on('disconnect', function() {
        deleteFromArray(socket.id);
    });
    
    // rates
	
    function getDateTime(inputDate) 
    {
        var date = new Date();
        if (typeof inputDate != 'undefined') date = new Date(inputDate);
        
        //date.setTime(date.getTime() - (40 * 1000)); // minus time
        
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
	
    // set rate
    socket.on('setRateToServer', function (data) {
        if(parseInt(data.x) == 854) {
            db.each("SELECT start_rate, date_close, status, type, rate_id, location_from, location_to FROM transport WHERE id = " + data.transportId, function(err, row) { 
                var allow = true;
                if(parseInt(row.status) == 1) {
                    if(parseInt(data.price) <= parseInt(row.start_rate)) {
                        var dateCloseNew = ''; //checkForAdditionalTimer(data);
                        var time = getDateTime();
                        // check time
                        var now = new Date();
                        var end = new Date(row.date_close);
                        if(now >= end){
                            allow = false;
                            io.sockets.socket(socket.id).emit('closeRate', {
                                response : 'Ставки больше не принимаются.'
                            }); 
                        }
                        
                        if (allow) {
                            if(row.rate_id) { // not first rate in transport	
                                // check if it's min rate
                                db.each("SELECT min(price) as price, user_id FROM rate WHERE transport_id = " + data.transportId + " group by transport_id order by date desc", function(err, min) {
                                    if(min.price > data.price) {
                                        var stmt = db.prepare("INSERT INTO rate(transport_id, date, price, user_id) VALUES (?, ?, ?, ?)");
                                        stmt.run(data.transportId, time, data.price, data.userId);
                                        stmt.finalize();

                                        db.each("SELECT id FROM rate WHERE transport_id = " + data.transportId + " and price = " + data.price + " and user_id = " + data.userId, function(err, row) {
                                            var stmt = "UPDATE transport SET rate_id = " + row.id + " WHERE id = " + data.transportId;
                                            db.run(stmt);
                                        });
                                        
                                        // online message only if this rate is the minimal of all
                                        var showOnlineMessage = showOnlineMessages(data);
                                        if(showOnlineMessage) {
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
                                        }
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
        } else {
            io.sockets.socket(socket.id).emit('closeRate', {
                response : 'Почистите кеш, т.е. нажмите Ctrl+F5.',
            });
        }
    });
});
