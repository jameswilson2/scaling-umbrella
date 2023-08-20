<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'gallery/_gallery_config.inc.php';
require_once 'gallery/_gallery_images.inc.php';

$gallery_id = $_GET['gallery_id'];

$sql = "SELECT gallery_url FROM tbl_gallery WHERE gallery_id='$gallery_id'";
$result = getQuery($sql);

$row = mysql_fetch_array($result);

$gallery_url = $row['gallery_url'];


deleteImage($gallery_url);

$sql = "DELETE FROM tbl_gallery WHERE gallery_id='$gallery_id'";
$result = getQuery($sql);

header('location:'.WEB_ROOT.'editor/gallery/');
exit;


?>