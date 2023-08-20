
	document.write('<script src="behaviour/jquery.googleMap.js" type="text/javascript"></script>');
	

	/* Google Mapping */
	$(function() {
		var mapimagessrc = new Array("presentation/marker.png");
		mapimages = new Array();
		$.each(mapimagessrc,function(i, a) {
			var thisimg = new Image()
			thisimg.src = a;
			mapimages.push(thisimg);
		});
		$('#googleMap').googleMap( {
			zoom:9,
			height:365,
			poi: [ {
				latitude:54.329153,
				longitude:-2.742677,
				title:'Kencomp',
				iconIndex:0
			}],
			icons: [
				{
					title:"Kencomp",
					'image':new google.maps.MarkerImage('presentation/marker.png', new google.maps.Size(42, 55), new google.maps.Point(0,0), new google.maps.Point(21, 55)),
					'shadow':new google.maps.MarkerImage('presentation/marker_shadow.png', new google.maps.Size(41, 28), new google.maps.Point(0,0), new google.maps.Point(41, 28)),
					'over':new google.maps.MarkerImage('presentation/marker.png', new google.maps.Size(42, 55), new google.maps.Point(0,0), new google.maps.Point(21, 55)),
					'out':new google.maps.MarkerImage('presentation/marker.png', new google.maps.Size(42, 55), new google.maps.Point(0,0), new google.maps.Point(21, 55)),
					'shape':{ coord: [20,55,7,37,0,23,0,16,5,6,16,0,26,0,36,5,42,16,42,23,34,38], type: 'poly' }
				}
			],
			streetView:false,
			mapType: "ROADMAP",
			directions:true,
			directionsselector:'#dirctions',
			directionsresults:'#dirctionsresults'
		});
	});