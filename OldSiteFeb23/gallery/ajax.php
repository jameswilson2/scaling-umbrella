<?php
require_once 'library/security/_access.inc.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'files/_php_builder.class.php';


$sql = "SELECT gallery_id, gallery_url, gallery_name, gallery_caption
		FROM tbl_gallery
		ORDER BY gallery_priority ASC";



$result = getQuery($sql);

$title = SITE_NAME.' - Gallery';


while ($row = mysql_fetch_array($result)){
	$gallery_id = $row['gallery_id'];
	$gallery_name = htmlspecialchars($row['gallery_name']);
	$gallery_url = htmlspecialchars($row['gallery_url']);
	$gallery_caption = $row['gallery_caption'];
	$ns_gn = strtolower(str_replace(' ', '-', $gallery_name));

	$image = <<<EOD
	
	
	<a href="gallery/item/$gallery_id/$ns_gn"><img src="gallery/images/thumbs/$gallery_url" alt="$gallery_name" width="148" height="87" /></a>
	

EOD;

	$images .= $image;

}

echo $images;

?>