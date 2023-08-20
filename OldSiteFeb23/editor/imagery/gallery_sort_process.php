<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';

$gallery_path = GALLERY_PATH;

$folder = $_POST['folder'];

$location = GALLERY_PATH.$folder.'/';

// posted new order

$order = $_POST['result'];
$order_list = explode(",", $order);

$num_items = count($order_list);

$xml_data = "";

$xml_handle = fopen($location.'content.xml', "r");
$line_number=0;
while (!feof($xml_handle)){
	$line = fgets($xml_handle, 1024);
	if (ereg('<photo [^>]+>', $line)) {
		$line_number++;
		$old_xml_data[$line_number] = $line;
	} elseif (ereg('<gallery [^>]+>', $line)){
		$gallery_type = $line;
	}
}
// close xml file
fclose ($xml_handle);

if ($line_number==$num_items){

	foreach ($order_list AS $order_row){
		$xml_data .= $old_xml_data[$order_row];
	}

	$xml_data = <<<EOD
<?xml version="1.0" ?>
<document>
$gallery_type$xml_data	</gallery>
</document>
EOD;


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

	header('location:'.WEB_ROOT.'editor/imagery/gallery_sort.php?folder='.$folder.'&action=imagesuccess');
	exit();

} else {

	header('location:'.WEB_ROOT.'editor/imagery/gallery_sort.php?folder='.$folder);
	exit();

}

?>