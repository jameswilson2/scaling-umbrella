<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';

// rename image caption

$caption = $_POST['caption'];
$subcaption = $_POST['subcaption'];
$folder = $_GET['folder'];
$image = $_GET['image'];
$gallery_path = GALLERY_PATH;

// remove from xml data - load line by line and remove line that matches filename


$location = $gallery_path.$folder;

$xml_handle = fopen($location.'content.xml', "r");

while (!feof($xml_handle)){
	$line = fgets($xml_handle, 1024);
	if (!ereg('<photo src="'.$image.'" caption="(.*)" subcaption="(.*)" />', $line)){
		// add to $xml_data
		$xml_data .= $line;
	} else {
		// rename captions and add to xml data
		$line = ereg_replace(' caption="([^"]*)"', ' caption="'.$caption.'"', $line);
		$line = ereg_replace(' subcaption="([^"]*)"', ' subcaption="'.$subcaption.'"', $line);
		$xml_data .= $line;
	}
}
// close xml file
fclose ($xml_handle);

$tempfilename = "tempfile.xml";
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

header('location:'.WEB_ROOT.'editor/imagery/gallery_detail.php?folder='.$folder.'&action=renamed');
?>