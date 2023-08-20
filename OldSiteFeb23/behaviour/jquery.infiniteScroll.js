(function($) {

$.fn.infiniteScroll = function(options) {
	var defaults = {
		slideSpeed:500//,
		//slideCount:3
		//width:,
		//height:,
	}
	var o = $.extend(defaults, options);
	
	return this.each(function() {
		var item = $(this);
		var myChildren = item.children();
		
		if(!o.width) {
			o.width = item.width();
		}
		o.childWidth = 0;
		totalWidth = 0;
		myChildren.each(function() {
			o.childWidth = Math.max($(this).outerWidth(true), o.childWidth);
			totalWidth += $(this).outerWidth(true);
		});
		if(!o.height) {
			o.height = 0;
			myChildren.each(function() {
				o.height = Math.max ($(this).height(), o.height);
			});
		}
		if(item.width() < totalWidth) {
			item.children().wrapAll('<div class="sliderDiv"><div class="sliderContainer" /></div>');
			var sd = item.prepend('<div class="scrollLeftBtn"></div><div class="scrollRightBtn"></div>').children('.scrollRightBtn').css('left',o.width).end().children('.sliderDiv');
			var sc = sd.css({
				width:o.width,
				height:o.height,
				overflow:"hidden",
				position:"relative",
				'text-align':"left"
			}).children().css('position','absolute').end().children('.sliderContainer');
			o.childrenWidth = 0;
			o.childrenPerWidth = 0;
			sc.children().each(function() {
				var cItem = $(this);
				o.childrenWidth += cItem.outerWidth(true);
				if(o.childrenWidth <= o.width + (cItem.outerWidth(true) - cItem.width())) {
					o.childrenPerWidth++;
				}
			});
			
			sc.css('width',o.childrenWidth);
			sd.append(sc.clone(true).css('left',o.childrenWidth)).append(sc.clone(true).css('left',-o.childrenWidth));
			
			if(!o.slideCount) {
				o.slideCount = o.childrenPerWidth;
			}
			
			item.children('.scrollLeftBtn').click(function(e) {
				sliderLeft();
				e.preventDefault();
			}).siblings('.scrollRightBtn').click(function(e) {
				sliderRight();
				e.preventDefault();
			});
			
			item.bind('touchstart', function(e) {
				var touch = e.originalEvent.touch || e.originalEvent.touches[0];
				tstartX = touch.pageX;
				tstartY = touch.pageY;
			}).bind('touchmove', function(e) {
				var touch = e.originalEvent.touch || e.originalEvent.touches[0];
				tX = touch.pageX;
				tY = touch.pageY;
				
				if(tX < tstartX - 10 || tX > tstartX + 10) {
					e.preventDefault();
				}
			}).bind('touchend', function(e) {
				if(tstartX && tX) {
					if(tX < tstartX - 20 || tX > tstartX + 20) {
					
						var origTrans = o.transition;
						if(tX < tstartX - 20) {
							o.transition = "left";
							item.children('.scrollRightBtn').click();
						}
						if(tX > tstartX + 20) {
							o.transition = "left";
							item.children('.scrollLeftBtn').click();
						}
						o.transition = origTrans;
					}
					tstartX = tstartY = tX = tY = false;
				}
			});
			
			$(window).bind('load resize', function() {
				item.find('.sliderDiv, .sliderContainer').css({width:item.width()});
				o.childWidth = 0;
				o.childHeight = 0;
				o.childrenWidth = 0;
				o.slideCount = 1;
				childrenItems = item.find('.sliderContainer').children()
				if(document.documentElement.clientWidth <= 600) {
					childrenItems.width(item.width());
				} else { 
					childrenItems.css('width','');
				}
				myChildren.each(function() {
					var cItem = $(this);
					o.childrenWidth += cItem.outerWidth(true);
					o.childWidth = Math.max(cItem.outerWidth(true), o.childWidth);
					o.childHeight = Math.max(cItem.outerHeight(true), o.childHeight);
				});
				var step = Math.floor(item.width() / o.childWidth);
				if(step > 1) {
					o.slideCount = step;
				}
				var fc = item.find('.sliderContainer').css('width',o.childrenWidth).filter(':first');
				fc.css('left',0).next().css('left',fc.width()).next().css('left',-fc.width());
				item.find('.sliderDiv').css('height',o.childHeight);
			}).resize();
		}
		
		function sliderLeft() {
			if(!o.width) {
				o.width = item.width();
			}
			if(sd.children('.sliderContainer:animated').length == 0) {
				sd.children('.sliderContainer').each(function() {
					var slider = $(this);
					var pos = slider.position();
					slider.animate({left: pos.left + (o.slideCount*o.childWidth)}, o.slideSpeed, function() {
						var slider = $(this);
						var pos = slider.position();
						if(pos.left > o.width && pos.left > slider.width()) {
							slider.css('left',pos.left - (slider.width() * 3));
						}
					});
				})
			}
		}
		
		function sliderRight() {
			if(!o.width) {
				o.width = item.width();
			}
			if(sd.children('.sliderContainer:animated').length == 0) {
				sd.children('.sliderContainer').each(function() {
					var slider = $(this);
					var pos = slider.position();
					slider.animate({left: pos.left - (o.slideCount*o.childWidth)}, o.slideSpeed, function() {
						var pos = slider.position();
						if(pos.left < -o.width * 2 && pos.left < -slider.outerWidth()) {
							slider.css('left',pos.left + (slider.outerWidth() * 3));
						}
					});
				});
			}
		}
		
	});
};
}) ($)