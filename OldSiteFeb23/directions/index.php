<?php
require_once 'library/security/_access.inc.php';
require_once 'library/captcha.class.php';
require_once 'files/_php_builder.class.php';
require_once 'points-of-interest/_poi.config.php';

$icons = array();
$sql = "SELECT * FROM tbl_poi_icons";
$result = getQuery($sql);
while($row = mysql_fetch_array($result)){
	$icons[] = $row;
}

$poi = array();
$sql = "SELECT * FROM tbl_poi";
$result = getQuery($sql);
while($row = mysql_fetch_array($result)){
	$poi[] = $row;
}

ob_start();
?>
<h1>Points of Interest</h1>
<div id="googleMap">&nbsp;</div>
<script type="text/javascript">
<!--
/* Google Mapping */
var mapopts;
$(function() {
	var mapimagessrc = new Array(
		<?php
			$sep = '';
			foreach($icons as $i) {
				$image = IMAGE_WEB_PATH.$i["icon_image"];
				$imageover = IMAGE_WEB_PATH.$i["icon_over_image"];
				echo <<<EOD
					$sep"$image","$imageover"
EOD;
				$sep = ',';
			}
		?>
	);
	mapimages = new Array();
	$.each(mapimagessrc,function(i, a) {
		var thisimg = new Image();
		thisimg.src = a;
		mapimages.push(thisimg);
	});
	mapopts = {
		poi: [ 
		<?php
			$sep = '';
			foreach($poi as $p) {
				$lat = $p["poi_latitude"];
				$lng = $p["poi_longitude"];
				$title = str_replace("'","\'",$p["poi_title"]);
				$content = preg_replace('/[\r\n]+/','',$p["poi_content"]);
				$content = str_replace('"','\\"',$content);
				$icon_id = $p["poi_icon"];
				echo <<<EOD
					$sep{
			latitude:$lat,
			longitude:$lng,
			title:'$title',
			content:"$content",
			iconIndex:'p$icon_id'
		}
EOD;
				$sep = ',';
			}
		?>
		],
		icons: {
				<?php
			$sep = '';
			foreach($icons as $i) {
				$id = $i["icon_id"];
				$title = $i["icon_title"];
				$image = IMAGE_WEB_PATH.$i["icon_image"];
				$imageover = IMAGE_WEB_PATH.$i["icon_over_image"];
				$hotspot = $i["icon_hotspot"];
				$width = $i["icon_width"];
				$height = $i["icon_height"];
				$hwidth = $width/2;
				echo <<<EOD
					$sep"p$id":{
				title:"$title",
				'image':new google.maps.MarkerImage('$image', new google.maps.Size($width, $height), new google.maps.Point(0,0), new google.maps.Point($hwidth, $height)),
				'shadow':new google.maps.MarkerImage('directions/trans.gif', new google.maps.Size($width, $height), new google.maps.Point(0,0), new google.maps.Point($hwidth, $height)),
				'over':new google.maps.MarkerImage('$imageover', new google.maps.Size($width, $height), new google.maps.Point(0,0), new google.maps.Point($hwidth, $height)),
				'out':new google.maps.MarkerImage('$image', new google.maps.Size($width, $height), new google.maps.Point(0,0), new google.maps.Point($hwidth, $height)),
				'shape':{ coord: [$hotspot], type: 'poly' }
			}
EOD;
				$sep = ',';
			}
		?>
		},
		streetView:false,
		directions:true,
		directionsButton:'directions/btn_directions.gif'
	};
	$('#googleMap').googleMap(mapopts);
});
//-->
</script>

<?php

$title = "Directions - ".SITE_NAME;
$description = "Directions - ".SITE_NAME;
$keywords = "Directions - ".SITE_NAME;

$content = ob_get_contents();
ob_end_clean();

$page = new PHPBuilder();

$page->setTitle($title);
$page->setDescription($description);
$page->setKeywords($keywords);
$page->setContent($content);

$page_content = $page->buildPage();

echo $page_content;

?>