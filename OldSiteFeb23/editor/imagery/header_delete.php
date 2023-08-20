<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';

// delete image

$folder = $_GET['folder'];
$image = $_GET['image'];
$gallery_path = HEADER_PATH;
$filename = $gallery_path.$folder.$image;
$deleted = $filename.'.deleted';

// copy file to 'deleted' location
$ok = copy($filename, $deleted);

// delete current file
// !also delete thumbnail!
@unlink($filename);

// remove from xml data - load line by line and remove line that matches filename

$location = $gallery_path.$folder.'/';
$gallery_xml = $location . 'content.xml';

$document = new DOMDocument();
$document->load($gallery_xml);

$nodes = array();

$path = new DOMXPath($document);
foreach($path->query("/document/gallery/photo[@src='$image']") as $node){
	$nodes[] = $node;
}

foreach($nodes as $node){
	$node->parentNode->removeChild($node);
}

$tempfilename = $location."tempfile.xml";
$filename = $location."content.xml";
$backup = $location."content.xml.BCK";

$document->save($tempfilename);

// delete old backup
@unlink ($backup);

// copy old file to backup
$ok = copy($filename, $backup);

// copy temporary file to correct location
$ok = copy($tempfilename, $filename);

// delete temporary file
@unlink($tempfilename);

header('location:'.WEB_ROOT.'editor/imagery/header_detail.php?folder='.$folder.'&action=deleted');
?>