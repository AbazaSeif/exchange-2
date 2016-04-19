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

/*mousewheel*/
(function(a){function d(b){var c=b||window.event,d=[].slice.call(arguments,1),e=0,f=!0,g=0,h=0;return b=a.event.fix(c),b.type="mousewheel",c.wheelDelta&&(e=c.wheelDelta/120),c.detail&&(e=-c.detail/3),h=e,c.axis!==undefined&&c.axis===c.HORIZONTAL_AXIS&&(h=0,g=-1*e),c.wheelDeltaY!==undefined&&(h=c.wheelDeltaY/120),c.wheelDeltaX!==undefined&&(g=-1*c.wheelDeltaX/120),d.unshift(b,e,g,h),(a.event.dispatch||a.event.handle).apply(this,d)}var b=["DOMMouseScroll","mousewheel"];if(a.event.fixHooks)for(var c=b.length;c;)a.event.fixHooks[b[--c]]=a.event.mouseHooks;a.event.special.mousewheel={setup:function(){if(this.addEventListener)for(var a=b.length;a;)this.addEventListener(b[--a],d,!1);else this.onmousewheel=d},teardown:function(){if(this.removeEventListener)for(var a=b.length;a;)this.removeEventListener(b[--a],d,!1);else this.onmousewheel=null}},a.fn.extend({mousewheel:function(a){return a?this.bind("mousewheel",a):this.trigger("mousewheel")},unmousewheel:function(a){return this.unbind("mousewheel",a)}})})(jQuery);
/*custom scrollbar*/
(function(c){var b={init:function(e){var f={set_width:false,set_height:false,horizontalScroll:false,scrollInertia:950,mouseWheel:true,mouseWheelPixels:"auto",autoDraggerLength:true,autoHideScrollbar:false,alwaysShowScrollbar:false,snapAmount:null,snapOffset:0,scrollButtons:{enable:false,scrollType:"continuous",scrollSpeed:"auto",scrollAmount:40},advanced:{updateOnBrowserResize:true,updateOnContentResize:false,autoExpandHorizontalScroll:false,autoScrollOnFocus:true,normalizeMouseWheelDelta:false},contentTouchScroll:true,callbacks:{onScrollStart:function(){},onScroll:function(){},onTotalScroll:function(){},onTotalScrollBack:function(){},onTotalScrollOffset:0,onTotalScrollBackOffset:0,whileScrolling:function(){}},theme:"light"},e=c.extend(true,f,e);return this.each(function(){var m=c(this);if(e.set_width){m.css("width",e.set_width)}if(e.set_height){m.css("height",e.set_height)}if(!c(document).data("mCustomScrollbar-index")){c(document).data("mCustomScrollbar-index","1")}else{var t=parseInt(c(document).data("mCustomScrollbar-index"));c(document).data("mCustomScrollbar-index",t+1)}m.wrapInner("<div class='mCustomScrollBox mCS-"+e.theme+"' id='mCSB_"+c(document).data("mCustomScrollbar-index")+"' style='position:relative; height:100%; overflow:hidden; max-width:100%;' />").addClass("mCustomScrollbar _mCS_"+c(document).data("mCustomScrollbar-index"));var g=m.children(".mCustomScrollBox");if(e.horizontalScroll){g.addClass("mCSB_horizontal").wrapInner("<div class='mCSB_h_wrapper' style='position:relative; left:0; width:999999px;' />");var k=g.children(".mCSB_h_wrapper");k.wrapInner("<div class='mCSB_container' style='position:absolute; left:0;' />").children(".mCSB_container").css({width:k.children().outerWidth(),position:"relative"}).unwrap()}else{g.wrapInner("<div class='mCSB_container' style='position:relative; top:0;' />")}var o=g.children(".mCSB_container");if(c.support.touch){o.addClass("mCS_touch")}o.after("<div class='mCSB_scrollTools' style='position:absolute;'><div class='mCSB_draggerContainer'><div class='mCSB_dragger' style='position:absolute;' oncontextmenu='return false;'><div class='mCSB_dragger_bar' style='position:relative;'></div></div><div class='mCSB_draggerRail'></div></div></div>");var l=g.children(".mCSB_scrollTools"),h=l.children(".mCSB_draggerContainer"),q=h.children(".mCSB_dragger");if(e.horizontalScroll){q.data("minDraggerWidth",q.width())}else{q.data("minDraggerHeight",q.height())}if(e.scrollButtons.enable){if(e.horizontalScroll){l.prepend("<a class='mCSB_buttonLeft' oncontextmenu='return false;'></a>").append("<a class='mCSB_buttonRight' oncontextmenu='return false;'></a>")}else{l.prepend("<a class='mCSB_buttonUp' oncontextmenu='return false;'></a>").append("<a class='mCSB_buttonDown' oncontextmenu='return false;'></a>")}}g.bind("scroll",function(){if(!m.is(".mCS_disabled")){g.scrollTop(0).scrollLeft(0)}});m.data({mCS_Init:true,mCustomScrollbarIndex:c(document).data("mCustomScrollbar-index"),horizontalScroll:e.horizontalScroll,scrollInertia:e.scrollInertia,scrollEasing:"mcsEaseOut",mouseWheel:e.mouseWheel,mouseWheelPixels:e.mouseWheelPixels,autoDraggerLength:e.autoDraggerLength,autoHideScrollbar:e.autoHideScrollbar,alwaysShowScrollbar:e.alwaysShowScrollbar,snapAmount:e.snapAmount,snapOffset:e.snapOffset,scrollButtons_enable:e.scrollButtons.enable,scrollButtons_scrollType:e.scrollButtons.scrollType,scrollButtons_scrollSpeed:e.scrollButtons.scrollSpeed,scrollButtons_scrollAmount:e.scrollButtons.scrollAmount,autoExpandHorizontalScroll:e.advanced.autoExpandHorizontalScroll,autoScrollOnFocus:e.advanced.autoScrollOnFocus,normalizeMouseWheelDelta:e.advanced.normalizeMouseWheelDelta,contentTouchScroll:e.contentTouchScroll,onScrollStart_Callback:e.callbacks.onScrollStart,onScroll_Callback:e.callbacks.onScroll,onTotalScroll_Callback:e.callbacks.onTotalScroll,onTotalScrollBack_Callback:e.callbacks.onTotalScrollBack,onTotalScroll_Offset:e.callbacks.onTotalScrollOffset,onTotalScrollBack_Offset:e.callbacks.onTotalScrollBackOffset,whileScrolling_Callback:e.callbacks.whileScrolling,bindEvent_scrollbar_drag:false,bindEvent_content_touch:false,bindEvent_scrollbar_click:false,bindEvent_mousewheel:false,bindEvent_buttonsContinuous_y:false,bindEvent_buttonsContinuous_x:false,bindEvent_buttonsPixels_y:false,bindEvent_buttonsPixels_x:false,bindEvent_focusin:false,bindEvent_autoHideScrollbar:false,mCSB_buttonScrollRight:false,mCSB_buttonScrollLeft:false,mCSB_buttonScrollDown:false,mCSB_buttonScrollUp:false});if(e.horizontalScroll){if(m.css("max-width")!=="none"){if(!e.advanced.updateOnContentResize){e.advanced.updateOnContentResize=true}}}else{if(m.css("max-height")!=="none"){var s=false,r=parseInt(m.css("max-height"));if(m.css("max-height").indexOf("%")>=0){s=r,r=m.parent().height()*s/100}m.css("overflow","hidden");g.css("max-height",r)}}m.mCustomScrollbar("update");if(e.advanced.updateOnBrowserResize){var i,j=c(window).width(),u=c(window).height();c(window).bind("resize."+m.data("mCustomScrollbarIndex"),function(){if(i){clearTimeout(i)}i=setTimeout(function(){if(!m.is(".mCS_disabled")&&!m.is(".mCS_destroyed")){var w=c(window).width(),v=c(window).height();if(j!==w||u!==v){if(m.css("max-height")!=="none"&&s){g.css("max-height",m.parent().height()*s/100)}m.mCustomScrollbar("update");j=w;u=v}}},150)})}if(e.advanced.updateOnContentResize){var p;if(e.horizontalScroll){var n=o.outerWidth()}else{var n=o.outerHeight()}p=setInterval(function(){if(e.horizontalScroll){if(e.advanced.autoExpandHorizontalScroll){o.css({position:"absolute",width:"auto"}).wrap("<div class='mCSB_h_wrapper' style='position:relative; left:0; width:999999px;' />").css({width:o.outerWidth(),position:"relative"}).unwrap()}var v=o.outerWidth()}else{var v=o.outerHeight()}if(v!=n){m.mCustomScrollbar("update");n=v}},300)}})},update:function(){var n=c(this),k=n.children(".mCustomScrollBox"),q=k.children(".mCSB_container");q.removeClass("mCS_no_scrollbar");n.removeClass("mCS_disabled mCS_destroyed");k.scrollTop(0).scrollLeft(0);var y=k.children(".mCSB_scrollTools"),o=y.children(".mCSB_draggerContainer"),m=o.children(".mCSB_dragger");if(n.data("horizontalScroll")){var A=y.children(".mCSB_buttonLeft"),t=y.children(".mCSB_buttonRight"),f=k.width();if(n.data("autoExpandHorizontalScroll")){q.css({position:"absolute",width:"auto"}).wrap("<div class='mCSB_h_wrapper' style='position:relative; left:0; width:999999px;' />").css({width:q.outerWidth(),position:"relative"}).unwrap()}var z=q.outerWidth()}else{var w=y.children(".mCSB_buttonUp"),g=y.children(".mCSB_buttonDown"),r=k.height(),i=q.outerHeight()}if(i>r&&!n.data("horizontalScroll")){y.css("display","block");var s=o.height();if(n.data("autoDraggerLength")){var u=Math.round(r/i*s),l=m.data("minDraggerHeight");if(u<=l){m.css({height:l})}else{if(u>=s-10){var p=s-10;m.css({height:p})}else{m.css({height:u})}}m.children(".mCSB_dragger_bar").css({"line-height":m.height()+"px"})}var B=m.height(),x=(i-r)/(s-B);n.data("scrollAmount",x).mCustomScrollbar("scrolling",k,q,o,m,w,g,A,t);var D=Math.abs(q.position().top);n.mCustomScrollbar("scrollTo",D,{scrollInertia:0,trigger:"internal"})}else{if(z>f&&n.data("horizontalScroll")){y.css("display","block");var h=o.width();if(n.data("autoDraggerLength")){var j=Math.round(f/z*h),C=m.data("minDraggerWidth");if(j<=C){m.css({width:C})}else{if(j>=h-10){var e=h-10;m.css({width:e})}else{m.css({width:j})}}}var v=m.width(),x=(z-f)/(h-v);n.data("scrollAmount",x).mCustomScrollbar("scrolling",k,q,o,m,w,g,A,t);var D=Math.abs(q.position().left);n.mCustomScrollbar("scrollTo",D,{scrollInertia:0,trigger:"internal"})}else{k.unbind("mousewheel focusin");if(n.data("horizontalScroll")){m.add(q).css("left",0)}else{m.add(q).css("top",0)}if(n.data("alwaysShowScrollbar")){if(!n.data("horizontalScroll")){m.css({height:o.height()})}else{if(n.data("horizontalScroll")){m.css({width:o.width()})}}}else{y.css("display","none");q.addClass("mCS_no_scrollbar")}n.data({bindEvent_mousewheel:false,bindEvent_focusin:false})}}},scrolling:function(i,q,n,k,A,f,D,w){var l=c(this);if(!l.data("bindEvent_scrollbar_drag")){var o,p,C,z,e;if(c.support.pointer){C="pointerdown";z="pointermove";e="pointerup"}else{if(c.support.msPointer){C="MSPointerDown";z="MSPointerMove";e="MSPointerUp"}}if(c.support.pointer||c.support.msPointer){k.bind(C,function(K){K.preventDefault();l.data({on_drag:true});k.addClass("mCSB_dragger_onDrag");var J=c(this),M=J.offset(),I=K.originalEvent.pageX-M.left,L=K.originalEvent.pageY-M.top;if(I<J.width()&&I>0&&L<J.height()&&L>0){o=L;p=I}});c(document).bind(z+"."+l.data("mCustomScrollbarIndex"),function(K){K.preventDefault();if(l.data("on_drag")){var J=k,M=J.offset(),I=K.originalEvent.pageX-M.left,L=K.originalEvent.pageY-M.top;G(o,p,L,I)}}).bind(e+"."+l.data("mCustomScrollbarIndex"),function(x){l.data({on_drag:false});k.removeClass("mCSB_dragger_onDrag")})}else{k.bind("mousedown touchstart",function(K){K.preventDefault();K.stopImmediatePropagation();var J=c(this),N=J.offset(),I,M;if(K.type==="touchstart"){var L=K.originalEvent.touches[0]||K.originalEvent.changedTouches[0];I=L.pageX-N.left;M=L.pageY-N.top}else{l.data({on_drag:true});k.addClass("mCSB_dragger_onDrag");I=K.pageX-N.left;M=K.pageY-N.top}if(I<J.width()&&I>0&&M<J.height()&&M>0){o=M;p=I}}).bind("touchmove",function(K){K.preventDefault();K.stopImmediatePropagation();var N=K.originalEvent.touches[0]||K.originalEvent.changedTouches[0],J=c(this),M=J.offset(),I=N.pageX-M.left,L=N.pageY-M.top;G(o,p,L,I)});c(document).bind("mousemove."+l.data("mCustomScrollbarIndex"),function(K){if(l.data("on_drag")){var J=k,M=J.offset(),I=K.pageX-M.left,L=K.pageY-M.top;G(o,p,L,I)}}).bind("mouseup."+l.data("mCustomScrollbarIndex"),function(x){l.data({on_drag:false});k.removeClass("mCSB_dragger_onDrag")})}l.data({bindEvent_scrollbar_drag:true})}function G(J,K,L,I){if(l.data("horizontalScroll")){l.mCustomScrollbar("scrollTo",(k.position().left-(K))+I,{moveDragger:true,trigger:"internal"})}else{l.mCustomScrollbar("scrollTo",(k.position().top-(J))+L,{moveDragger:true,trigger:"internal"})}}if(c.support.touch&&l.data("contentTouchScroll")){if(!l.data("bindEvent_content_touch")){var m,E,s,t,v,F,H;q.bind("touchstart",function(x){x.stopImmediatePropagation();m=x.originalEvent.touches[0]||x.originalEvent.changedTouches[0];E=c(this);s=E.offset();v=m.pageX-s.left;t=m.pageY-s.top;F=t;H=v});q.bind("touchmove",function(x){x.preventDefault();x.stopImmediatePropagation();m=x.originalEvent.touches[0]||x.originalEvent.changedTouches[0];E=c(this).parent();s=E.offset();v=m.pageX-s.left;t=m.pageY-s.top;if(l.data("horizontalScroll")){l.mCustomScrollbar("scrollTo",H-v,{trigger:"internal"})}else{l.mCustomScrollbar("scrollTo",F-t,{trigger:"internal"})}})}}if(!l.data("bindEvent_scrollbar_click")){n.bind("click",function(I){var x=(I.pageY-n.offset().top)*l.data("scrollAmount"),y=c(I.target);if(l.data("horizontalScroll")){x=(I.pageX-n.offset().left)*l.data("scrollAmount")}if(y.hasClass("mCSB_draggerContainer")||y.hasClass("mCSB_draggerRail")){l.mCustomScrollbar("scrollTo",x,{trigger:"internal",scrollEasing:"draggerRailEase"})}});l.data({bindEvent_scrollbar_click:true})}if(l.data("mouseWheel")){if(!l.data("bindEvent_mousewheel")){i.bind("mousewheel",function(K,M){var J,I=l.data("mouseWheelPixels"),x=Math.abs(q.position().top),L=k.position().top,y=n.height()-k.height();if(l.data("normalizeMouseWheelDelta")){if(M<0){M=-1}else{M=1}}if(I==="auto"){I=100+Math.round(l.data("scrollAmount")/2)}if(l.data("horizontalScroll")){L=k.position().left;y=n.width()-k.width();x=Math.abs(q.position().left)}if((M>0&&L!==0)||(M<0&&L!==y)){K.preventDefault();K.stopImmediatePropagation()}J=x-(M*I);l.mCustomScrollbar("scrollTo",J,{trigger:"internal"})});l.data({bindEvent_mousewheel:true})}}if(l.data("scrollButtons_enable")){if(l.data("scrollButtons_scrollType")==="pixels"){if(l.data("horizontalScroll")){w.add(D).unbind("mousedown touchstart MSPointerDown pointerdown mouseup MSPointerUp pointerup mouseout MSPointerOut pointerout touchend",j,h);l.data({bindEvent_buttonsContinuous_x:false});if(!l.data("bindEvent_buttonsPixels_x")){w.bind("click",function(x){x.preventDefault();r(Math.abs(q.position().left)+l.data("scrollButtons_scrollAmount"))});D.bind("click",function(x){x.preventDefault();r(Math.abs(q.position().left)-l.data("scrollButtons_scrollAmount"))});l.data({bindEvent_buttonsPixels_x:true})}}else{f.add(A).unbind("mousedown touchstart MSPointerDown pointerdown mouseup MSPointerUp pointerup mouseout MSPointerOut pointerout touchend",j,h);l.data({bindEvent_buttonsContinuous_y:false});if(!l.data("bindEvent_buttonsPixels_y")){f.bind("click",function(x){x.preventDefault();r(Math.abs(q.position().top)+l.data("scrollButtons_scrollAmount"))});A.bind("click",function(x){x.preventDefault();r(Math.abs(q.position().top)-l.data("scrollButtons_scrollAmount"))});l.data({bindEvent_buttonsPixels_y:true})}}function r(x){if(!k.data("preventAction")){k.data("preventAction",true);l.mCustomScrollbar("scrollTo",x,{trigger:"internal"})}}}else{if(l.data("horizontalScroll")){w.add(D).unbind("click");l.data({bindEvent_buttonsPixels_x:false});if(!l.data("bindEvent_buttonsContinuous_x")){w.bind("mousedown touchstart MSPointerDown pointerdown",function(y){y.preventDefault();var x=B();l.data({mCSB_buttonScrollRight:setInterval(function(){l.mCustomScrollbar("scrollTo",Math.abs(q.position().left)+x,{trigger:"internal",scrollEasing:"easeOutCirc"})},17)})});var j=function(x){x.preventDefault();clearInterval(l.data("mCSB_buttonScrollRight"))};w.bind("mouseup touchend MSPointerUp pointerup mouseout MSPointerOut pointerout",j);D.bind("mousedown touchstart MSPointerDown pointerdown",function(y){y.preventDefault();var x=B();l.data({mCSB_buttonScrollLeft:setInterval(function(){l.mCustomScrollbar("scrollTo",Math.abs(q.position().left)-x,{trigger:"internal",scrollEasing:"easeOutCirc"})},17)})});var h=function(x){x.preventDefault();clearInterval(l.data("mCSB_buttonScrollLeft"))};D.bind("mouseup touchend MSPointerUp pointerup mouseout MSPointerOut pointerout",h);l.data({bindEvent_buttonsContinuous_x:true})}}else{f.add(A).unbind("click");l.data({bindEvent_buttonsPixels_y:false});if(!l.data("bindEvent_buttonsContinuous_y")){f.bind("mousedown touchstart MSPointerDown pointerdown",function(y){y.preventDefault();var x=B();l.data({mCSB_buttonScrollDown:setInterval(function(){l.mCustomScrollbar("scrollTo",Math.abs(q.position().top)+x,{trigger:"internal",scrollEasing:"easeOutCirc"})},17)})});var u=function(x){x.preventDefault();clearInterval(l.data("mCSB_buttonScrollDown"))};f.bind("mouseup touchend MSPointerUp pointerup mouseout MSPointerOut pointerout",u);A.bind("mousedown touchstart MSPointerDown pointerdown",function(y){y.preventDefault();var x=B();l.data({mCSB_buttonScrollUp:setInterval(function(){l.mCustomScrollbar("scrollTo",Math.abs(q.position().top)-x,{trigger:"internal",scrollEasing:"easeOutCirc"})},17)})});var g=function(x){x.preventDefault();clearInterval(l.data("mCSB_buttonScrollUp"))};A.bind("mouseup touchend MSPointerUp pointerup mouseout MSPointerOut pointerout",g);l.data({bindEvent_buttonsContinuous_y:true})}}function B(){var x=l.data("scrollButtons_scrollSpeed");if(l.data("scrollButtons_scrollSpeed")==="auto"){x=Math.round((l.data("scrollInertia")+100)/40)}return x}}}if(l.data("autoScrollOnFocus")){if(!l.data("bindEvent_focusin")){i.bind("focusin",function(){i.scrollTop(0).scrollLeft(0);var x=c(document.activeElement);if(x.is("input,textarea,select,button,a[tabindex],area,object")){var J=q.position().top,y=x.position().top,I=i.height()-x.outerHeight();if(l.data("horizontalScroll")){J=q.position().left;y=x.position().left;I=i.width()-x.outerWidth()}if(J+y<0||J+y>I){l.mCustomScrollbar("scrollTo",y,{trigger:"internal"})}}});l.data({bindEvent_focusin:true})}}if(l.data("autoHideScrollbar")&&!l.data("alwaysShowScrollbar")){if(!l.data("bindEvent_autoHideScrollbar")){i.bind("mouseenter",function(x){i.addClass("mCS-mouse-over");d.showScrollbar.call(i.children(".mCSB_scrollTools"))}).bind("mouseleave touchend",function(x){i.removeClass("mCS-mouse-over");if(x.type==="mouseleave"){d.hideScrollbar.call(i.children(".mCSB_scrollTools"))}});l.data({bindEvent_autoHideScrollbar:true})}}},scrollTo:function(e,f){var i=c(this),o={moveDragger:false,trigger:"external",callbacks:true,scrollInertia:i.data("scrollInertia"),scrollEasing:i.data("scrollEasing")},f=c.extend(o,f),p,g=i.children(".mCustomScrollBox"),k=g.children(".mCSB_container"),r=g.children(".mCSB_scrollTools"),j=r.children(".mCSB_draggerContainer"),h=j.children(".mCSB_dragger"),t=draggerSpeed=f.scrollInertia,q,s,m,l;if(!k.hasClass("mCS_no_scrollbar")){i.data({mCS_trigger:f.trigger});if(i.data("mCS_Init")){f.callbacks=false}if(e||e===0){if(typeof(e)==="number"){if(f.moveDragger){p=e;if(i.data("horizontalScroll")){e=h.position().left*i.data("scrollAmount")}else{e=h.position().top*i.data("scrollAmount")}draggerSpeed=0}else{p=e/i.data("scrollAmount")}}else{if(typeof(e)==="string"){var v;if(e==="top"){v=0}else{if(e==="bottom"&&!i.data("horizontalScroll")){v=k.outerHeight()-g.height()}else{if(e==="left"){v=0}else{if(e==="right"&&i.data("horizontalScroll")){v=k.outerWidth()-g.width()}else{if(e==="first"){v=i.find(".mCSB_container").find(":first")}else{if(e==="last"){v=i.find(".mCSB_container").find(":last")}else{v=i.find(e)}}}}}}if(v.length===1){if(i.data("horizontalScroll")){e=v.position().left}else{e=v.position().top}p=e/i.data("scrollAmount")}else{p=e=v}}}if(i.data("horizontalScroll")){if(i.data("onTotalScrollBack_Offset")){s=-i.data("onTotalScrollBack_Offset")}if(i.data("onTotalScroll_Offset")){l=g.width()-k.outerWidth()+i.data("onTotalScroll_Offset")}if(p<0){p=e=0;clearInterval(i.data("mCSB_buttonScrollLeft"));if(!s){q=true}}else{if(p>=j.width()-h.width()){p=j.width()-h.width();e=g.width()-k.outerWidth();clearInterval(i.data("mCSB_buttonScrollRight"));if(!l){m=true}}else{e=-e}}var n=i.data("snapAmount");if(n){e=Math.round(e/n)*n-i.data("snapOffset")}d.mTweenAxis.call(this,h[0],"left",Math.round(p),draggerSpeed,f.scrollEasing);d.mTweenAxis.call(this,k[0],"left",Math.round(e),t,f.scrollEasing,{onStart:function(){if(f.callbacks&&!i.data("mCS_tweenRunning")){u("onScrollStart")}if(i.data("autoHideScrollbar")&&!i.data("alwaysShowScrollbar")){d.showScrollbar.call(r)}},onUpdate:function(){if(f.callbacks){u("whileScrolling")}},onComplete:function(){if(f.callbacks){u("onScroll");if(q||(s&&k.position().left>=s)){u("onTotalScrollBack")}if(m||(l&&k.position().left<=l)){u("onTotalScroll")}}h.data("preventAction",false);i.data("mCS_tweenRunning",false);if(i.data("autoHideScrollbar")&&!i.data("alwaysShowScrollbar")){if(!g.hasClass("mCS-mouse-over")){d.hideScrollbar.call(r)}}}})}else{if(i.data("onTotalScrollBack_Offset")){s=-i.data("onTotalScrollBack_Offset")}if(i.data("onTotalScroll_Offset")){l=g.height()-k.outerHeight()+i.data("onTotalScroll_Offset")}if(p<0){p=e=0;clearInterval(i.data("mCSB_buttonScrollUp"));if(!s){q=true}}else{if(p>=j.height()-h.height()){p=j.height()-h.height();e=g.height()-k.outerHeight();clearInterval(i.data("mCSB_buttonScrollDown"));if(!l){m=true}}else{e=-e}}var n=i.data("snapAmount");if(n){e=Math.round(e/n)*n-i.data("snapOffset")}d.mTweenAxis.call(this,h[0],"top",Math.round(p),draggerSpeed,f.scrollEasing);d.mTweenAxis.call(this,k[0],"top",Math.round(e),t,f.scrollEasing,{onStart:function(){if(f.callbacks&&!i.data("mCS_tweenRunning")){u("onScrollStart")}if(i.data("autoHideScrollbar")&&!i.data("alwaysShowScrollbar")){d.showScrollbar.call(r)}},onUpdate:function(){if(f.callbacks){u("whileScrolling")}},onComplete:function(){if(f.callbacks){u("onScroll");if(q||(s&&k.position().top>=s)){u("onTotalScrollBack")}if(m||(l&&k.position().top<=l)){u("onTotalScroll")}}h.data("preventAction",false);i.data("mCS_tweenRunning",false);if(i.data("autoHideScrollbar")&&!i.data("alwaysShowScrollbar")){if(!g.hasClass("mCS-mouse-over")){d.hideScrollbar.call(r)}}}})}if(i.data("mCS_Init")){i.data({mCS_Init:false})}}}function u(w){if(i.data("mCustomScrollbarIndex")){this.mcs={top:k.position().top,left:k.position().left,draggerTop:h.position().top,draggerLeft:h.position().left,topPct:Math.round((100*Math.abs(k.position().top))/Math.abs(k.outerHeight()-g.height())),leftPct:Math.round((100*Math.abs(k.position().left))/Math.abs(k.outerWidth()-g.width()))};switch(w){case"onScrollStart":i.data("mCS_tweenRunning",true).data("onScrollStart_Callback").call(i,this.mcs);break;case"whileScrolling":i.data("whileScrolling_Callback").call(i,this.mcs);break;case"onScroll":i.data("onScroll_Callback").call(i,this.mcs);break;case"onTotalScrollBack":i.data("onTotalScrollBack_Callback").call(i,this.mcs);break;case"onTotalScroll":i.data("onTotalScroll_Callback").call(i,this.mcs);break}}}},stop:function(){var g=c(this),e=g.children().children(".mCSB_container"),f=g.children().children().children().children(".mCSB_dragger");d.mTweenAxisStop.call(this,e[0]);d.mTweenAxisStop.call(this,f[0])},disable:function(e){var j=c(this),f=j.children(".mCustomScrollBox"),h=f.children(".mCSB_container"),g=f.children(".mCSB_scrollTools"),i=g.children().children(".mCSB_dragger");f.unbind("mousewheel focusin mouseenter mouseleave touchend");h.unbind("touchstart touchmove");if(e){if(j.data("horizontalScroll")){i.add(h).css("left",0)}else{i.add(h).css("top",0)}}g.css("display","none");h.addClass("mCS_no_scrollbar");j.data({bindEvent_mousewheel:false,bindEvent_focusin:false,bindEvent_content_touch:false,bindEvent_autoHideScrollbar:false}).addClass("mCS_disabled")},destroy:function(){var e=c(this);e.removeClass("mCustomScrollbar _mCS_"+e.data("mCustomScrollbarIndex")).addClass("mCS_destroyed").children().children(".mCSB_container").unwrap().children().unwrap().siblings(".mCSB_scrollTools").remove();c(document).unbind("mousemove."+e.data("mCustomScrollbarIndex")+" mouseup."+e.data("mCustomScrollbarIndex")+" MSPointerMove."+e.data("mCustomScrollbarIndex")+" MSPointerUp."+e.data("mCustomScrollbarIndex"));c(window).unbind("resize."+e.data("mCustomScrollbarIndex"))}},d={showScrollbar:function(){this.stop().animate({opacity:1},"fast")},hideScrollbar:function(){this.stop().animate({opacity:0},"fast")},mTweenAxis:function(g,i,h,f,o,y){var y=y||{},v=y.onStart||function(){},p=y.onUpdate||function(){},w=y.onComplete||function(){};var n=t(),l,j=0,r=g.offsetTop,s=g.style;if(i==="left"){r=g.offsetLeft}var m=h-r;q();e();function t(){if(window.performance&&window.performance.now){return window.performance.now()}else{if(window.performance&&window.performance.webkitNow){return window.performance.webkitNow()}else{if(Date.now){return Date.now()}else{return new Date().getTime()}}}}function x(){if(!j){v.call()}j=t()-n;u();if(j>=g._time){g._time=(j>g._time)?j+l-(j-g._time):j+l-1;if(g._time<j+1){g._time=j+1}}if(g._time<f){g._id=_request(x)}else{w.call()}}function u(){if(f>0){g.currVal=k(g._time,r,m,f,o);s[i]=Math.round(g.currVal)+"px"}else{s[i]=h+"px"}p.call()}function e(){l=1000/60;g._time=j+l;_request=(!window.requestAnimationFrame)?function(z){u();return setTimeout(z,0.01)}:window.requestAnimationFrame;g._id=_request(x)}function q(){if(g._id==null){return}if(!window.requestAnimationFrame){clearTimeout(g._id)}else{window.cancelAnimationFrame(g._id)}g._id=null}function k(B,A,F,E,C){switch(C){case"linear":return F*B/E+A;break;case"easeOutQuad":B/=E;return -F*B*(B-2)+A;break;case"easeInOutQuad":B/=E/2;if(B<1){return F/2*B*B+A}B--;return -F/2*(B*(B-2)-1)+A;break;case"easeOutCubic":B/=E;B--;return F*(B*B*B+1)+A;break;case"easeOutQuart":B/=E;B--;return -F*(B*B*B*B-1)+A;break;case"easeOutQuint":B/=E;B--;return F*(B*B*B*B*B+1)+A;break;case"easeOutCirc":B/=E;B--;return F*Math.sqrt(1-B*B)+A;break;case"easeOutSine":return F*Math.sin(B/E*(Math.PI/2))+A;break;case"easeOutExpo":return F*(-Math.pow(2,-10*B/E)+1)+A;break;case"mcsEaseOut":var D=(B/=E)*B,z=D*B;return A+F*(0.499999999999997*z*D+-2.5*D*D+5.5*z+-6.5*D+4*B);break;case"draggerRailEase":B/=E/2;if(B<1){return F/2*B*B*B+A}B-=2;return F/2*(B*B*B+2)+A;break}}},mTweenAxisStop:function(e){if(e._id==null){return}if(!window.requestAnimationFrame){clearTimeout(e._id)}else{window.cancelAnimationFrame(e._id)}e._id=null},rafPolyfill:function(){var f=["ms","moz","webkit","o"],e=f.length;while(--e>-1&&!window.requestAnimationFrame){window.requestAnimationFrame=window[f[e]+"RequestAnimationFrame"];window.cancelAnimationFrame=window[f[e]+"CancelAnimationFrame"]||window[f[e]+"CancelRequestAnimationFrame"]}}};d.rafPolyfill.call();c.support.touch=!!("ontouchstart" in window);c.support.pointer=window.navigator.pointerEnabled;c.support.msPointer=window.navigator.msPointerEnabled;var a=("https:"==document.location.protocol)?"https:":"http:";c.event.special.mousewheel||document.write('<script src="'+a+'//cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.0.6/jquery.mousewheel.min.js"><\/script>');c.fn.mCustomScrollbar=function(e){if(b[e]){return b[e].apply(this,Array.prototype.slice.call(arguments,1))}else{if(typeof e==="object"||!e){return b.init.apply(this,arguments)}else{c.error("Method "+e+" does not exist")}}}})(jQuery);
var Timer = function(){};
Timer.prototype = {
  init: function(serverDate, initDate, id, status, transportId) {
    this.dateNow = new Date(serverDate);
    this.endDate = new Date(initDate); // дата и время от которых идет обратный отсчет
    this.transportId = transportId;
    this.status = status;
    this.str = '#' + id;
    this.minUpdate = 10000;
    if ($(this.str).length > 0) {
        this.container = document.getElementById(id);
        this.numOfDays = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ]; // установили количество дней для месяцев
        this.borrowed = 0;   //заимствованные
        this.years = 0, this.months = 0, this.days = 0;
        this.hours = 0, this.minutes = 0, this.seconds = 0;
        this.updateNumOfDays(); // устанавливает количество дней в феврале текущего года
        this.updateCounter();
        var _this = this;
        // каждые 60 сек
        this.updateInterval = setInterval(function(){_this.reloadCounter();}, this.minUpdate);
    }
  },
  // устанавливает количество дней в феврале текущего года
  updateNumOfDays: function() {
    var dateNow = this.dateNow;
    var currYear = dateNow.getFullYear();
    if ( (currYear % 4 == 0 && currYear % 100 != 0) || currYear % 400 == 0 ) {
        this.numOfDays[1] = 29; //кол-во дней в феврале высокосного года
    }
    var self = this;
    setTimeout(function(){self.updateNumOfDays();}, (new Date((currYear + 1), 0, 1) - dateNow)); // количество дней в феврале будет проверено через год 1 января //////(было 2 февраля)
  },
  datePartDiff: function(now, then, MAX){ //cur_seconds, end_seconds, max
    var diff = then - now - this.borrowed;
    this.borrowed = 0;
    if ( diff > -1 ) return diff; // разница > или = 0
    this.borrowed = 1;
    return (MAX + diff);
  },
  calculate: function(){
    var futureDate = this.endDate;
    this.dateNow.setSeconds(this.dateNow.getSeconds() + 1);
    var currDate = this.dateNow;
    this.seconds = this.datePartDiff(currDate.getSeconds(), futureDate.getSeconds(), 60);
    this.minutes = this.datePartDiff(currDate.getMinutes(), futureDate.getMinutes(), 60);
    this.hours = this.datePartDiff(currDate.getHours(), futureDate.getHours(), 24);
    this.days = this.datePartDiff(currDate.getDate(), futureDate.getDate(), this.numOfDays[currDate.getMonth()]);
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
  reloadCounter: function(){
        var _this = this;
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/transport/getCurTime',
            cache: false,
            data:{
                endDate: this.endDate,
            },
            success: function(response) {
                _this.dateNow = new Date(response.date);
                var futureDate = _this.endDate;
                _this.dateNow.setSeconds(_this.dateNow.getSeconds() + 1);
                var currDate = _this.dateNow;
                _this.seconds = _this.datePartDiff(currDate.getSeconds(), futureDate.getSeconds(), 60);
                _this.minutes = _this.datePartDiff(currDate.getMinutes(), futureDate.getMinutes(), 60);
                _this.hours = _this.datePartDiff(currDate.getHours(), futureDate.getHours(), 24);
                _this.days = _this.datePartDiff(currDate.getDate(), futureDate.getDate(), _this.numOfDays[currDate.getMonth()]);
                _this.months = _this.datePartDiff(currDate.getMonth(), futureDate.getMonth(), 12);
                _this.years = _this.datePartDiff(currDate.getFullYear(), futureDate.getFullYear(),0);

                if(parseInt(response.minUpdate) != this.minUpdate) {
                    var self = _this;
                    _this.minUpdate = parseInt(response.minUpdate);
                    clearInterval(_this.updateInterval);
                    _this.updateInterval = setInterval(function(){self.reloadCounter();}, _this.minUpdate);  
                }
            }
        });
    
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
              years = "<span class='t-year'><strong>" + this.years + "</strong> " + title + " </span>";
          }
          if(this.months > 0) {
              var title = 'месяцев';
              var modulo = this.months%10;
              if(this.months == 1) {
                  title = 'месяц';
              } else if(this.months == 2 || this.months == 3 || this.months == 4) {
                  title = 'месяца';
              }
              months = "<span class='t-month'><strong>" + this.months + "</strong> " + title + " </span>";
          }
          if(this.days > 0) {
              var title = 'дней';
              var modulo = this.days%10;
              var intPart = Math.floor(this.days/10);
              if(modulo == 1 && intPart != 1) {
                  title = 'день';
              } else if((modulo == 2 || modulo == 3 || modulo == 4) && intPart != 1) {
                  title = 'дня';
              }
              days = "<span class='t-days'><strong>" + this.days + "</strong> " + title + " </span>";
          }

          this.container.innerHTML = years + months + days + ' <span class="t-time">' + this.hours + ':' + this.minutes + ':' + this.seconds + '</span>';

          if(typeof rateList.data !== "undefined" && typeof rateList.data.status !== "undefined") this.status = parseInt(rateList.data.status);
          if ( this.endDate > this.dateNow && this.status ) { //проверка не обнулился ли таймер
              var self = this;
              setTimeout(function(){self.updateCounter();}, 1000);
          } else {
              $(".ui-dialog-content").dialog( "close" );
              if($('.r-submit').length) {
                  $('.r-submit').addClass('disabled');
                  $('.rate-wrapper').slideUp("slow");
              }    
              this.container.innerHTML = '<span class="t-closed"><img class="small-loading" src="/images/loading-small.gif"/>Обработка результатов</span>';
              
              $('#t-container').removeClass('open');
              var _this = this;
              clearInterval(this.updateInterval);

              if(this.endDate < this.dateNow) {
                  refreshIntervalId = setInterval(function(){_this.addCloseLabel(_this.container, _this.transportId, refreshIntervalId);}, 5000);  
              } else { // this.endDate == this.dateNow
                  setTimeout(function(){_this.addCloseLabelWithDelay(_this.container, _this.transportId, _this.refreshIntervalId);}, 120000);
              }
              /********************/
              // Доп время
              // checkForAdditionalTimer(this.transportId, this.status, this.container);
          }
       }
    },  
    addCloseLabelWithDelay: function(container, transportId, refreshIntervalId) {
        var _this = this;
        refreshIntervalId = setInterval(function(){_this.addCloseLabel(container, transportId, refreshIntervalId);}, 5000);
    },
    addCloseLabel: function(container, transportId, refreshIntervalId) {
        var containerId = container.getAttribute('id');
        var index = containerId.indexOf('counter-');
        if(index > -1) id = containerId.substring(8);
        else id = transportId;
        $.ajax({
            type: 'POST',
            url: '/transport/checkForTransportStatus',
            dataType: 'json',
            data:{
                id: id,
            },
            success: function(response) {
               if(response == 0) {
                   if(containerId == 't-container') $('#'+containerId).removeClass('open');
                   container.innerHTML = '<span class="t-closed">Перевозка закрыта</span>';
                   if(typeof refreshIntervalId != 'undefined') clearInterval(refreshIntervalId);
                   /* hide transport from the list */
                   var parent = $('#'+containerId).parent().parent().parent();
                   if(parent.hasClass('transport')) parent.addClass('hide');
                   /* end hide transport */
               }
        }});
    }
};

