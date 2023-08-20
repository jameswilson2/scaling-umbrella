
function Scrollable(container){
	EventEmitter.call(this);
	container.style.overflow = "hidden";
	this._container = container;
	var self = this;
	$(this._container).scroll(function(){
		self.emit("scroll", self._container.scrollLeft, self._container.scrollTop);
	});
}

inherits(Scrollable, EventEmitter);

Scrollable.prototype.scrollTo = function(x, y){
	this._container.scrollLeft = x
	this._container.scrollTop = y
}

Scrollable.prototype.scrollBy = function(x, y){
	x = this._container.scrollLeft + x;
	y = this._container.scrollTop + y;
	this.scrollTo(x, y);
}

Scrollable.prototype.getRemaining = function(){
	return {
		top: this._container.scrollTop,
		right: (this._container.scrollWidth - this._container.clientWidth) - this._container.scrollLeft,
		bottom: (this._container.scrollHeight - this._container.clientHeight) - this._container.scrollTop,
		left: this._container.scrollLeft
	}
}

Scrollable.prototype.getViewSize = function(){
	return {
		width: this._container.clientWidth,
		height: this._container.clientHeight
	}
}

Scrollable.prototype.getScrollSize = function(){
	return {
		width: this._container.scrollWidth,
		height: this._container.scrollHeight
	}
}

function Scrollbar(container, scrollable, options){

	options = $.extend({
		axis: "vertical"
	}, options);

	
	function getViewSize(){
		return scrollable.getViewSize().width;
	}
	
	function getContentSize(){
		return scrollable.getScrollSize().width;
	}
	
	var bar = document.createElement("div");
	bar.className = "horizontal-scrollbar";
	
	$(bar).css({
		width: $(container).width() + "px",
		height:"3em",
		background:"silver",
		position:"relative"
	});
	
	container.appendChild(bar);
	
	var button = document.createElement("div");
	bar.appendChild(button);
	$(button).css({position:"relative"});
	
	var dragStartX = null;
	var scrollLeft = 0;
	var scrollTop = 0;
	
	function dragButton(event){
		var delta = event.clientX - dragStartX;
		var remaining = scrollable.getRemaining();
		document.title = remaining.right;
		if((delta > 0 && !remaining.right) || (delta < 0 && !remaining.left)) return;
		scrollLeft += (delta/$(container).width()) * getContentSize();
		scrollable.scrollTo(scrollLeft, scrollTop);
		dragStartX = event.clientX;
	}
	
	function mouseupButton(event){
		$(document).unbind('mousemove', dragButton);
		$(document).unbind('mouseup', mouseupButton);
	}
	
	// Prevent IE8 accelerator popup
	$(button).bind("selectstart", function(event){
		event.preventDefault();
	});
	
	$(button).mousedown(function(event){
		dragStartX = event.clientX;
		var remaining = scrollable.getRemaining();
		scrollLeft = remaining.left;
		scrollTop = remaining.top;
		$(document).mousemove(dragButton);	
		$(document).mouseup(mouseupButton);
		event.preventDefault();
		event.stopPropagation();
	});
	
	$(bar).mousedown(function(event){
		if(event.pageX - $(this).offset().left < $(button).position().left){
			scrollable.scrollBy(-40, 0);
		}
		else{
			scrollable.scrollBy(40, 0);
		}
	});
	
	function update(){
		
		var remaining = scrollable.getRemaining();
		var viewSize = getViewSize();
		var contentSize = getContentSize();
		var scrollBarWidth = $(container).width();
		
		var left = (remaining.left / contentSize) * scrollBarWidth;
		var right = (remaining.right / contentSize) * scrollBarWidth;
		
		$(button).css({
			left: Math.round(left) + "px",
			width: ((viewSize/contentSize) * scrollBarWidth) + "px",
			height:"3em",
			background:"black",
			cursor:"pointer"
		});
	}
	
	update();
	scrollable.on("scroll", update);
}
