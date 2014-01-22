/*$(document).ready(function(){
	chat.init();
});
*/
var rateList = {
	data : {
	    btnUp     : $("div.rate-btns").find('div.up'),
		btnDown   : $("div.rate-btns").find('div.down'),
		btnSend   : $("#rate-btn"),
		container : $("#data"),    //posts
		price     : $('#rate-price'),
	},
	
	init : function(){
		//console.log(this.data.priceStep);
		this.data.btnUp.click(function(){
			//var element = $('#rate-price');
			if(!$(this).hasClass('disabled')){
				var curRate = this.data.price.val();
				var newRate = parseInt(curRate) + priceStep;
				if(newRate <= $("#rate-price").attr('init')) this.data.price.val(newRate);
				
				//button style
				if (parseInt($("#rate-price").attr('init')) == this.data.price.val()) $(this).addClass('disabled');
				else $(this).removeClass('disabled');
			}
		});

		this.data.btnDown.click(function(){
			if(this.data.price.val() < this.data.price.attr('init')) 
				this.data.btnUp.removeClass('disabled');
			if(!$(this).hasClass('disabled')){
				var element = $('#rate-price');
				var curRate = element.val();
				var newRate = parseInt(curRate) - priceStep; 
				if(newRate > 0) element.val(newRate);
			}
		});

		this.data.btnSend.click(function(){
		    console.log(111);
			/*if(!$(this).hasClass('disabled')){
				//updateCounter(posts, $('#rate-price').val());
				this.update(this.data.container, this.data.price.val());
			}*/
		});
	},
	update : function(posts, price) {
	    if ($('#data').length > 0) {
			price = typeof price !== 'undefined' ? price : '';
			var currentScroll = $('#data').scrollTop();
			//console.log(currentScroll);
			var startCount = $('#data').find('.post').length;
			//var height = $('#data:first').outerHeight();
			//console.log(height);
			$.ajax({
				type: 'POST',
				url: '/transport/updateRatesPrice',
				dataType: 'json',
				data:{
					id: this.data.transportId,
					newRate: price, 
				},
				success: function(rates) {
					$.each( rates.all, function( key, value ) {
						add(value, posts);
					});
					
					$("#rate-price").attr('init', rates.price);
					$('#last-rate').html(rates.price);
			}});
		}
	}
};