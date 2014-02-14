var io = require('socket.io').listen(3000);

io.sockets.on('connection', function (socket) {
    var fs = require("fs");
    var file = "d:/server/domains/exchange/protected/data/exchange.db";
    var sqlite3 = require("sqlite3").verbose();
    var db = new sqlite3.Database(file);
    var arr = [];
	var name = [];
	var i = -1;
	
	socket.on('events', function (id) {
	    socket.set('id', id);
        seachNewEvents(id);
	});  
    
    setInterval(function(){
	    socket.get('id', function (err, id) {
		    seachNewEvents(id);
		});
    }, 2000);
	
	function seachNewEvents(id){
	    db.each("SELECT status FROM user_event WHERE user_id = " + id + " and status = 1", function(err, row) {
	    }, function(err, rows) {
		    io.sockets.socket(socket.id).emit('updateEvents', {
				count : rows
			});
		});
	}
});