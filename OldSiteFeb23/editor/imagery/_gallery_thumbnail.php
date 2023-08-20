<?php
// Include the Thumbnail class
require_once 'imagery/_thumbnail.php';
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';

$imageFile = GALLERY_PATH.$_GET['folder'].$_GET['image'];

// Instantiate the thumbnail
$tn=new Thumbnail(GALLERY_THUMB_WIDTH, GALLERY_THUMB_HEIGHT);

// Load the image from a file
$tn->loadFile($imageFile);

// Send the HTTP Content-Type header
header("HTTP/1.1 200 OK");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-Type: ".$tn->getMime());


// Display the thumbnail
$tn->buildThumb();
?>