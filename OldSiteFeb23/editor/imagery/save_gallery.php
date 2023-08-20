<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';
require_once 'library/_page.class.php';


if (isset($_POST['caption'])){
	$caption = $_POST['caption'];
	$subcaption = $_POST['subcaption'];
	$filename = $_POST['filename'];
	$folder = $_POST['folder'];
	$type = $_POST['type'];

	$gallery_path = GALLERY_PATH;

	$location = GALLERY_PATH.$folder.'/';

	$gallery_xml = $location.'content.xml';

	$xml_handle = fopen($gallery_xml, "r");

	while (!feof($xml_handle)){
		$line = fgets($xml_handle, 1024);

		if (ereg('<gallery [^>]+>', $line)){
			// if line starts <gallery then check is on page then extract size
			$th_w = ereg_replace('(.+)(th_w=")([^"]+)(")(.+)', '\\3', $line);
			$th_h = ereg_replace('(.+)(th_h=")([^"]+)(")(.+)', '\\3', $line);
		}

	}
	// close xml file
	fclose ($xml_handle);

	$old = EDITABLE_ROOT.'editor/temp/'.$filename;
	$new = $location.$filename;

	// create thumb

	list($or_w, $or_h, $or_t) = getimagesize($old);

	$or_image = imagecreatefromjpeg($old);

	$th_image = imagecreatetruecolor($th_w, $th_h);

	// cut out a rectangle from the resized image and store in thumbnail

	if ($or_w < $or_h){
		$th_x1 = 0;
		$th_x2 = $or_w;
		$th_y1 = ($or_h - ($or_w))/2;
		$th_y2 = $or_w;
	} else {
		$th_x1 = ($or_w - ($or_h))/2;;
		$th_x2 = $or_h;
		$th_y1 = 0;
		$th_y2 = $or_h;
	}

	imagecopyresampled($th_image, $or_image, 0, 0, $th_x1, $th_y1, $th_w, $th_h, $th_x2, $th_y2);

	// generate thumbnail
	imagejpeg($th_image, $location.'thumbs/'.$filename, IMAGE_QUALITY);

	rename($old, $new);

	// remove from xml data - load line by line and remove line that matches filename

	$xml_data = file_get_contents($location.'content.xml');

	list($xml_data, $footer) = explode("</gallery>", $xml_data);

	$xml_data .= <<<EOD
	<photo src="$filename" caption="$caption" subcaption="$subcaption" />
	</gallery>
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

	header('location:'.WEB_ROOT.'editor/imagery/gallery_detail.php?folder='.$folder.'&action=imagesuccess');
	exit();

}


if (!isset($_GET['filename'])){
	header('location:'.WEB_ROOT.'editor/imagery/gallery.php');
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
<form action="imagery/save_gallery.php" method="post" id="sck-form">
	<p>
		<label>Caption:</label>
		<input type="text" name="caption" class="field" />
	</p>
	<p>
		<label>Subcaption:</label>
		<input type="text" name="subcaption" class="field" />
	</p>
	<p>
		<input type="hidden" value="<?php echo $filename; ?>" name="filename" />
		<input type="hidden" value="<?php echo $dir; ?>" name="folder" />
		<input type="hidden" value="<?php echo $type; ?>" name="type" />
		<input type="submit" value="Save Image" />
	</p>
</form>

<?php echo $footer; ?>