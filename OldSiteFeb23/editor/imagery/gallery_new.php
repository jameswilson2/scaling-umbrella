<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';

$newgallery = $_POST['newGallery'];

// sort foldername out - swap spaces for _

// check folder doesn;t exist already

// create folder

// create thumbs folder

// copy files across - all files in _template/thumbs and _template folder

// redirect

$newgallery = ereg_replace('[^0-9a-zA-Z-]', '_', $newgallery);

$location = GALLERY_PATH;

// open current directory
$dp = opendir($location);
$dir_list = array();
// loop through the directory
while (false !== ($directory = readdir($dp))) {
	if (is_dir($location . $directory)) {
		// $directory is a directory...
		$dir_list[] = $directory;
	}
}
// Close top level directory
closedir($dp);

// check new directory against current list

foreach ($dir_list as $dir){
	if (strtolower($dir)==strtolower($newgallery)){
		// folder already exists
		header('location:'.WEB_ROOT.'editor/imagery/gallery.php?action=newgalleryexists');
		exit();
	}
}

$ok = @mkdir($location.$newgallery."/");

if (!$ok){
	header('location:'.WEB_ROOT.'editor/imagery/gallery.php?action=newgalleryfail');
	exit();
}

mkdir($location.$newgallery."/thumbs/");

// open current directory
$dp = opendir($location."_template/");

// loop through the directory
$file_list = Array();
while (false !== ($entry = readdir($dp))) {
	$file_list[] = $entry;
}

// Close top level directory
closedir($dp);



foreach($file_list as $filename){
	$ok = copy($location."_template/".$filename, $location.$newgallery."/".$filename);
}



// open current directory
$dp = opendir($location."_template/thumbs/");

// loop through the directory
$thumb_list = Array();
while (false !== ($entry = readdir($dp))) {
	$thumb_list[] = $entry;
}

// Close top level directory
closedir($dp);

foreach($thumb_list as $filename){
	$ok = copy($location."_template/thumbs/".$filename, $location.$newgallery."/thumbs/".$filename);
}





header('location:'.WEB_ROOT.'editor/imagery/gallery.php?action=newgallerysuccess');
exit();

?>