if($('body').length > 0) {
	$('body').prepend('<script type="text/javascript" src="http://maps.google.com/maps/api/js?v=3.1&amp;sensor=false&amp;region=GB"></script>');
} else {
	document.write('<script type="text/javascript" src="http://maps.google.com/maps/api/js?v=3.1&amp;sensor=false&amp;region=GB"></script>');
}

(function($) {
$.fn.googleMap = function(options) {
	var defaults = {
		width: 'auto',
		height: 'auto',
		zoom: 'auto',
		poi: [],
		icons: [],
		latCenter: 'auto',
		lngCenter: 'auto',
		streetView: true,
		directions: false,
		mapType: "HYBRID",
		actionSeperator: '-'
	}
	var o = $.extend(defaults, options);
	
	return this.each(function() {
		var markers = [];
		var map;
		var g = $(this);
		var infowindow;
		var activeItem;
		var directionsDisplay;
		
		g.append('<div class="map_canvas"></div>');
		var mc = g.children('.map_canvas');
		var latlngbounds = new google.maps.LatLngBounds();
		
		if(o.poi.length > 0) {
			
			for (var i = 0; i < o.poi.length; i++) {
				latlngbounds.extend(new google.maps.LatLng(o.poi[i].latitude,o.poi[i].longitude));
			}
		}
		if(o.width != 'auto') {
			mc.width(o.width);
		}
		if(o.height == 'auto') {
			if(mc.height() == 0)
				mc.height(mc.width());
		} else {
			mc.height(o.height);
		}

		var center;
		if(o.latCenter == 'auto' && o.lngCenter == 'auto') {
			if(o.poi.length > 0) {
				center = latlngbounds.getCenter();
			} else {
				center = new google.maps.LatLng(54.355356,-2.934837);
			}
		} else {
			center = new google.maps.LatLng(o.latCenter,o.lngCenter);
		}
		var zoom;
		if(o.zoom == 'auto') {
			zoom = 6;
		} else {
			zoom = o.zoom;
		}

		var myOptions = {
		  center: center,
		  zoom:zoom,
		  streetViewControl:o.streetView,
		  scrollwheel:false
		}
		switch(o.mapType.toUpperCase()) {
			case "ROADMAP":
				myOptions.mapTypeId = google.maps.MapTypeId.ROADMAP;
				break;
			case "SATELLITE":
				myOptions.mapTypeId = google.maps.MapTypeId.SATELLITE;
				break;
			case "TERRAIN":
				myOptions.mapTypeId = google.maps.MapTypeId.TERRAIN;
				break;
			case "HYBRID":
			default:
				myOptions.mapTypeId = google.maps.MapTypeId.HYBRID;
				break;
		}
		map = new google.maps.Map(mc[0], myOptions);
		if(o.poi.length > 0) {
			setMarkers();
			if(o.poi.length > 0 && o.zoom == 'auto' && o.latCenter == 'auto' && o.lngCenter == 'auto') {
				map.fitBounds(latlngbounds);
			}
		}
		
		mc.click(function() {
			map.setOptions({
				scrollwheel:true
			});
			mc.unbind('click');
		});
		
		if(o.directions) {
			var detailsHTML = '<div class="directionsDetails"><h3>Directions</h3><label>Your Postcode</label> <input class="postcode" /> ';
			if(o.directionsButton) {
				detailsHTML += '<input type="image" src="'+o.directionsButton+'" class="directionsSearch" value="Get Directions" /></div>';
			} else {
				detailsHTML += '<input type="button" value="Get Directions" class="directionsSearch" /></div>';
			}
			g.append(detailsHTML);
			g.find('.directionsSearch').click(getDirections);
			g.append('<div class="directionResults clear"></div>');
			var directionsOptions = { draggable: true }; 
			directionsDisplay = new google.maps.DirectionsRenderer(directionsOptions); 
			var directionsService = new google.maps.DirectionsService(); 
			directionsDisplay.setMap(map);
			directionsDisplay.setPanel(g.children(".directionResults")[0]);
		}
		
		function setMarkers() { 
			markers = new Array();
			for (var i = 0; i < o.poi.length; i++) { 
				var poi = o.poi[i]; 
				var myLatLng = new google.maps.LatLng(poi.latitude, poi.longitude); 
				var myOptions = {
					position: myLatLng,
					map: map, 
					title: poi.title
				}
				if(typeof poi.iconIndex == "number" || typeof poi.iconIndex == "string") {
					myOptions.shadow = o.icons[poi.iconIndex].shadow;
					myOptions.icon = o.icons[poi.iconIndex].image;
					myOptions.shape = o.icons[poi.iconIndex].shape;
					if(g.find('.iconCheck_'+poi.iconIndex).length < 1) {
						g.append('<div class="iconCheck_'+poi.iconIndex+' iconCheckSection"><input id="type_' + poi.iconIndex + '" checked="checked" type="checkbox" /> <img src="' + o.icons[poi.iconIndex].image.url + '" alt="' + o.icons[poi.iconIndex].title + '" /> <label for="type_' + poi.iconIndex + '"> ' + o.icons[poi.iconIndex].title + '</label></div>');
					}
					g.children('.iconCheckSection').unbind('change').change(filterChange);
				}
				poi.marker = new google.maps.Marker(myOptions); 
				markers.push(poi.marker);
				if(typeof poi.content == "string") {
					google.maps.event.addListener(poi.marker, 'click', function() { popContent(this, o.poi) } );
				}
				if(typeof poi.iconIndex == "number") {
					google.maps.event.addListener(poi.marker, 'mouseover', function() { markerOver(this, o.poi) });
					google.maps.event.addListener(poi.marker, 'mouseout', function() { markerOut(this, o.poi) });
				}
			}
		}
		
		function popContent(thisMarker, objArray) { 
			var thisItem = $.grep(objArray, function(a) { return (a.title) == thisMarker.title})[0];
			if(infowindow) {
				resethglt();
				infowindow.close();
			}
			thisItem.hglt = true;
			var popContent = thisItem.content + "<div><a href='javascript:;' class='zoomLink'><span>Zoom</span></a> ";
			if(o.directions) {
				popContent+= o.actionSeperator+" <a href='javascript:;' class='directionsLink'><span>Get Directions</span></a></div>";
			} else {
				popContent+= "</div>";
			}
			infowindow = new google.maps.InfoWindow({ content:popContent });
			infowindow.open(map,thisMarker);
			
			google.maps.event.addListener(infowindow, 'domready', function() {
				$('.zoomLink').unbind('click').click(function() {
					zoomToLatLng(thisItem.latitude,thisItem.longitude);
				});
				if(o.directions) {
					$('.directionsLink').unbind('click').click(getDirections);
				}
				
				google.maps.event.addListener(infowindow, 'closeclick', function(event, ui) {
					resethglt();
				});
			});
			activeItem = thisItem;
		}
		
		function zoomToLatLng(lat,lng) {
			map.panTo(new google.maps.LatLng(lat,lng));
			map.setZoom(14);
			map.getStreetView().setVisible(false);
		}
		
		function markerOver(thisMarker, objArray) { 
			var thisItem = $.grep(objArray, function(a) { return (a.title) == thisMarker.title})[0];
			thisMarker.setIcon(o.icons[thisItem.iconIndex].over);
		}

		function markerOut(thisMarker, objArray) { 
			var thisItem = $.grep(objArray, function(a) { return (a.title) == thisMarker.title})[0];
			if(thisItem.hglt && thisItem.active)
				thisMarker.setIcon(o.icons[thisItem.iconIndex].over);
			else if(thisItem.active)
				thisMarker.setIcon(o.icons[thisItem.iconIndex].image);
			else 
				thisMarker.setIcon(o.icons[thisItem.iconIndex].out);
		}
		
		function filterChange() {
			$.each(o.poi, function(a,item) { 
				if(g.find('#type_'+item.iconIndex).attr('checked')) {
					if(!item.active) {
						if(item.hglt)
							item.marker.setIcon(o.icons[item.iconIndex].over);
						else
							item.marker.setIcon(o.icons[item.iconIndex].image);
					}
					if(!item.marker.getVisible())
						item.marker.setVisible(true);
					item.active = true;
				} else {
					item.marker.setVisible(false);
				}
			});
		}
		
		function streetViewLatLng(lat,lng) {
			var panoramaOptions = { 
			  position: new google.maps.LatLng(lat,lng), 
			  pov: { 
				heading: 34, 
				pitch: 10, 
				zoom: 1 
			  } 
			};
			var panorama = new  google.maps.StreetViewPanorama(document.getElementById("pano"), panoramaOptions); 
			map.setStreetView(panorama);
		}
		
		function resethglt() {
			if(activeItem) {
				var thisItem = activeItem;
				if(thisItem.active)
					thisItem.marker.setIcon(o.icons[thisItem.iconIndex].image);
				else 
					thisItem.marker.setIcon(o.icons[thisItem.iconIndex].out);
				thisItem.hglt = false;
				activeItem = false;
			}
		}
		
		function getDirections() {
			var pc = g.find('.postcode');
			var start = pc.val();
			if(start == "" || !start) {
				alert("Please enter the postcode/area you are starting from");
				pc.focus();
			} else {
				if(!activeItem && o.poi.length == 1) {
					activeItem = o.poi[0];
				}
				if(activeItem) {
					var request = { 
						origin:start,
						destination:activeItem.marker.getPosition(),
						travelMode: google.maps.DirectionsTravelMode.DRIVING
					}; 
					directionsService.route(request, function(result, status) { 
						if (status == google.maps.DirectionsStatus.OK) { 
							directionsDisplay.setDirections(result); 
							location.hash = "map_canvas";
							infowindow.close();
							$('.directionResults').removeClass('clear');//.append('<div class="printDirect"><img src="interface/btn_printdirections.gif" alt="Print" class="printbtn" onclick="printDirections()" /></div>');
						} else {
							alert("Please check you have entered the correct postcode");
						}
					}); 
				} else {
					alert("Please select a place to go to");
				}
			}
		}
	});
};
}) ($)