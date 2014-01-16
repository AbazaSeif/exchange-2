$(document).ready(function(){
	$.ajax({
	  url: 'exchange/site/updateCounter',
	  cache: false,
	  success: function(){
		//$("#events-count").innerHtml(2222);
		//console.log(answer);
	  }
	});
});