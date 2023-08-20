<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';
require_once 'library/_page.class.php';

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>
<h2><?php echo SITE_NAME; ?> - Edit Galleries</h2>
<?php if (isset($_GET['action'])):
	$action = $_GET['action'];
	echo "<div id=\"user-notice\">";
	switch ($action){
		case 'newgallerysuccess':
			echo "<p>Gallery created successfully.</p>";
			break;
		case 'newgalleryfail':
			echo "<p>Could not create gallery.</p>";
			break;
		case 'newgalleryexists':
			echo "<p>Cannot create gallery - exists already.</p>";
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
<?php endif; ?>
<div id="newitems">

	<p><img src="presentation/folder_open.gif" alt="New Gallery" width="16" height="14" title="New Gallery" class="minicon" /> <strong><a href="javascript:;" onclick="expand('newgalleryDiv')">Create a new Gallery</a></strong></p>

	<div id="newgalleryDiv" class="hide">
		<div class="newitem">
			<form action="imagery/gallery_new.php" method="post">
				<p><strong>New gallery name:</strong> <input type="text" name="newGallery" id="newGallery" value="" /><input type="submit" id="createNewGallery" name="createNewGallery" value="Create New Gallery" onclick="this.form.submit();this.disabled=true;this.value='Creating';" /></p>
			</form>
		</div>
	</div>

</div>
<ul class="filelist">
<?php
$location = GALLERY_PATH;

// open current directory
$dp = opendir($location);
$dir_list = array();
// loop through the directory
while (false !== ($directory = readdir($dp))) {
	$match = 0;
	$disallowed = array('.', '..','_template');
	foreach ($disallowed as $dis) {
		if ($dis == $directory ){
			$match++;
		}
	}
	if (is_dir($location . $directory) && $match == 0) {
		// $directory is a directory...
		$dir_list[] = $directory;
	}
}

// Close top level directory
closedir($dp);

$dir_lowercase = array_map('strtolower', $dir_list);
array_multisort($dir_lowercase, SORT_ASC, SORT_STRING, $dir_list);

$i=1;

foreach ($dir_list as $dir){
	$sublocation = $location.$dir.'/';
	?>
	<li>
		<div class="actionimg"><a href="imagery/gallery_sort.php?folder=<?php echo $dir; ?>">Sort the photos in this gallery</a></div>
		<img src="presentation/folder_closed.gif" id="image<?php echo $dir; ?>" width="16" height="13" alt="directory" />
	<a class="folder" href="imagery/gallery_detail.php?folder=<?php echo $dir; ?>"><?php echo $dir; ?></a>
	</li>
<?php
}
?>
</ul>
<?php echo $footer; ?>
