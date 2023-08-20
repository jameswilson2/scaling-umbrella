
function initVerticalScrollbar(container){
	
	container.style.overflow = "auto";
	var clientHeight = container.clientHeight;
	var scrollHeight = container.scrollHeight;
	container.style.overflow = "hidden";
	if(clientHeight >= scrollHeight) return;
	$(container).wrap(document.createElement("div"));
	var wrapper = container.parentNode;
	wrapper.style.position = "relative";
	
	var LINE_JUMP = 10;
	var REPEAT_INTERVAL = 100;
	var SCROLLBAR_MARGIN = 3;
	
	var upButton = document.createElement("div");
	upButton.className = "vertical-scrollbar-up";
	$(upButton).css({
		position:"absolute",
		top:"0px",
		right:"0px"
	});
	upButton.onmousedown = function(){
		container.scrollTop = container.scrollTop - LINE_JUMP;
		untilEvent("mouseup", REPEAT_INTERVAL, function(){
			container.scrollTop = container.scrollTop - LINE_JUMP;
		});
		return false;
	}
	wrapper.appendChild(upButton);
	var upButtonHeight = $(upButton).height();
	
	var downButton = document.createElement("div");
	downButton.className = "vertical-scrollbar-down";
	$(downButton).css({
		position:"absolute",
		/*bottom:"0px",*/
		right:"0px"
	});
	downButton.onmousedown = function(){
		container.scrollTop = container.scrollTop + LINE_JUMP;
		untilEvent("mouseup", REPEAT_INTERVAL, function(){
			container.scrollTop = container.scrollTop + LINE_JUMP;
		});
		return false;
	}
	wrapper.appendChild(downButton);
	var downButtonHeight = $(downButton).height();
	
	var bar = document.createElement("div");
	bar.className = "vertical-scrollbar";
	$(bar).css({
		position:"absolute",
		top:upButtonHeight + "px",
		right:"0px",
		height:(clientHeight - (upButtonHeight + downButtonHeight)) + "px"
	});
	$(bar).mousedown(function(event){
		var y = event.pageY - $(this).offset().top;
		var scrollY = y/this.clientHeight * container.scrollHeight;
		var jump = (scrollY < container.scrollTop ? -container.clientHeight : container.clientHeight);
		var timeLimit = Math.ceil(Math.abs(scrollY - container.scrollTop)/container.clientHeight);
		var times = 0;
		
		function move(){
			if(++times > timeLimit) return;
			container.scrollTop = container.scrollTop + jump;
		}
		move();
		untilEvent("mouseup", REPEAT_INTERVAL, move);
	});
	wrapper.appendChild(bar);
	container.style.paddingRight = ($(bar).width() + SCROLLBAR_MARGIN) + "px";
	
	// Ready to position down button (bottom:0px doesn't work as expected in IE6)
	$(downButton).css({
		top:(upButtonHeight + $(bar).height()) + "px"
	});
	
	
	var button = document.createElement("div");
	button.className = "button";
	$(button).css({
		position:"relative",
		left:"0px"
	});
	bar.appendChild(button);
	
	$(button).mousedown(function(event){
		var lastY = event.clientY;
		var scrollTop = container.scrollTop;
		drag(function(event){
			var delta = event.clientY - lastY;
			if(delta < 0 && container.scrollTop == 0 || delta > 0 && scrollBottom() == 0) return;
			scrollTop += (delta / bar.clientHeight) * container.scrollHeight;
			container.scrollTop = scrollTop;
			lastY = event.clientY;
		});
		event.stopPropagation();
	});

	function update(){
		var top = (container.scrollTop / container.scrollHeight) * bar.clientHeight;
		var height = (container.clientHeight / container.scrollHeight) * bar.clientHeight;
		button.style.top = top + "px";
		button.style.height = height + "px";  
	}
	
	function drag(moveCallback){
        $(document).mousemove(moveCallback);
        function drop(){
            $(document).unbind("mousemove", moveCallback);
            $(document).unbind("mouseup", drop);
        }
        $(document).mouseup(drop);
    }
	
	function untilEvent(event, interval, callback){
		var id = window.setInterval(callback, interval);
		function finish(){
			window.clearInterval(id);
			$(document).unbind(event, finish);
		}
		$(document).bind(event, finish);
	}
	
    function preventDefault(event){
        event.preventDefault();
    }
	
    function scrollBottom(){
        return container.scrollHeight - (container.scrollTop + container.clientHeight);
    }
	
	function noselect(element){
		$(element).css({"-moz-user-select":"none"}).bind("selectstart", preventDefault);
	}
	
	noselect(bar);
	noselect(button);
	noselect(upButton);
	noselect(downButton);
	
	update();
	$(container).scroll(update);
	
	$(container).mousewheel(function(event, delta){
		container.scrollTop = container.scrollTop + (-delta)*LINE_JUMP;
		event.preventDefault();
		
	});
}
