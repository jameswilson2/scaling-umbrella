<?php

require_once 'library/security/_access.inc.php';

$id = safeaddslashes($_GET['catid']);

$sql = "SELECT * FROM tbl_images_categories";

$result = getQuery($sql);

$catagories = array();
while($category = mysql_fetch_array($result)) {
	$catagories[] = array("catoptid"=>$category["image_category_id"], "catoptname"=>$category["image_category_name"]);
}

$sql = "SELECT * FROM tbl_images WHERE image_category = $id ORDER BY image_url";

$result = getQuery($sql);

$images = array();
while($image = mysql_fetch_array($result)) {
	$images[] = array("image_id"=>$image["image_id"], "image_url"=>$image["image_url"]);
}

$data = array("images"=>$images, "categories"=>$catagories);
echo json_encode($data);
?>