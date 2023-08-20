$(document).ready(function(){
	if($.browser.msie && $.browser.version == "6.0"){
		$("ul li:first-child").addClass("first-child");
	}
});

function inherits(subclass, superclass){
	function inheritance(){}
	inheritance.prototype = superclass.prototype;
	subclass.prototype = new inheritance();
	subclass.prototype.constructor = subclass;
}

function EventEmitter(){
	this._events = {};
}

EventEmitter.prototype.on = function(eventName, eventHandler){
	var handlers = this._events[eventName];
	if(!handlers){
		handlers = [];
		this._events[eventName] = handlers;
	}
	handlers.push(eventHandler);
}

EventEmitter.prototype.emit = function(eventName){
	var handlers = this._events[eventName];
	if(!handlers) return;
	var arguments = Array.prototype.slice.call(arguments, 1);
	for(var i = 0; i < handlers.length; i++){
		handlers[i].apply(this, arguments);
	}
}

function Timer(delay, func){
	this._running = false;
	this._intervalID = null;
	this._delay = delay;
	this._callback = func;
}

Timer.prototype.start = function(){
	if(this._running) return;
	this._intervalID = window.setInterval(this._callback, this._delay);
	this._running = true;
}

Timer.prototype.stop = function(){
	if(!this._running) return;
	window.clearInterval(this._intervalID);
	this._running = false;
}

Timer.prototype.restart = function(){
	if(!this._running) return;
	this.stop();
	this.start();
}

function initNonEmptyTextHandlers(){
	$(".non-empty").each(function(){
		this.defaultValue = this.value;
		$(this).addClass("non-empty-empty");
		$(this).focus(function(){
			if(this.value == this.defaultValue){
				this.value = "";
				$(this).removeClass("non-empty-empty");
			}
		});
		$(this).blur(function(){
			if(!this.value.length){
				$(this).addClass("non-empty-empty");
				this.value = this.defaultValue;
			}
		});
	});
}

function noselect(element){
	$(element).css({"-moz-user-select":"none"}).bind("selectstart", function(e){e.preventDefault();});
}

function createLoadingElement(message, loadingIconSrc){

	var div = document.createElement("div");
	div.style.textAlign = "center";
	div.style.fontSize = "x-small";
	
	var loading = new Image();
	loading.src = loadingIconSrc || "presentation/loading.gif";
	loading.style.border = "0";
	
	div.appendChild(loading);
	div.appendChild(document.createElement("br"));
	div.appendChild(document.createTextNode(message));
	
	return div;
}
