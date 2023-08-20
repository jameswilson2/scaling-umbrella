<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';
require_once 'library/_page.class.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){

	$caption = $_POST['caption'];
	$imagefilename = $_POST['filename'];
	$folder = $_POST['folder'];
	$type = $_POST['type'];

	$gallery_path = HEADER_PATH;

	$location = HEADER_PATH.$folder.'/';

	$old = EDITABLE_ROOT.'editor/temp/'.$imagefilename;
	$new = $location.$imagefilename;

	rename($old, $new);
	
	$tempfilename = "tempfile.xml";
	$filename = $location."content.xml";

	// remove from xml data - load line by line and remove line that matches filename
	$document = new DOMDocument();
	$document->load($filename);

	$path = new DOMXPath($document);
	foreach($path->query("/document/gallery") as $node){
		$element = $document->createElement('photo');
		$element->setAttribute('src',$imagefilename);
		$element->setAttribute('caption',$caption);
		$node->appendChild($element);
	}

	$backup = $location."content.xml.BCK";

	$document->save($tempfilename);
	// copy old file to backup
	$ok = copy($filename, $backup);

	// copy temporary file to correct location
	$ok = copy($tempfilename, $filename);

	// delete temporary file
	@unlink($tempfilename);

	header('location:'.WEB_ROOT.'editor/imagery/header_detail.php?folder='.$folder.'&action=imagesuccess');
	exit();

}


if (!isset($_GET['filename'])){
	header('location:'.WEB_ROOT.'editor/imagery/header.php');
	exit;
}

$filename = $_GET['filename'];
$dir = $_GET['extra'];
$type = $_GET['type'];

$_page = new Page($menus);

$header = $_page->getHeader();
$footer = $_page->getFooter();

echo $header;

// display image, prompt for caption - if ok then save
$image = WEB_ROOT.'editor/temp/'.$filename;
?>
<h2>Confirm and Add Caption</h2>
<p><img src="imagery/_showtempimage.php?image=<?php echo $filename; ?>" alt="" /></p>
<form action="imagery/save_header.php" method="post" id="sck-form">
	<p>
		<label>Caption:</label>
		<input type="text" name="caption" class="field" />
	</p>
	<p>
		<input type="hidden" value="<?php echo $filename; ?>" name="filename" />
		<input type="hidden" value="<?php echo $dir; ?>" name="folder" />
		<input type="hidden" value="<?php echo $type; ?>" name="type" />
		<input type="submit" value="Save Image" />
	</p>
</form>

<?php echo $footer; ?>