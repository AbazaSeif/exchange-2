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
        //seachNewEvents(id, minNotyfy);
        //updateEventsCount(id);	
    });
	
    socket.on('disconnect', function() {
        deleteFromArray(socket.id);
    });
    
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
});


