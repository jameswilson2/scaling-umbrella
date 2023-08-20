var imported = document.createElement('script');
imported.src = 'https://npmcdn.com/leaflet@1.3.4/dist/leaflet.js';
document.head.appendChild(imported);


var link = document.createElement('link');  
link.rel = 'stylesheet';  
link.type = 'text/css'; 
link.href = 'https://npmcdn.com/leaflet@1.3.4/dist/leaflet.css';  
document.getElementsByTagName('HEAD')[0].appendChild(link);

if(window.attachEvent) {
    window.attachEvent('onload', loadMap);
} else {
    if(window.onload) {
        var curronload = window.onload;
        var newonload = function(evt) {
            curronload(evt);
            loadMap(evt);
        };
        window.onload = newonload;
    } else {
        window.onload = loadMap;
    }
}

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

function loadMap() {
var startPoint = [54.2892153,-2.7450094];
var map = L.map('map', {editable: true}).setView(startPoint, 10),
    tilelayer = L.tileLayer('http://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {maxZoom: 25, attribution: 'Data \u00a9 <a href="http://www.openstreetmap.org/copyright"> OpenStreetMap Contributors </a> Tiles \u00a9 HOT'}).addTo(map);
    L.EditControl = L.Control.extend({
        options: {
            position: 'topleft',
            callback: null,
            kind: '',
            html: ''
        },
        onAdd: function (map) {
            var container = L.DomUtil.create('div', 'leaflet-control leaflet-bar'),
                link = L.DomUtil.create('a', '', container);
            link.href = '#';
            link.title = 'Create a new ' + this.options.kind;
            link.innerHTML = this.options.html;
            L.DomEvent.on(link, 'click', L.DomEvent.stop)
                      .on(link, 'click', function () {
                        window.LAYER = this.options.callback.call(map.editTools);
                      }, this);
            return container;
        }
    });
    var poly = L.polygon([
      [
	    [
			[54.11215,-3.130524],
			[54.152784,-3.147004],
			[54.202617,-3.144257],
			[54.254796,-3.139944],
			[54.343649,-3.111298],
			[54.376858,-3.078188],
			[54.377657,-3.046517],
			[54.347602,-3.060250],
			[54.285470,-3.094539],
			[54.254394,-3.085613],
			[54.268181,-2.976307],
			[54.248277,-2.931675],
			[54.320929,-2.890885],
			[54.33174,-2.934144],
			[54.350954,-2.968476],
			[54.373359,-2.968476],
			[54.394152,-2.966416],
			[54.418131,-2.988389],
			[54.426121,-2.957489],
			[54.404544,-2.911484],
			[54.373359,-2.805054],
			[54.391353,-2.683464],
			[54.355356,-2.671791],
			[54.327937,-2.629219],
			[54.271740,-2.640409],
			[54.106515,-2.801170],
			[54.160022,-2.888889],
			[54.193177,-2.834183],
			[54.149567,-2.930775]
        ],
		[
			[54.325685744798726,-2.52493629057426],
			[54.306129814628264,-2.4714200024027377],
			[54.293638393828175,-2.39932308846619],
			[54.29906014884144,-2.3697355308104306],
			[54.31055048524376,-2.340450083138421],
			[54.320327557286845,-2.315711115952581],
			[54.33210322649086,-2.308241258142516],
			[54.34581823815681,-2.3221775761339813],
			[54.37550630844876,-2.336767923552543],
			[54.39576381979681,-2.3261086153797805],
			[54.470037612805754,-2.348499298095703],
			[54.45272605356699,-2.35231876373291],
			[54.42919954046966,-2.3421692533884197],
			[54.39247122105509,-2.3493412655079737],
			[54.36235881641611,-2.3535796167561784],
			[54.32698212928454,-2.336007496342063],
			[54.31290659277615,-2.3619090544525534],
			[54.312671010937144,-2.3733713175170124],
			[54.314083981331414,-2.389323797542602],
			[54.323048038428574,-2.4348651370382868]
		]
	]	  
    ]).addTo(map);
	
	var postcode = getParameterByName('postcode');
	
	if (postcode) {
	var postcode = postcode.replace(/ /g, '+');
	var urlcall = 'https://nominatim.openstreetmap.org/?format=json&postalcode=' + postcode + '&format=json&limit=1'
	
    function getJSON(url) {
        var resp ;
        var xmlHttp ;

        resp  = '' ;
        xmlHttp = new XMLHttpRequest();

        if(xmlHttp != null)
        {
            xmlHttp.open( "GET", url, false );
            xmlHttp.send( null );
            resp = xmlHttp.responseText;
        }

        return resp ;
    }
	
	var gjson ;
	gjson = getJSON(urlcall) ;
	
	var marker = eval('(' + gjson + ')');
	var lat = marker[0].lat
	var lon = marker[0].lon
	
	var markerarr = [lat, lon]
	
	var rec = L.marker(
        markerarr
    ).addTo(map);
	}
}	