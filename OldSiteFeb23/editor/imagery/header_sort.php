<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';
require_once 'library/_page.class.php';

if (!isset($_GET['folder']) || $_GET['folder']==''){
	header('location:header.php');
	exit;
}

$location = HEADER_PATH;
$dir = $_GET['folder'];

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>

<h2><?php echo SITE_NAME; ?> - Edit Gallery Order</h2>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js" type="text/javascript"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js" type="text/javascript"></script>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	$("#sortable_header").sortable({ placeholder:"placeholder_hover", revert:true });
});
//]]>
</script>

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
//<![CDATA[
$(document).ready(function() {	
	$("#user-notice").hide();
	if ($("#user-notice").is(":hidden")) {
		$("#user-notice").slideDown("slow");
		$("#user-notice").animate( { backgroundColor: "#DC9E26" }, 600 );
		$("#user-notice").animate( { backgroundColor: "#2683DC" }, 2000 );
	}
});
//]]>
</script>
<?php } ?>

<div id="newitems">
<form method="post" action="imagery/header_sort_process.php" onsubmit="return processForm(this)">
	<input type="hidden" name="order" value="test"/>
	<input type="hidden" name="folder" value="<?php echo $dir;?>"/>
	<strong>Drag the photos below to the order you want, then click ...</strong>
	<input type="submit" value="Sort Photos" />
	<script type="text/javascript">
	//<![CDATA[
		function processForm(form){
			var order = []; $("#sortable_header img").each(function(){order.push(this.getAttribute("alt"));});
			form.order.value = JSON.stringify(order);
			return true;
		}
	//]]>
	</script>
</form>

</div>

<ul id="sortable_header">
<?php
$sublocation = $location.$dir.'/';
$gallery_xml = $sublocation.'content.xml';

// open xml file line by line and extract data

$images = array();

$document = new DOMDocument();
$document->load($gallery_xml);

$path = new DOMXPath($document);
foreach($path->query("/document/gallery/photo") as $node){
	$node_src = $node->getAttribute('src');
	$src = "imagery/_header_thumbnail.php?image=".urlencode($node_src)."&amp;folder=$dir/";
	echo "<li><img src=\"$src\" alt=\"$node_src\" /></li>";
}
?>
</ul>

<?php echo $footer; ?>