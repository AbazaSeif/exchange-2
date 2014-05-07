/*
 * 	Easy Tooltip 1.0 - jQuery plugin
 *	written by Alen Grakalic	
 *	http://cssglobe.com/post/4380/easy-tooltip--jquery-plugin
 *
 *	Copyright (c) 2009 Alen Grakalic (http://cssglobe.com)
 *	Dual licensed under the MIT (MIT-LICENSE.txt)
 *	and GPL (GPL-LICENSE.txt) licenses.
 *
 *	Built for jQuery library
 *	http://jquery.com
 *
 */
 
(function($) {

	$.fn.easyTooltip = function(options){
	  
		// default configuration properties
		var defaults = {	
			xOffset: 10,		
			yOffset: 25,
			tooltipId: "easyTooltip",
			clickRemove: false,
			content: "",
			useElement: ""
		}; 
			
		var options = $.extend(defaults, options);  
		var content;
				
		this.each(function() {  				
			var title = $(this).text() + '<br/>' + $(this).attr("title");	
                        //console.log($(this).text());
			$(this).hover(function(e){											 							   
				content = (options.content != "") ? options.content : title;
				content = (options.useElement != "") ? $("#" + options.useElement).html() : content;
				$(this).attr("title","");									  				
				if (content != "" && content != undefined){			
					//$("body").append("<div id='"+ options.tooltipId +"' class='tip-darkgray tip-arrow-bottom'>"+ content +"</div>");		
		                        
                                        var element = '<div id="'+ options.tooltipId +'" class="easytooltip" style="visibility: inherit; border: 0px none; padding: 0px; background-image: none; background-color: transparent; opacity: 0.95">' +
                                                '<table border="0" cellpadding="0" style="border-spacing: 0">' + 
                                                    '<tbody>' +
                                                        '<tr>' +
                                                            '<td class="tip-top tip-bg-image" colspan="2" style="background-image: url(/images/tip-darkgray.png)"><span></span></td>' +
                                                            '<td class="tip-right tip-bg-image" rowspan="2" style="background-image: url(/images/tip-darkgray.png)"><span></span></td>' +
                                                        '</tr>' +
                                                        '<tr>' +
                                                            '<td class="tip-left tip-bg-image" rowspan="2" style="background-image: url(/images/tip-darkgray.png)"><span></span></td>' +
                                                            '<td style="widht: 100%">' +
                                                                '<div class="tip-inner tip-bg-image" style="background-image: url(/images/tip-darkgray.png)">' +
                                                                    content +
                                                                '</div>' +
                                                            '</td>' +
                                                        '</tr>' +
                                                        '<tr>' +
                                                            '<td class="tip-bottom tip-bg-image" colspan="2" style="background-image: url(/images/tip-darkgray.png)"><span></span></td>' +
                                                        '</tr>' +
                                                    '</tbody>' +
                                                '</table>' + 
                                                '<div class="tip-arrow tip-arrow-bottom" style="visibility: inherit;"><span></span></div>' +
                                            '</div>'
                                        ;
                                        $("body").append(element);		
		                        var element = $("#" + options.tooltipId);
                                        var height = $("#" + options.tooltipId).height();
					element
						.css("position","absolute")
						.css("top",(e.pageY - options.yOffset - height) + "px")
						.css("left",(e.pageX + options.xOffset - 35) + "px")						
						.css("display","none")
						.fadeIn("fast")
				}
			},
			function(){	
				$("#" + options.tooltipId).remove();
				$(this).attr("title",title);
			});	
			$(this).mousemove(function(e){
                                var element = $("#" + options.tooltipId);
                                var height = element.height();
				element
					.css("top",(e.pageY - options.yOffset - height) + "px")
					.css("left",(e.pageX + options.xOffset - 35) + "px")					
			});	
			if(options.clickRemove){
				$(this).mousedown(function(e){
					$("#" + options.tooltipId).remove();
					$(this).attr("title",title);
				});				
			}
		});
	  
	};

})(jQuery);