function checkForAdditionalTimer(transportId, status, container)
{
    var id = container.getAttribute('id');
    $.ajax({
         type: 'POST',
         url: '/transport/checkForAdditionalTimer',
         dataType: 'json',
         data:{
             id: transportId,
         },
         success: function(response) {
            if(response.end) {
                var timer = new Timer();
                timer.init(response.now, response.end, id, status, transportId);
                $('#'+id).addClass('add-t');
            } else {
                var label = $('#'+id);
                label.removeClass('add-t');
                if(id == 't-container') label.removeClass('open');
                /* hide transport from the list */
                var parent = label.parent().parent().parent();
                if(parent.hasClass('transport')) parent.addClass('hide');
                /* end hide transport */
                container.innerHTML = '<span class="t-closed">Перевозка закрыта</span>';
                if($('.r-submit').length) {
                    $('.r-submit').addClass('disabled');
                    $('.rate-wrapper').slideUp("slow");
                }
            }
    }});
}
var rateList = {
    init : function(){
        this.container = $("#rates");
        var element = $( "#rate-price" );
        //if(typeof(rateList.data.socket) !== 'undefined' && parseInt(rateList.data.status)) {
        if(typeof(rateList.data.socket) == 'undefined') { // load with ajax rates for admin and logist
            rateList.load(this.container);
        } else { //   if( && parseInt(rateList.data.userId)) {
            rateList.data.socket.on('setRate', function (data) {
                if(data.dateCloseNew)rateList.data.dateCloseNew = data.dateCloseNew;
                
                var initPrice = parseInt($('#rate-price').attr('init'));
                if (data.transportId == rateList.data.transportId) {
                    var price = data.price;
                    if(rateList.data.nds) {
                        price = Math.ceil(price * (100 + rateList.data.nds*100) / 100);
                        if(price%100!=0)price -= price%100;
                    }
                    
                    var element = rateList.createElement(initPrice, data.date, price, '', data.company);
                    
                    var containerElements = $.trim(rateList.containerElements);
                    if(containerElements !== '') {
                        $('#rates').html(containerElements);
                        $('#rates').prepend(element);
                        rateList.containerElements = $('#rates').html();
                    } else {              
                        $('#rates').prepend(element);
                        rateList.containerElements = $('#rates').html();
                    }
                }
            });
            
            rateList.data.socket.on('loadRates', function (data) {
                $("#r-preloader").remove();//css('display', 'none');
                for(var j = 0; j < data.rows; j++) {
                    //
                    var obj = {
                        price: data.arr[j][1],
                        time: data.arr[j][2],
                        company: data.arr[j][3],
                        with_nds: 0
                    };
                    rateList.add(obj);
                }
            });
            
            rateList.data.socket.on('errorRate', function (data) {
                $('#maxRateVal').text(parseInt(data.price));
                $("#errorRate").dialog("open");
            });
            
            rateList.data.socket.on('closeRate', function (data) {
                $('#closeTr').text(data.response);
                $("#closeRate").dialog("open");
            });
            
            // rateList.data.socket.on('error', function (data) {
            //     $('#text').text('Произошла ошибка, пожалуйста перезагрузите страницу');
            //     $("#errorSocket").dialog("open");
            // });

            //****** Сообщение *********
            
            $( "#rate-up" ).on('click', function() {
                var newRate = parseInt(element.val()) + rateList.data.priceStep;
                if(newRate <= element.attr('init')) {
                    element.val(newRate);
                    if($('#rate-down').hasClass('disabled'))$('#rate-down').removeClass('disabled');
                }
                if(newRate + rateList.data.priceStep > element.attr('init')) $(this).addClass('disabled');
            });

            $( "#rate-up" ).mousedown(function(e) {
                clearTimeout(this.downTimer);
                this.downTimer = setInterval(function() {
                    $( "#rate-up" ).trigger('click');                
                }, 150);
            }).mouseup(function(e) {
                clearInterval(this.downTimer);
            });

            $( "#rate-down" ).on('click', function() {
                var step = rateList.data.priceStep;
                var newRate = element.val() - step;
                if(newRate > 0) {
                    element.val(newRate);
                    if($('#rate-up').hasClass('disabled'))$('#rate-up').removeClass('disabled');
                }
                if( (newRate - step) <= 0 ) {
                    $(this).addClass('disabled');
                }
            });

            $( "#rate-down" ).mousedown(function(e) {
                clearTimeout(this.downTimer);
                this.downTimer = setInterval(function() {
                    $( "#rate-down" ).trigger('click');                
                }, 150);
            }).mouseup(function(e) {
                clearInterval(this.downTimer);
            });

            $( ".r-submit" ).click(function() {
                if(socket.socket.connected) {
                    if(!$(this).hasClass('disabled')) {
                        $.ajax({
                            type: 'POST',
                            url: '/user/transport/checkStatus',
                            dataType: 'json',
                            data:{
                                id: rateList.data.transportId
                            },
                            success: function(response) {
                                if(response.allow) { 
                                    $('#setPriceVal').text(parseInt($( "#rate-price" ).val()));
                                    $("#addRate").dialog("open");
                                    rateList.data.time = response.time;
                                } else {
                                    $('#curStatus').text(response.status);
                                    $("#errorStatus").dialog("open");
                                }
                        }});
                    }
                } else {
                    $("#errorSocket").dialog("open");
                }
            });

            $('#setRateBtn').live('click', function() {
                //if(!troubleWithSocket) {
                    $('#addRate').dialog('close');

                    if(rateList.data.defaultRate) $('#rates').html('');
                    //$('#t-error').html('');

                    var price = parseInt($('#rate-price').val());
                    if(rateList.data.nds) {
                        price = price * 100/(100 + rateList.data.nds*100);
                    }

                    $(this).attr('init', price);

                    var time = getTime();

                    rateList.data.socket.emit('setRateToServer',{
                        transportId: rateList.data.transportId,
                        dateClose : rateList.data.dateClose,
                        dateCloseNew : rateList.data.dateCloseNew,
                        userId: rateList.data.userId,
                        company: rateList.data.company,
                        price : price,
                        type : rateList.data.transportType,
                        timedate : rateList.data.time,
                        x: 854
                    }); 
                //}
            });

            $('#rate-price').blur(function() {
                var inputVal = parseInt($(this).val());
                var maxVal = $(this).attr('init');
                var kratnoe = rateList.data.priceStep;
                if(inputVal > maxVal) $(this).val(maxVal);
                if(inputVal <= 0) $(this).val(kratnoe);
                
                var residue = inputVal % kratnoe;
                if(residue != 0) {
                    if((inputVal - residue) > 0) $(this).val(inputVal - residue);
                    else $(this).val(kratnoe);
                    inputVal = parseInt($(this).val());
                }

                if((parseInt($(this).val()) - kratnoe) <= 0) $('#rate-down').addClass('disabled');
                else $('#rate-down').removeClass('disabled');
                if((parseInt($(this).val()) + kratnoe) > $(this).attr('init')) {
                    $('#rate-up').addClass('disabled');
                } else $('#rate-up').removeClass('disabled');
            });

            $(document).keypress(function(e) {
                if (e.which == 13) {
                    $( "#rate-price" ).trigger('blur');
                }
            });
        }      
    },
    update : function(posts, price, userName) {
        if (this.container.length > 0) {
            price = typeof price !== 'undefined' ? price : '';
            $.ajax({
                type: 'POST',
                url: '/transport/updateRates',
                dataType: 'json',
                data:{
                    id: this.data.transportId,
                    newRate: price,
                    step: this.data.priceStep
                },
                success: function(rates) {
                    if(rates.all.length) {
                        rateList.data.socket.emit('setRate',{
                             userName : userName, 
                             price : price
                        });   
                    } else {
                        //rateList.container.after('<div id="no-rates">Нет предложений</div>');
                    }
            }});
        }
    },
    load : function(posts) {
        if (this.container.length > 0) {
            $.ajax({
                type: 'POST',
                url: '/transport/updateRates',
                dataType: 'json',
                data: {
                    id: this.data.transportId,
                    newRate: '',
                    step: this.data.priceStep
                },
                success: function(rates) {
                    if(rates.all.length) {
                        var container = $("#rates");
                        var height = 49;
                        var count = 0;
                        var scrollBefore = container.scrollTop();
                        if(scrollBefore) count = scrollBefore/height;
                        
                        rateList.container.html('');
                        var initPrice = parseInt($('#rate-price').attr('init'));
                        
                        $.each( rates.all, function( key, value ) {
                            rateList.add(value, initPrice);
                        });

                        if(scrollBefore){
                            container.scrollTop(height * (count + 1));
                        }
                        
                        if(rates.price) {
                            var value = parseInt(rates.price);// - (rateList.data.priceStep + rateList.data.priceStep * rateList.data.nds);
                            if(rateList.data.nds){
                               value += value * rateList.data.nds;
                            }
                            var step = rateList.data.priceStep + rateList.data.priceStep * rateList.data.nds;
                            
                            //var price = $("#rate-price");
                        }
                    } else {
                        rateList.container.after('<div id="no-rates">Нет предложений</div>');
                    }
            }});
        }
    },
    add : function(rate, initPrice) {
        var time = '';
        var id = 0;
        var price = parseInt(rate.price);
        price = Math.ceil(price + price * this.data.nds);
        
        if (rate.id) id = rate.id;
        var element = this.createElement(initPrice, rate.time, price, id, rate.company, parseInt(rate.with_nds), parseInt(rate.price));
        
        this.container.prepend(element);
    },
    createElement : function(initPrice, date, price, id, company, nds, ratePrice) {
        var companyName = company;
        var pos = companyName.indexOf("(");
        if(pos > -1) companyName = companyName.substring(0, pos);
        if(initPrice < price) {
            $('#rate-price').attr('init', price);
        }
        
        var newElement = "<div class='rate-one'>";
        
        if(id) {
            newElement = "<div id='" + id + "' class='rate-one'>";
        }
        
        newElement += "<div class='r-o-container'>" + 
                "<span>" + date + "</span>" + 
                "<div class='r-o-user'>" + companyName;
        
        newElement += "</div>" +
            "</div>"
        ;
        
        if(nds && rateList.data.trType){
            var withNds = Math.ceil(ratePrice + ratePrice * rateList.data.ndsValue);
            newElement += "<div class='price-container'>" + 
                "<div class='r-o-price'>" + parseInt(price) + rateList.data.currency + 
                "</div>" +
                "<div class='r-o-nds'>" + '(c НДС: ' + withNds + rateList.data.currency + ') '+ 
                "</div>" +
            "</div>";
        } else {
            newElement += "<div class='r-o-price'>" + parseInt(price) + rateList.data.currency + "</div>";
        }
        newElement += "</div>";
        
        return newElement;
    },
    getContainerHeight : function() {
        var h=0;
        this.container.find('.rate-one').each(function(k){
            h += $(this).outerHeight();
        });
        return h;
    }
};
function updateEventCount(userId){
    setInterval(function(){
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/transport/getevents/',
            cache: false,
            data:{
                userId: userId,
            },
            success: function(response) {
                if(parseInt(response)) $('#event-counter').html(response); 
                else $('#event-counter').html(''); 
            }
        });
    }, 120000);
}
var menu = {
    init : function() {
        var activeElement = parseInt(sessionStorage.getItem('menu'));
        var activeSubElement = parseInt(sessionStorage.getItem('submenu'));
        
        if(activeElement != 'null') {
             if (this.countSubmenuElem != 'null') {
                if(activeElement >= this.countSubmenuElem) activeElement = activeElement + this.countSubmenuElem;
             }
             $('.user-menu li').eq(activeElement).find('a:first').addClass('menu-active');
        }
        
        if(activeSubElement != 'null') {
            activeSubElement = parseInt(activeSubElement) - 2;
            $('#submenu li').eq(activeSubElement).find('a:first').addClass('menu-active');
        } 
        
        $( "#submenu>li>a" ).click(function() {
            if(!$(this).hasClass('menu-active')) {
                $("a.menu-active").removeClass('menu-active');
                $(this).addClass('menu-active');
                sessionStorage.setItem('menu', null);
                sessionStorage.setItem('submenu', $(this).parents("li").index());
            }
        });
        
        $( ".user-menu>li>a" ).click(function() {
            if(!$(this).hasClass('menu-active')) {
                $( "a.menu-active" ).removeClass('menu-active');
                $(this).addClass('menu-active');
                sessionStorage.setItem('submenu', null);
                if(!$(this).hasClass('exit') && !$(this).hasClass('admin')) {
                    sessionStorage.setItem('menu', $(this).parents("li").index());
                } else {
                    sessionStorage.setItem('menu', null);
                }
            }
        });
    }
};
$(window).load(function(){
    $(".items").mCustomScrollbar({
        scrollButtons:{
            enable:true
        }
    });
});
    
