<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';
/*
some basic checking
*/
if (!isset($_GET['image'])) exit;
$imageFile = EDITABLE_ROOT.'editor/temp/'.$_GET['image'];
if (!file_exists($imageFile)) exit;
$Size = filesize($imageFile);
/*
send out all headers
*/
header("HTTP/1.1 200 OK");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-Type: image/jpg");
header("Content-Length: $Size");
/*
and finally the image itself
*/
readfile($imageFile);
?>