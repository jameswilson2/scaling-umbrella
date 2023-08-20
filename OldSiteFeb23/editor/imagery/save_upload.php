<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';
require_once 'imagery/_image_lists.inc.php';
require_once 'library/_page.class.php';


// prompt to save depending on type


if(isset($_GET['new_filename']))
{
	$newfile = $_GET['new_filename'];
	$filename = $_GET['filename'];
	$location = IMAGE_PATH;
	// open current directory
	$dp = opendir($location);
	// loop through the directory
	$file_list = Array();
	while (false !== ($entry = readdir($dp))) {
		if (is_file($location.$entry) && eregi('.jpe?g', $entry)) {
			// $entry is a jpg or jpeg file...
			$file_list[] = $entry;
		}
	}
	// Close directory
	closedir($dp);

	// check new filename against current list
	// must check if it''s the same with either extension
	foreach ($file_list as $file){
		if (strtolower($file)==strtolower($newfile)||strtolower($file)==strtolower($newfile.".jpg")||strtolower($file)==strtolower($newfile.".jpeg")){
			// file already exists
			header('location:'.WEB_ROOT.'editor/imagery/save_upload.php?action=imageexists&filename='.$filename);
			exit();
		}
	}

	// check that filename only contains alphanumeric characters
	if (ereg('^[0-9a-zA-Z]+$', $newfile)){
		$newfile = strtolower($newfile).'.jpg';
		$new = IMAGE_PATH.$newfile;
		$old = EDITABLE_ROOT.'editor/temp/'.$filename;
		rename($old, $new);

		//buildImageList();
		// redirect to success page
		header('location:'.WEB_ROOT.'editor/imagery/index.php?action=newimagesuccess&filename='.$newfile);
		exit();
	}
	header('location:'.WEB_ROOT.'editor/imagery/save_upload.php?action=imagefail&filename='.$filename);
	exit();
}



if (!isset($_GET['filename'])){
	header('location:'.WEB_ROOT.'editor/imagery/index.php');
	exit;
}

$_page = new Page($menus);

$header = $_page->getHeader();
$footer = $_page->getFooter();

$filename = $_GET['filename'];

$image = WEB_ROOT.'editor/temp/'.$filename;

echo $header;

?>

<?php
$action = $_GET['action'];
if ( $action ) {
	echo "<div id=\"user-notice\">";
	switch ($action){
		case 'imageexists':
			echo "<p>Cannot create new image - an image with this name already exists</p>";
			break;
		case 'imagefail':
			echo "<p>Cannot create an image with this name</p>";
			break;
	}
	echo "</div>";
?>
<script type="text/javascript">
<!--
$(document).ready(function() {
	$("#user-notice").hide();
	if ($("#user-notice").is(":hidden")) {
		$("#user-notice").slideDown("slow");
		$("#user-notice").animate( { backgroundColor: "#DC9E26" }, 600 );
		$("#user-notice").animate( { backgroundColor: "#2683DC" }, 2000 );
	}
});
//-->
</script>
<?php } ?>

<p class="image_header"><strong>Here is your cropped image:</strong></p>
<div class="image_image"><img src="imagery/_showtempimage.php?image=<?php echo $filename; ?>" alt="" /></div>

<p class="image_header"><strong>Cancel upload of this image?</strong></p>
<div class="image_image">
	<p><strong>Yes:</strong> <a href="imagery/index.php">cancel</a> or <strong>No:</strong> please go to next box below:</p>
</div>

<p class="image_header"><strong>Please choose a filename:</strong></p>
<div class="image_image">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
	<p>
		Type your filename here: <input type="text" name="new_filename" id="new_filename" />.jpg <input type="hidden" value="<?php echo $filename; ?>" name="filename" /><input type="submit" value="Save Image" />
	</p>
</form>
</div>

<?php echo $footer; ?>