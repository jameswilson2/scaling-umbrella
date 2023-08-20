<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';

// delete image

$folder = $_GET['folder'];
$image = $_GET['image'];
$gallery_path = GALLERY_PATH;
$filename = $gallery_path.$folder.$image;
$thumb = $gallery_path.$folder."thumbs/".$image;
$deleted = $filename.'.deleted';
$th_deleted = $thumb.'.deleted';

// copy file to 'deleted' location
$ok = copy($filename, $deleted);
$ok = copy($thumb, $th_deleted);

// delete current file
// !also delete thumbnail!
@unlink($filename);
@unlink($thumb);


// remove from xml data - load line by line and remove line that matches filename

$location = $gallery_path.$folder;

$xml_handle = fopen($location.'content.xml', "r");

while (!feof($xml_handle)){
	$line = fgets($xml_handle, 1024);
	if (!ereg('<photo src="'.$image.'" caption="(.*)" subcaption="(.*)" />', $line)){
		// add to $xml_data
		$xml_data .= $line;
	}
}
// close xml file
fclose ($xml_handle);

$tempfilename = $location."tempfile.xml";
$filename = $location."content.xml";
$backup = $location."content.xml.BCK";

// save file to temporary file
$tempfile = fopen($tempfilename, 'w');
if (!$tempfile) {
	exit("<p>Unable to open temporary file for writing!</p>");
}
fwrite($tempfile, $xml_data);
fclose($tempfile);

// delete old backup
@unlink ($backup);

// copy old file to backup
$ok = copy($filename, $backup);

// copy temporary file to correct location
$ok = copy($tempfilename, $filename);

// delete temporary file
@unlink($tempfilename);

header('location:'.WEB_ROOT.'editor/imagery/gallery_detail.php?folder='.$folder.'&action=deleted');
?>