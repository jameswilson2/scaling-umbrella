function Slideshow(){
	EventEmitter.call(this);
	this._slides = [];
	this._currentSlide = null;
	this._selectPending = null;
	var self = this;
	this._timer = new Timer(5000, function(){
		self.select((self._currentSlide + 1) % self._slides.length);
	});
}

inherits(Slideshow, EventEmitter);

Slideshow.prototype.add = function(slide){
	this._slides.push(slide);
	var index = this._slides.length - 1;
	this.emit("add", index);
	if(index == this._selectPending){
		var selectPending = this._selectPending;
		this._selectPending = null;
		this.select(selectPending);
	}
}

Slideshow.prototype.get = function(index){
	return this._slides[index];
}

Slideshow.prototype.count = function(){
	return this._slides.length;
}

Slideshow.prototype.play = function(){
	this._timer.start();
	this.emit("play");
}

Slideshow.prototype.pause = function(){
	this._timer.stop();
	this.emit("pause");
}

Slideshow.prototype.restartDelay = function(){
	this._timer.restart();
}

Slideshow.prototype.select = function(index){
	this._timer.restart();
	if(index === undefined || !this._slides[index]){
		this._selectPending = index;
		return;
	}
	this._currentSlide = index;
	this.emit("change", this._currentSlide);
}

Slideshow.prototype.prev = function(){
	this.select((this._currentSlide - 1 < 0 ? this._slides.length - 1: this._currentSlide - 1));
}

Slideshow.prototype.next = function(){
	this.select((this._currentSlide + 1) % this._slides.length);
}

function loadSlideshowGalleryXml(slideshow, url, callback){
	callback = callback || $.noop;
	var first = true;
	$.get(url, function(data){
		var dir = url.match(/(.*)\/.*$/)[1];
		
		var images = [];
		var loaded = 0;
		
		function loadSlides(){
			// Load the images into the slideshow in sequence
			for(; loaded < images.length && images[loaded].waiting; loaded++){
				if(images[loaded].failedToLoad) continue;
				var image = images[loaded];
				var href = $(image).data("href");
				var slideElement = image;
				if(href){
					slideElement = document.createElement("a");
					slideElement.href = href;
					slideElement.appendChild(image);
				}
				slideshow.add(slideElement);
				if(first){
					first = false;
					callback();
				}
			}
		}
		
		function onImageLoad(){
			this.waiting = true;
			if(this.sequence == loaded) loadSlides();
		}
		
		function onImageError(){
			this.waiting = true;
			this.failedToLoad = true;
			if(this.sequence == loaded) loadSlides();
		}
		
		$("document > gallery > photo", data).each(function(){
			var image = new Image();
			images.push(image);
			image.sequence = images.length - 1;
			$(image).data("href", this.getAttribute("href"));
			$(image).data("caption", this.getAttribute("caption"));
			$(image).load(onImageLoad);
			$(image).error(onImageError);
			image.src = dir + "/" + this.getAttribute("src");
		});
	});
}
