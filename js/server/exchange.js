var io = require('socket.io').listen(3000);
var allSockets = [];

//socket.on('disconnect', function() { connectCounter--; });
io.sockets.on('connection', function (socket) {
    var fs = require("fs");
    var file = "d:/server/domains/exchange/protected/data/exchange.db";
    var sqlite3 = require("sqlite3").verbose();
    var db = new sqlite3.Database(file);
    var arr  = [];
    var name = [];
    var i = -1;

    //socket.on('disconnect', function() {
        //deleteFromArray(clients, socket.id);
        //delete allSockets[id]
    //});

    var classic2 = ['This','Is','An','Array'];
    
    socket.on('init', function (id) {
        console.log( id + '=======' + socket.id);
        allSockets[id] = socket.id;
    });
    
    /* ----- Update count of messages in frontend ----- */
    function seachNewEvents(id)
    {
        db.each("SELECT status FROM user_event WHERE user_id = " + id + " and status = 1", function(err, row) {
        }, function(err, rows) {
            io.sockets.socket(socket.id).emit('updateEvents', {
                count : rows
            });
        });
    }
    
    socket.on('events', function (id) {
        socket.set('id', id);
        seachNewEvents(id);
    });  
    
    /*
    setInterval(function(){
	    socket.get('id', function (err, id) {
		    seachNewEvents(id);
		});
    }, 2000);
    */
    
    
    /* ----- Rates ----- */
    
    /* Load all rates when open transport page in the first time  */
    socket.on('loadRates', function (id) {
        //allSockets[id] = socket.id;
        db.serialize(function() {
            db.each("SELECT user_id, price FROM rate WHERE transport_id = " + id + " order by price asc", function(err, row) {
                i++;
                arr[i] = new Array (row.user_id, row.price);
            }, function(err, rows) {
                /*io.sockets.socket(socket.id).emit('endinit');	
                for(var j = 0; j < rows; j++) {
                    var k = 0;
                    db.each("SELECT id, name, surname FROM user WHERE id = " + arr[j][0], function(err, user) {
                        io.sockets.socket(socket.id).emit('init', {
                        //io.sockets.emit('init', {
                            price : arr[k][1],
                            name  : user.name + ' ' + user.surname,
                            count : rows
                        });
                        k++;
                    });
                }*/
            });
        });
    });
    
    socket.on('setRate', function (data) {
        db.each("SELECT user_id FROM rate WHERE transport_id = " + data.transportId + " and price = " + data.price, function(err, row) {
        }, function(err, rows) {
            if(rows == 0) {
                db.each("SELECT rate_id, location_from, location_to FROM transport WHERE id = " + data.transportId, function(err, row) { 
                    if(row.rate_id != 'null') {
                        db.each("SELECT user_id FROM rate WHERE id = " + row.rate_id, function(err, user) {
                             //console.log( ' = '   + user.user_id );
                             //console.log( ' = '   + allSockets[user.user_id] );
                             //console.log( '** = ' + socket.id );
                             io.sockets.socket(allSockets[user.user_id]).emit('onlineEvent', {
                                name : row.location_from + ' &mdash; ' + row.location_to
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
                    date: data.date
                });
                
                socket.broadcast.emit('setRate', {
                    name : data.name,
                    surname : data.surname,
                    price : data.price,
                    date: data.date
                });
            } else {
                io.sockets.socket(socket.id).emit('errorRate', {
                    price : data.price
                });
            }
        });
    }); 
});