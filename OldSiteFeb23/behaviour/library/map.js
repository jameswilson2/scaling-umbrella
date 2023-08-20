//<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>

function initLocationMap(element, position, options){

	position = new google.maps.LatLng(position[0], position[1]);
	
	options = options || {};
	var mapOptions = {
		zoom: options.zoom || 14,
		center: position,
		mapTypeId: options.mapType || google.maps.MapTypeId.ROADMAP
	}
	
	var map = new google.maps.Map(element, mapOptions);
	
	new google.maps.Marker({
		position: position,
		map: map
	});
}
