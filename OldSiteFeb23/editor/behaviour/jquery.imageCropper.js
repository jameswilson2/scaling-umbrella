(function($) {
$.fn.imageCropper = function(options) {
	var defaults = {
		resizable:true,
		xmlurl:'configuration.xml',
		controls:'#controls',
		imgurl:'',
		type:'',
		extra:'',
		filename:'',
		width:'auto',
		height:'auto',
		showSizeSelect:true,
		action:'imagery/resize_process.php'
	};
	var o = $.extend(defaults, options);
	
	var iereg = new RegExp('MSIE [2-6]', 'g');
	var thisBrowser = navigator.appVersion;
	var oldIE = iereg.exec(thisBrowser);
	var scale = 1;
	var imageWidth = 0;
	var imageHeight = 0;
	var xmlconfig;
	var el;
	
	return this.each(function() {
		el = $(this);
		
		$(window).load( function() {
			$.ajax({
				url:o.xmlurl,
				dataType:'xml',
				success:function(data) {
					xmlconfig = $(data).find('item');
					initImageCropper();
				}
			});
		});
		
	});
	
	function initImageCropper() {
		var controls = $(o.controls);
		var img = el.children('img');
		
		var sizePixels = '<input type="hidden" id="cropwidth" name="width" /><input type="hidden" id="cropheight" name="height" />';
		if(o.resizable) {
			sizePixels = ' Width:<input type="text" id="cropwidth" name="width" /> Height:<input type="text" id="cropheight" name="height" />';
		}
		
		var reset = '<input type="button" value="Reset" id="resetButton" /> '
		if(!o.resizable || !o.showSizeSelect) {
			reset = '';
		}
		
		controls.html('<form class="inlineForm" action="'+o.action+'" method="get"><input type="hidden" id="filename" name="filename" value="'+o.filename+'" /><input type="hidden" id="extra" name="extra" value="'+o.extra+'" /><input type="hidden" id="type" name="type" value="'+o.type+'" /><input type="hidden" id="photo_width" name="photo_width" /><input type="hidden" id="photo_height" name="photo_height" /><input type="hidden" id="mask_x" name="mask_x" /><input type="hidden" id="mask_y" name="mask_y" />'+sizePixels+' '+reset+'<input type="button" value="Enlarge" id="enlargeButton" /> <input type="button" value="Reduce" id="reduceButton" /> <input type="button" value="Fit to box" id="fittoboxButton" /> <input <input type="submit" value="Finish and Crop" id="valueButton" /></form>');
		
		imageWidth = img.width();
		imageHeight = img.height();
		
		var items = xmlconfig;
		if(items.length > 0 && o.showSizeSelect) {
			var sizeOptions = "<select id='size'>";
			var defaultW = defaultH = 0;
			items.each(function(i,a) {
				var $a = $(a);
				if($a.attr('width') < imageWidth && $a.attr('height') < imageHeight) {
					if(i==0) {
						defaultW = $a.attr('width');
						defaultH = $a.attr('height');
						if(o.width == 'auto') {
							$('#cropwidth').val($a.attr('width'));
						} else {
							$('#cropwidth').val(o.width);
							defaultW = o.width;
						}
						
						if(o.height == 'auto') {
							$('#cropheight').val($a.attr('height'));
						} else {
							$('#cropheight').val(o.height);
							defaultH = o.height;
						}
					}
					sizeOptions += "<option data-width='"+$a.attr('width')+"' data-height='"+$a.attr('height')+"'>"+$a.attr('name')+"</option>";
				}
			});
			if(o.resizable) {
				sizeOptions += "<option>Custom size</option>";
			}
			sizeOptions += "</select>";
			controls.prepend(sizeOptions);
			$('#size').change(function() {
				var $s = $(this);
				var item = $s.children('option:eq('+this.selectedIndex+')');
				if(item.attr('data-width')) {
					scale = 1;
					resizeImage();
					$('#cropwidth').val(item.attr('data-width'));
					$('#cropheight').val(item.attr('data-height'));
					$('#resetButton').click();
					imageCropper(el[0].parentNode, item.attr('data-width'), item.attr('data-height'), (imageWidth*scale / 2) - (item.attr('data-width') /2), (imageHeight*scale / 2) - (item.attr('data-height') /2));
				}
			}).change();
		} else {
			$('#cropwidth').val(o.width);
			$('#cropheight').val(o.height);
			$('#resetButton').click();
			imageCropper(el[0].parentNode, o.width, o.height, (imageWidth*scale / 2) - (o.width /2), (imageHeight*scale / 2) - (o.height /2));
		}
		
		if(o.resizable) {
			el.selectable( {
				stop: function(event, ui) {
					if($('#size').length > 0) {
						var size = $('#size');
						size[0].selectedIndex = size.children().length-1;
					}
					var dims = $('.ui-selectable-helper');
					var pos = dims.position();
					var cropper = $('.cropper').offset();
					$('#cropwidth').val(dims.width());
					$('#cropheight').val(dims.height());
					imageCropper(event.target.parentNode, dims.width(), dims.height(), pos.left - cropper.left, pos.top - cropper.top);
				}
			});
		}
		$('#resetButton').click( function() {
			$('.cropper').removeClass('cropObject').find('.cover, .resizer, #preview').remove();
			scale = 1;
			$('#valueButton').hide();
			resizeImage();
		});
		$('#valueButton').click(function() {
			var res = $('.cropper').children('.resizer');
			if(res.length > 0) {
				var pos = res.position();
				$('#photo_width').val(imageWidth*scale);
				$('#photo_height').val(imageHeight*scale);
				$('#mask_x').val(pos.left);
				$('#mask_y').val(pos.top);
			}
		});
		$('#cropwidth').blur( function() {
			var size = $('#size');
			size[0].selectedIndex = size.children().length-1;
			textResize("width");
		}).keypress(captureEvt);
		$('#cropheight').blur( function() {
			var size = $('#size');
			size[0].selectedIndex = size.children().length-1;
			textResize("height");
		}).keypress(captureEvt);
		var enlarging;
		var reducing;
		$('#enlargeButton').mousedown(function() {
			if(enlarging)
				clearInterval(enlarging)
			var enlarge = function() {
				var res = $('.resizer');
				scale += 0.01;
				if(((imageWidth * scale > $('#cropwidth').val() && imageHeight * scale > $('#cropheight').val())) || (o.resizable && size[0].selectedIndex == size.children().length-1)) {
					resizeImage();
				} else {
					scale -= 0.01;
				}
			};
			enlarge();
			enlarging = setInterval(enlarge,100);
		}).bind('mouseup mouseleave',function() {
			clearInterval(enlarging);
		});
		$('#reduceButton').mousedown(function() {
			if(reducing)
				clearInterval(reducing)
			var reduce = function() {
				var size = $('#size');
				var res = $('.resizer');
				scale -= 0.01;
				if(((imageWidth * scale > $('#cropwidth').val() && imageHeight * scale > $('#cropheight').val())) || (o.resizable && size[0].selectedIndex == size.children().length-1)) {
					resizeImage();
				} else {
					scale += 0.01;
				}
			};
			reduce();
			reducing = setInterval(reduce,100);
		}).bind('mouseup mouseleave',function() {
			clearInterval(reducing);
		});
		
		$('#fittoboxButton').click(function() {
			var res = $('.cropper').children('.resizer');
			if(res.length > 0) {
				var pos = res.position();
				var widthScale = (res.width()+2)/imageWidth;
				var heightScale = (res.height()+2)/imageHeight;
				scale = Math.max(widthScale,heightScale);
				resizeImage();
				res.css({
					'left':(imageWidth*scale-res.width())/2,
					'top':(imageHeight*scale-res.height())/2
				});
				positionShadow(res);
			}
		});
		
		$(document).keypress(function(e) {
			var res = $('.resizer');
			var pos = res.position();
			switch(e.keyCode) {
				case 38: 
					if(pos.top > 0) {
						res.css('top',pos.top - 1);
						positionShadow(res);
					}
					break;
				case 40:
					if(pos.top + res.height() < imageHeight - 2) {
						res.css('top',pos.top + 1);
						positionShadow(res);
					}
					break;
				case 37: 
					if(pos.left > 0) {
						res.css('left',pos.left - 1);
						positionShadow(res);
					}
					break;
				case 39:
					if(pos.left + res.width() < imageWidth - 2) {
						res.css('left',pos.left + 1);
						positionShadow(res);
					}
					break;
			}
		});
	}

	function textResize(mode) {
		var crop = $('.cropper');
		var res = crop.children('.resizer');
		var size = $('#size');
		$('#cropwidth').val($('#cropwidth').val().replace(/[^\d]/gi,''));
		$('#cropheight').val($('#cropheight').val().replace(/[^\d]/gi,''));
		if(res.length > 0) {
			if(parseInt($('#cropwidth').val()) > 5 && parseInt($('#cropheight').val()) > 5) {
				if(parseInt($('#cropwidth').val())-2 > crop.children('.fullImage').width())
					$('#cropwidth').val(crop.children('.fullImage').width())
				res.width(parseInt($('#cropwidth').val())-2);
				if(res.position().left + parseInt($('#cropwidth').val()) -2 > crop.children('.fullImage').width()) {
					res.css('left', (crop.children('.fullImage').width() - parseInt($('#cropwidth').val())) + "px");
				}
				if(parseInt($('#cropheight').val())-2 > crop.children('.fullImage').height())
					$('#cropheight').val(crop.children('.fullImage').height())
				res.height(parseInt($('#cropheight').val())-2);
				if(res.position().top + parseInt($('#cropheight').val()) -2 > crop.children('.fullImage').height()) {
					res.css('top', (crop.children('.fullImage').height() - parseInt($('#cropheight').val())) + "px");
				}
			}
			positionHandles(res);
		}
	}

	function imageCropper(target, w, h, l, t) {
		var tgt = $(target);
		var img = tgt.children('.fullImage');
		tgt.addClass('cropObject').prepend('<div class="cover"></div>');
		tgt.children('.cover').css('opacity','0.5').width(img.width()).height(img.height());
		tgt.prepend('<div class="resizer"></div>');
		tgt.children('.resizer').prepend('<img alt="" src="'+img.children('img:eq(0)').attr('src')+'" />').children('img').css({
			'position':'relative',
			'left':(-l) + "px",
			'top':(-t) + "px",
			'width':imageWidth*scale,
			'height':imageHeight*scale
		});
		
		if('#preview') {
			$('#previewcontainer').css('border' ,'#000 solid 10px');
			$('#preview').css('background-image','url("'+img.children('img:eq(0)').attr('src')+'")')
		}

		$('#cropwidth').val($('#cropwidth').val().replace(/[^\d]*/gi,''));
		$('#cropheight').val($('#cropheight').val().replace(/[^\d]*/gi,''));
		
		var resizer = tgt.children('.resizer');
		resizer.css({ 'width':w+'px', 'height':h+'px', 'left':l+'px', 'top':t+'px' }).draggable( {
			containment: 'parent',
			refreshPositions: true,
			drag: dragged,
			stop: dragged
		});
		if(o.resizable) {
			resizer.resizable( {
				handles: 'all',
				containment: 'parent',
				resize: resized,
				stop: resized,
				aspectRatio: $('#constrain').attr('checked')
			});
		}
		
		positionHandles(tgt.children('.resizer'));
		if(oldIE) {
			setTimeout(function() { positionHandles($('.resizer')) }, 5);
		}
		$('#valueButton').show();
	}

	function dragged(event, ui) {
		positionShadow(event.target);
	}
	function resized(event, ui) {
		ratio = $('#cropwidth').val()/$('#cropheight').val();
		
		if($(event.target).width() > $(event.target).parent().children('.fullImage').width())
			$(event.target).width($(event.target).parent().children('.fullImage').width());
		if($(event.target).height() > $(event.target).parent().children('.fullImage').height())
			$(event.target).height($(event.target).parent().children('.fullImage').height());
		
		$('#cropwidth').val($(event.target).width()+2);
		$('#cropheight').val($(event.target).height()+2);
		
		if($('#size').length > 0) {
			var size = $('#size');
			size[0].selectedIndex = size.children().length-1;
		}
		
		positionHandles(event.target);
	}
	function positionHandles(target) {
		var startWidth = $(target).outerWidth();
		var startHeight = $(target).outerHeight();
		$('.ui-resizable-s, .ui-resizable-se, .ui-resizable-sw').css('top',(startHeight-8) + "px");
		$('.ui-resizable-ne, .ui-resizable-e, .ui-resizable-se').css('left',(startWidth-8) + "px");
		$('.ui-resizable-s, .ui-resizable-n').css('left',(startWidth/2-8) + "px");
		$('.ui-resizable-w, .ui-resizable-e').css('top',(startHeight/2-8) + "px");
		positionShadow(target);
	}

	function positionShadow(target) { 
		var parentObj = $(target).parents('.cropObject');
		var resizer = parentObj.children('.resizer');
		var cropPosition = resizer.position();
		resizer.children('img').css({
			'left':(-cropPosition.left-1) + "px",
			'top':(-cropPosition.top-1) + "px"
		});
	}

	function captureEvt(evt){
		evt = (evt) ? evt : event;
		var charCode = (evt.which) ? evt.which : evt.charCode;
		if(charCode==13) {
			$('#cropwidth').blur();
			$('#cropheight').blur();
			return false;
		}
	}

	function resizeImage() {
		$('.fullImage img, .resizer img, .cover').width(imageWidth * scale).height(imageHeight * scale);
		textResize('width');
		textResize('height');
	}
};
})($)