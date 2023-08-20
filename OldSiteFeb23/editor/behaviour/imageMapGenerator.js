function setupImageMapGenerator(canvassel, imgsel, inputsel, hotmode, buttonsel, scaleIt) {

	var canvas
	var shapes = [];
	var mode = "";
	var currentPoly;
	var input;
	var lastPoints;
	var lastPos;
	var scaleUp = scaleIt;
	
	$(canvassel).html('<canvas id="canvas" width="0" height="0"></canvas>').attr('unselectable', 'on').css('user-select', 'none').attr('onselectstart', false).parent().attr('unselectable', 'on').css('user-select', 'none').attr('onselectstart', false).children('img').attr('unselectable', 'on').css('user-select', 'none').attr('onselectstart', false);
	
	$(canvassel).children('canvas').attr('width',$(imgsel).width()).attr('height',$(imgsel).height());
	global = this;
	canvas = global.canvas = new fabric.Canvas('canvas', { selection:false });
	input = $(inputsel);
	mode = hotmode;
	buttonSec = $(buttonsel);
	
	if(hotmode == "") {
		buttonSec.append('<a class="circle">Add Circle</a> ');
		$('.circle').click(function() {
			mode = "circle";
			deselect();
		});
		buttonSec.append('<a class="square">Add Square</a> ');
		$('.square').click(function() {
			mode = "square";
			deselect();
		});
		buttonSec.append('<a class="polyugon">Add Polygon</a> ');
		$('.polygon').click(function() {
			mode = "polygon";
			deselect();
		});	
	}
	if(buttonSec.children('.finishpolygon').length == 0) {
		buttonSec.append('<a class="finishpolygon">Finish Polygon</a> ');
	}
	$('.finishpolygon').unbind('click').click(function() {
		mode = "polygon";
		currentPoly.selectable = true;
		activeItem = currentPoly;
		currentPoly = null;
		canvas.setActiveObject(activeItem);
		$(this).hide();
		setVars()
		mode = "";
	});	
	if(buttonSec.children('.delete').length == 0) {
		buttonSec.append('<a class="delete">Delete Item</a> ');
	}
	$('.delete').click(function() {
		var myObj = canvas.getActiveObject();
		shapes = _.without(shapes,myObj);
		clearEditControls();
		canvas.remove(myObj);
		mode = "polygon";
		deselect();
	});
	
	if(input.val() != "") {
		myvals = input.val();
		splitval = myvals.split(',');
		var points = [];
		var inc = 0;
		var minX = minY = 10000
		var maxX = maxY = 0;
		_.each(splitval,function(a,i) {
			a = splitval[i] = a*scaleUp;
			if(i % 2 == 0) { 
				minX = Math.min(minX,a);
				maxX = Math.max(maxX,a);
			} else {
				minY = Math.min(minY,a);
				maxY = Math.max(maxY,a);
				inc+= 1;
			}
		});
		var centerX = (minX + (maxX - minX)/2);
		var centerY = (minY + (maxY - minY)/2);
		inc = 0;
		_.each(splitval,function(a,i) {
			if(i % 2 == 0) { 
				points[i-inc] = { x:parseFloat(a)-centerX }
			} else {
				points[i-1-inc].y = parseFloat(a)-centerY;
				inc+= 1;
			}
		});
		var opts = {
			type:hotmode,
			fill:"#8f5ded",
			left:centerX,
			top:centerY,
			width:maxX - minX,
			height:maxY - minY
		}
		
		var shape;
		switch(hotmode) {
			case "rect":
				shape = new fabric.Rect(opts);
				shape.lockUniScaling = false;
				break;
			case "circle":
				opts.radius=input.attr('data-radius'),
				shape = new fabric.Circle(opts);
				shape.lockUniScaling = true;
				break;
			case "polygon":
				shape = new fabric.Polygon(points,opts)
				break;
		}
		shape.lockRotation = true;
		canvas.add(shape);
		shapes.push(shape);
		mode="";
		$('.finishpolygon').hide();
	} else {
		mode = hotmode;
		deselect();
	}	
	
	function add(left, top) {
		if(mode.length > 0) {
			var obj = { 
			  left: left, 
			  top: top,
			  fill: '#' + getRandomColor(), 
			  opacity: 0.7
			};
			
			var shape;
			switch(mode) {
				case "square":
					obj.width=50;
					obj.height=50;
					shape = new fabric.Rect(obj);
					
					shape.lockUniScaling = false;
					break;
				case "circle":
					obj.radius=50,
					shape = new fabric.Circle(obj);
					shape.lockUniScaling = true;
					break;
				case "polygon":
					$('.finishpolygon').show();
					obj.selectable = false;
					if(!currentPoly) {
						shape = new fabric.Polygon([{ x: 0, y: 0 }],obj);
						lastPoints = [{ x: 0, y: 0 }];
						lastPos = {left: left, top: top};
					} else {
						obj.left = lastPos.left;
						obj.top = lastPos.top;
						obj.fill = currentPoly.fill;
						currentPoly.points.push({ x: left - lastPos.left, y: top-lastPos.top });
						shapes = _.without(shapes,currentPoly);
						
						lastPoints.push({ x: left-lastPos.left, y: top-lastPos.top })
						quickshape = new fabric.Polygon(lastPoints,obj);
						minX = _.min(lastPoints, function(a) { return a.x }).x;
						minY = _.min(lastPoints, function(a) { return a.y }).y;
						var newpoints = [];
						_.each(lastPoints, function(a) { 
							var newPoint = {};
							newPoint.x = a.x - (quickshape.width/2) - minX;
							newPoint.y = a.y - (quickshape.height/2) - minY;
							newpoints.push(newPoint);
						});
						obj.left += quickshape.width/2 + minX;
						obj.top += quickshape.height/2 + minY;
						shape = new fabric.Polygon(newpoints,obj);
						canvas.remove(currentPoly);
					}
					currentPoly = shape;
					break;
			}
			shape.lockRotation = true;
			shape.link = $('#hrefBox').val();
			shape.title = $('#titleBox').val();
			canvas.add(shape);
			shapes.push(shape);
			if(mode!="polygon") {
				mode="";
			}
		} else {
			deselect();
		}
	}
	
	var activeItem;
	var activeEditCircles;
	
	canvas.observe('mouse:down', function(e) {
		if(!e.target) {
			add(e.e.layerX,e.e.layerY);
		} else {
			if(_.detect(shapes, function(a) { return _.isEqual(a,e.target) })) {
				if(!_.isEqual(activeItem, e.target)) {
					clearEditControls();
				}
				activeItem = e.target;
				if(activeItem.type=="polygon") {
					addEditCircles();
				}
				$('#hrefBox').val(activeItem.link);
				$('#titleBox').val(activeItem.title);
			}
		}
	});
	
	canvas.observe('object:moving', function(e) {
		readjustControls(e);
	});
	
	canvas.observe('mouse:up', function(e) {
		if(!_.isUndefined(activeItem) && !_.isNull(activeItem)) {
			if(activeItem.type=="polygon") {
				if(activeEditCircles.length == 0) {
					addEditCircles(e);
				}
			}
		}
	});
	canvas.observe('object:modified', function(e) {
		if(activeItem.type=="polygon") {
			clearEditControls(e);
		}
	});
	
	function deselect() {
		if(!_.isUndefined(activeItem) && !_.isNull(activeItem)) {
			activeItem.setActive(false);
			activeItem = null;
		}
		clearEditControls();
	}
	
	function readjustControls(e) {
		if(typeof e.target == "object") {
			tgt = e.target;
			if(_.detect(activeEditCircles, function(a) { return _.isEqual(a,tgt) })) {
				activeItem.points[tgt.pointIndex].x = (tgt.left - activeItem.left) / activeItem.scaleX;
				activeItem.points[tgt.pointIndex].y = (tgt.top - activeItem.top) / activeItem.scaleY;
			} else {
				if(activeItem.type=="polygon") {
					_.each(activeEditCircles,function(p) {
						p.left = activeItem.left + (activeItem.points[p.pointIndex].x * activeItem.scaleX);
						p.top = activeItem.top + (activeItem.points[p.pointIndex].y * activeItem.scaleY);
					});
				}
			}
			setVars();
		}
	}
	
	function clearEditControls() {
		_.each(activeEditCircles,function (item) { canvas.remove(item); });
		activeEditCircles = [];
	}
	
	function addEditCircles() {
		_.each(activeItem.points,function(p, i) {
			var holdershape = new fabric.Circle({
				left: activeItem.left + (p.x * activeItem.scaleX),
				top: activeItem.top + (p.y * activeItem.scaleY),
				strokeWidth: 3,
				radius: 10,
				fill: '#fff',
				stroke: '#666'
			});
			holdershape.hasControls = holdershape.hasBorders = false;
			holdershape.pointIndex = i;
			activeEditCircles.push(holdershape);
			canvas.add(holdershape);
		});
	}
	
	function getRandomColor() {
		return (
			pad(getRandomInt(0, 255).toString(16), 2) + 
			pad(getRandomInt(0, 255).toString(16), 2) + 
			pad(getRandomInt(0, 255).toString(16), 2)
		);
	}
	  
	function pad(str, length) {
		while (str.length < length) {
			str = '0' + str;
		}
		return str;
	};

	var getRandomInt = fabric.util.getRandomInt;
	
	var areas;
	function setVars() {
		var areas = [];
		_.each(shapes,function(a) {
			var area = {};
			area.link = a.link;
			area.title = a.title;
			switch(a.type) {
				case "circle":
					area.shape = a.type;
					area.coords = [a.left,a.top,a.radius / scaleUp];
					break;
				case "rect":
					area.shape = a.type;
					var thisWidth = a.width / scaleUp;
					var thisHeight = a.height / scaleUp;
					area.coords = [a.left-(thisWidth/2),a.top-(thisHeight/2),a.left+(thisWidth/2),a.top+(thisHeight/2)];
					break;
				case "polygon":
					area.shape = "poly";
					var coords = [];
					_.each(a.points, function(p) { 
						newX = (p.x + a.left) / scaleUp;
						newY = (p.y + a.top) / scaleUp;
						coords.push(newX);
						coords.push(newY);
					});
					area.coords = coords;
					break;
			}
			input.val(area.coords);
		});
		//$('#codeSection').html(_.template($('#map_template').html(), { areas:areas }));
		return false;
	};
	
	$('#hrefBox').blur(function() {
		if(activeItem)
			activeItem.link = $(this).val();
	});
	$('#titleBox').blur(function() {
		if(activeItem)
			activeItem.title = $(this).val();
	});
	
}