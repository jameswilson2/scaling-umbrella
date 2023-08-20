
function Scrollable(container, options){

	options = options || {};
	
	EventEmitter.call(this);
	
	var styles = {};
	styles.overflow = "hidden";
	styles.position = "relative";
	$(container).css(styles);
	
	$(container).wrapInner(document.createElement("div"));
	
	this._container = container;
	
	this._viewWidth = $(container).width();
	this._viewHeight = $(container).height();
	
	var plate = {};
	
	plate.element = container.firstChild;
	plate.element.style.position = "absolute";
	
	plate.width = $(plate.element).width();
	plate.height = $(plate.element).height();
	
	plate.x = 0;
	plate.y = 0;
	
	plate.minX = -plate.width + this._viewWidth;
	plate.maxX = 0;
	
	plate.minY = -plate.height + this._viewHeight;
	plate.maxY = 0;
	
	this._plate = plate;
}

inherits(Scrollable, EventEmitter);

Scrollable.prototype.scrollTo = function(x, y){
	
	x = -x;
	y = -y;
	
	function limit(lower, x, upper){
		if(x< lower) return lower;
		else if(x > upper) return upper;
		else return x;
	}
	
	this._plate.x = limit(this._plate.minX, x, this._plate.maxX);
	this._plate.y = limit(this._plate.minY, y, this._plate.maxY);
	
	$(this._plate.element).css({
		left: this._plate.x + "px",
		top: this._plate.y + "px"
	});
	
	this.emit("scroll", -this._plate.x, -this._plate.y);
}

Scrollable.prototype.scrollBy = function(x, y){
	x = -this._plate.x + x;
	y = -this._plate.y + y;
	this.scrollTo(x, y);
}

Scrollable.prototype.getRemaining = function(){
	var plate = this._plate;
	return {
		top: plate.y - plate.minY,
		right: plate.maxX - plate.x,
		bottom: plate.maxY - plate.y,
		left: plate.x - plate.minX
	}
}

Scrollable.prototype.getViewSize = function(){
	return {
		width: this._viewWidth,
		height: this._viewHeight
	}
}

Scrollable.prototype.getContentSize = function(){
	return {
		width: this._plate.width,
		height: this._plate.height
	}
}

function Scrollbar(container, scrollable, options){

	options = options || {};
	
	var viewSize = scrollable.getViewSize();
	var contentSize = scrollable.getContentSize();
	
	var bar = document.createElement("div");
	bar.className = "horizontal-scrollbar";
	
	$(bar).css({
		width: viewSize.width + "px",
		height:"2em",
		background:"silver",
		position:"relative"
	});
	
	container.appendChild(bar);
	
	var button = document.createElement("div");
	bar.appendChild(button);
	$(button).css({position:"relative"});
	
	var dragStartX = null;
	
	function dragButton(event){
		var delta = event.clientX - dragStartX;
		var remaining = scrollable.getRemaining();
		if((delta > 0 && !remaining.left) || (delta < 0 && !remaining.right)) return;
		scrollable.scrollBy((delta/viewSize.width)*contentSize.width, 0);
		document.title = event.clientX - dragStartX;
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
		
		var left = (remaining.right / contentSize.width) * viewSize.width;
		var right = (remaining.left / contentSize.width) * viewSize.width;
		
		$(button).css({
			left: Math.round(left) + "px",
			width: ((viewSize.width/contentSize.width) * viewSize.width) + "px",
			height:"2em",
			background:"black"
		});
	}
	
	update();
	scrollable.on("scroll", update);
}