(function($){
    $('#dialog-connect').live('click', function() {
        $("#modalDialog").dialog("open");
    });

    $('.ui-widget-overlay').live('click', function() {
        $(".ui-dialog-content").dialog( "close" );
    });

    $( "#abordRateBtn" ).live('click', function() {
        $(".ui-dialog-content").dialog( "close" );
    });

    $( "#errorRate .btn" ).live('click', function() {
        $(".ui-dialog-content").dialog( "close" );
    });

    $( "#errorStatus .btn" ).live('click', function() {
        $(".ui-dialog-content").dialog( "close" );
    });
    $( "#closeRate .btn" ).live('click', function() {
        $(".ui-dialog-content").dialog( "close" );
    });
    $( "#errorSocket .btn" ).live('click', function() {
        $(".ui-dialog-content").dialog( "close" );
    });
})(jQuery);


(function($) {
    $.onlineEvent = function(obj) {
        var obj = $.extend({
            time: 5000, 
            speed: 'slow', 
            msg: null, 
            id: null, 
            className: null, 
            evented: false,
            position:{ top:0,right:0 } 
        }, obj);

        var message = $('#online-event');
        
        message.css('position', 'fixed').css({ right:'auto', left:'auto', top:'auto', bottom:'auto'}).css(obj.position);
        var event = $('<div class="event"></div>');
        message.append(event); 
        event.click(function(){ 
            event.fadeOut(obj.speed,function(){ 
                $(this).remove();
            });
        });
        if (obj.className) event.addClass(obj.className); 
        event.html(obj.msg);
        
        setTimeout(function(){ 
            event.fadeOut(obj.speed,function(){ 
                $(this).remove();
            });
        }, obj.time);
     };
})(jQuery);
