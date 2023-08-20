<?php
require_once 'library/security/_access.inc.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'files/_php_builder.class.php';


$sql = "SELECT * FROM tbl_maparea";

$result = getQuery($sql);

$first = true;
while ($row = mysql_fetch_array($result)){
	$area_coords = $row["area_coords"];
	$area_name = $row["area_name"];
	$area_expanded = $row["area_expanded"];
	
	$area = '';
	$expandedarea = '';
	if($first) {
		$first = false;
	} else {
		$areas .= ',';
	}
	
	$area .= $area_coords;
	$expandedarea .= $area_expanded;

	$areas .= <<<EOD
	{
		area:$area,
		expanded:$expandedarea
}
EOD;
}

echo <<<EOD
var areas = [
	$areas
]
EOD;

?>