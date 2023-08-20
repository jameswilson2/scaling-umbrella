
	document.write('<script src="directions/jquery.googleMap.js" type="text/javascript"></script>');
	

	/* Google Mapping */
	$(function() {
		var mapimagessrc = new Array("directions/pointer.png",'directions/pointer_over.png');
		mapimages = new Array();
		$.each(mapimagessrc,function(i, a) {
			var thisimg = new Image();
			thisimg.src = a;
			mapimages.push(thisimg);
		});
$('#googleMap').googleMap( {
			zoom:9,
			width:500,
			poi: [ {
				latitude:54.329444,
				longitude:-2.746297,
				title:'We are here',
				content:"<h2>We are here</h2><p>Address</p>",
				iconIndex:0
			}],
			icons: [
				{
					title:"We are here",
					'image':new google.maps.MarkerImage('directions/pointer.png', new google.maps.Size(32, 37), new google.maps.Point(0,0), new google.maps.Point(16, 34)),
					'shadow':new google.maps.MarkerImage('directions/trans.gif', new google.maps.Size(32, 37), new google.maps.Point(0,0), new google.maps.Point(16, 34)),
					'over':new google.maps.MarkerImage('directions/pointer_over.png', new google.maps.Size(32, 37), new google.maps.Point(0,0), new google.maps.Point(16, 34)),
					'out':new google.maps.MarkerImage('directions/pointer.png', new google.maps.Size(32, 37), new google.maps.Point(0,0), new google.maps.Point(16, 34)),
					'shape':{ coord: [16,37,0,19,0,11,11,0,22,0,32,9,32,19], type: 'poly' }
				}
			],
			streetView:false,
			directions:true,
			directionsButton:'directions/btn_directions.gif'
		});
	});