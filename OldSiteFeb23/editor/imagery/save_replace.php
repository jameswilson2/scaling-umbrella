<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';
require_once 'library/_page.class.php';


if (isset($_GET['replace'])){

	$filename = $_GET['filename'];
	$extra = $_GET['extra'];

	$old_image = IMAGE_PATH.$extra;
	$new_image = EDITABLE_ROOT.'editor/temp/'.$filename;

	rename($old_image, $old_image.'.BCK');

	rename($new_image, $old_image);

	// redirect to success page
	header('location:'.WEB_ROOT.'editor/imagery/index.php?action=imagesuccess');
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
$extra = $_GET['extra'];

$image = WEB_ROOT.'editor/temp/'.$filename;


echo $header; ?>
<p class="image_header"><strong>Original:</strong></p>
<div class="image_image"><img src="imagery/_showimage.php?image=<?php echo $extra; ?>" alt="" /></div>

<p class="image_header"><strong>New Image:</strong></p>
<div class="image_image"><img src="imagery/_showtempimage.php?image=<?php echo $filename; ?>" alt="" /></div>

<p class="image_header"><strong>Do you wish to replace this image?</strong></p>
<div class="image_image">
	<div class="article-left">
		<p><strong>No:</strong> <a href="imagery/index.php">Cancel</a> or ...</p>
	</div>
	<div class="article-rightalt">
		<p><strong>Yes:</strong> <a href="imagery/save_replace.php?replace=1&amp;filename=<?php echo $filename; ?>&amp;extra=<?php echo $extra; ?>">Replace</a></p>
	</div>
	<div class="clear"></div>
</div>

<?php echo $footer; ?>