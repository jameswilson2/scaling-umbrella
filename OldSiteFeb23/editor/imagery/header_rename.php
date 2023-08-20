<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';

// rename image caption

$caption = $_POST['caption'];
$folder = $_GET['folder'];
$url = $_POST['url'];
$image = htmlspecialchars($_GET['image']);
$gallery_path = HEADER_PATH;

$location = $gallery_path.$folder;
$filename = $location . 'content.xml';

$document = new DOMDocument();
$document->load($filename);

$path = new DOMXPath($document);
if(isset($_POST['caption'])) {
	foreach($path->query("/document/gallery/photo[@src='$image']") as $node){
		$node->setAttribute('caption', $caption);
	}
}
if(isset($_POST['url'])) {
	foreach($path->query("/document/gallery/photo[@src='$image']") as $node){
		$node->setAttribute('href', $url);
	}
}

$tempfilename = "tempfile.xml";
$filename = $location."content.xml";
$backup = $location."content.xml.BCK";

$document->save($tempfilename);

// copy old file to backup
$ok = copy($filename, $backup);

// copy temporary file to correct location
$ok = copy($tempfilename, $filename);

// delete temporary file
@unlink($tempfilename);

header('location:'.WEB_ROOT.'editor/imagery/header_detail.php?folder='.$folder.'&action=renamed');
?>