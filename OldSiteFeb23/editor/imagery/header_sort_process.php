<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';

$order = json_decode($_POST['order']);

$gallery_path = HEADER_PATH;
$folder = $_POST['folder'];
$location = $gallery_path.$folder.'/';
$gallery_xml = $location . 'content.xml';

$document = new DOMDocument();
if(@$document->load($gallery_xml) === false){
	echo "Couldn't load xml file!";
	exit;
}

$node_src_index = array();

$path = new DOMXPath($document);
foreach($path->query("/document/gallery/photo") as $node){
	$node_src_index[$node->getAttribute('src')] = $node;
}

$galleryNode = null;

foreach($node_src_index as $key => $node){
	$galleryNode = $node->parentNode;
	$node->parentNode->removeChild($node);
}

foreach($order as $src){
	$galleryNode->appendChild($node_src_index[$src]);
}

$tempfilename = $location."tempfile.xml";
$document->save($tempfilename);

@unlink($gallery_xml);
rename($tempfilename, $gallery_xml);

$web_root = WEB_ROOT;
header("location: {$web_root}editor/imagery/header_sort.php?folder=$folder&action=imagesuccess");
