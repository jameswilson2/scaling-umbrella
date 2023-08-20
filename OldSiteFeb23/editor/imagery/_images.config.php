<?php

define('IMAGE_FOLDER', 'images/');
define('GALLERY_FOLDER', 'flash/gallery/');
define('HEADER_FOLDER', 'flash/header/');

define('IMAGE_PATH', EDITABLE_ROOT.IMAGE_FOLDER);
define('IMAGE_WEB_PATH', WEB_ROOT.IMAGE_FOLDER);

define('GALLERY_PATH', EDITABLE_ROOT.GALLERY_FOLDER);
define('GALLERY_WEB_PATH', WEB_ROOT.GALLERY_FOLDER);

define('HEADER_PATH', EDITABLE_ROOT.HEADER_FOLDER);
define('HEADER_WEB_PATH', WEB_ROOT.HEADER_FOLDER);

define('IMAGE_QUALITY', '90');

define('RAD_LOCATION', 'http://www.sck-editor.co.uk/RADuploader/uploader.php');
define('PROCESS_RAD', WEB_ROOT.'editor/imagery/rad_process.php');
define('REDIRECT_RAD', WEB_ROOT.'editor/imagery/resizer.php');

define('IMAGES_THUMBNAIL_WIDTH', 100);
define('IMAGES_THUMBNAIL_HEIGHT', 120);

$disallowed_images = array('floorplan.jpg');

$image_dimensions = array(
	array('Content Photo Horizontal', '291', '56'),
	array('Content Photo Vertical', '194', '291'),
	array('Content Photo Square', '291', '291')
);


$IMAGES_ROOT_DIR_PATH = EDITABLE_ROOT . "images";
$IMAGES_URI_ROOT_PATH = WEB_ROOT . "editor/images/";

$IMAGES_GLOB_PATTERN = "{*.gif,*.jpg,*.png}";

$IMAGES_THUMBNAIL_OUTPUT_EXT = "jpg";
$IMAGES_THUMBNAIL_WIDTH = 128;
$IMAGES_THUMBNAIL_HEIGHT = 128;

$IMAGES_TEMP_DIR = EDITABLE_ROOT . "editor/temp";

function images_file_path($path){
    global $IMAGES_ROOT_DIR_PATH;
    $path = trim($path, "/\\");
    return "$IMAGES_ROOT_DIR_PATH/$path";
}

?>