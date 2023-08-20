<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';

/*
echo "<pre>";
print_r($_GET);
echo "</pre>";
exit;
*/

// resize image in temp location then redirect to save prompt

$width = $_GET['width'];
$height = $_GET['height'];
$photo_height = intval($_GET['photo_height']);
$photo_width = intval($_GET['photo_width']);
$mask_x = intval($_GET['mask_x']);
$mask_y = intval($_GET['mask_y']);
$extra = $_GET['extra'];
$type = $_GET['type'];
$filename = $_GET['filename'];

$img = imagecreatefromjpeg(EDITABLE_ROOT.'editor/temp/'.$filename);

list($orig_w, $orig_h, $or_t) = getimagesize(EDITABLE_ROOT.'editor/temp/'.$filename);

$full = imagecreatetruecolor($photo_width, $photo_height);
imagecopyresampled($full, $img, 0, 0, 0, 0, $photo_width, $photo_height, $orig_w, $orig_h);

$final = imagecreatetruecolor($width, $height);
imagecopy($final, $full, 0, 0, $mask_x, $mask_y, $width, $height);

imagejpeg($final, EDITABLE_ROOT.'editor/temp/'.$filename, IMAGE_QUALITY);


switch ($type){
	case 'header':
		header('location:'.WEB_ROOT.'editor/imagery/save_header.php?type='.$type.'&filename='.$filename.'&extra='.$extra);
		exit;
		break;

	case 'replace':
		header('location:'.WEB_ROOT.'editor/imagery/save_replace.php?type='.$type.'&filename='.$filename.'&extra='.$extra);
		exit;
		break;

	case 'upload':
		header('location:'.WEB_ROOT.'editor/imagery/save_upload.php?type='.$type.'&filename='.$filename.'&extra='.$extra);
		exit;
		break;
}

?>