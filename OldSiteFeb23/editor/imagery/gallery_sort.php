<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';
require_once 'library/_page.class.php';

if (!isset($_GET['folder']) || $_GET['folder']==''){
	header('location:gallery.php');
	exit;
}

$location = GALLERY_PATH;
$dir = $_GET['folder'];

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>

<h2><?php echo SITE_NAME; ?> - Edit Gallery Order</h2>

<script type="text/javascript">
<!--
$(document).ready(function(){
	$("#sortable_gallery").sortable({ placeholder:"placeholder_hover", revert:true });
});
//-->
</script>
<script src="behaviour/ui.base.js" type="text/javascript"></script>
<script src="behaviour/ui.sortable.js" type="text/javascript"></script>
<script src="behaviour/ui.dimensions.js" type="text/javascript"></script>

<?php
$action = $_GET['action'];
if ( $action ) {
	echo "<div id=\"user-notice\">";
	switch ($action){
		case 'imagesuccess':
			echo "<p>Images reordered successfully!</p>";
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

<div id="newitems">
<strong>Drag the photos below to the order you want, then click ...</strong>
<form id="sck-altform" method="post" action="imagery/gallery_sort_process.php" class="actionimg">
	<input type="hidden" id="folder" name="folder" value="<?php echo $dir; ?>" />
	<input type="hidden" id="result" name="result" value="" />
	<input type="submit" value="Sort Photos!" onclick="document.getElementById('result').value = $('#sortable_gallery').sortable('serialize');" />
</form>
</div>

<ul id="sortable_gallery">
<?php
$sublocation = $location.$dir.'/';
$gallery_xml = $sublocation.'content.xml';

// open xml file line by line and extract data

$images = array();

$xml_handle = fopen($gallery_xml, "r");

$line_number = 0;
while (!feof($xml_handle)){
	$line = fgets($xml_handle, 1024);

	if (ereg('<gallery [^>]+>', $line)){

	} elseif (ereg('<photo [^>]+>', $line)) {
		$line_number++;
		$image_filename = ereg_replace('(.+)(src=")([^"]+)(")(.+)', '\\3', $line);
		$images[$line_number] = $image_filename;
	}

}
// close xml file
fclose ($xml_handle);

// echo array
foreach ($images AS $image_line=> $image_name){
	$gallery = GALLERY_WEB_PATH;
	$image_row = <<<EOD
		<li id="item_$image_line"><img src="$gallery$dir/thumbs/$image_name" width="60" height="60" /></li>\n
EOD;
	echo $image_row;
}
?>
</ul>

<?php echo $footer; ?>