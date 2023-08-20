<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';

// rename image href

$href = $_POST['href'];
$folder = $_GET['folder'];
$image = htmlspecialchars($_GET['image']);
$gallery_path = HEADER_PATH;

$location = $gallery_path.$folder;
$filename = $location . 'content.xml';

$document = new DOMDocument();
$document->load($filename);

$path = new DOMXPath($document);
foreach($path->query("/document/gallery/photo[@src='$image']") as $node){
	$node->setAttribute('href', $href);
}

$tempfilename = "tempfile.xml";
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

header('location:'.WEB_ROOT.'editor/imagery/header_detail.php?folder='.$folder.'&action=renamed');
?>