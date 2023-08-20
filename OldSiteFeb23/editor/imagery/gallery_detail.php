<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';
require_once 'library/_page.class.php';


if (!isset($_GET['folder']) || $_GET['folder']==''){
	header('location:gallery.php');
	exit;
}
if (isset($_GET['page'])){
	$page_number = $_GET['page'];
} else {
	$page_number = 1;
}

$items_per_page = 5;
$num_links = 10;

$first = (($page_number-1)*$items_per_page)+1;
$last = (($page_number-1)*$items_per_page)+$items_per_page;

$location = GALLERY_PATH;
$dir = $_GET['folder'];

$_page = new Page($menus);

$header = $_page->getHeader();
$footer = $_page->getFooter();

echo $header; ?>
<h2><?php echo SITE_NAME; ?> - Edit Galleries</h2>

<?php
$action = $_GET['action'];
if ( $action ) {
	echo "<div id=\"user-notice\">";
	switch ($action){
		case 'imagesuccess':
			echo "<p>Image uploaded successfully!</p>";
			break;
		case 'deleted':
			echo "<p>Image deleted successfully!</p>";
			break;
		case 'renamed':
			echo "<p>Image renamed successfully!</p>";
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
	<p><img src="presentation/icon_plus.gif" alt="Add" width="16" height="15" title="Add" class="minicon" /> <a href="imagery/upload_caller.php?type=gallery&amp;extra=<?php echo $dir; ?>">Upload a new image</a></p>
</div>

<ul class="imagelist">
<?php
$sublocation = $location.$dir.'/';
$gallery_xml = $sublocation.'content.xml';

// open xml file line by line and extract data

$images = array();

$xml_handle = fopen($gallery_xml, "r");

$line_number = 0;
while (!feof($xml_handle)){
	$line = fgets($xml_handle, 1024);

	if (ereg('<photo [^>]+>', $line)) {
		$line_number++;
		if ($line_number >= $first && $line_number<=$last){
			// if line starts <photo then extract caption and filename - save to array
			if (ereg(' caption="[^"]+" ', $line)){
				$image_caption = ereg_replace('(.+)( caption=")([^"]+)(")(.+)', '\\3', $line);
			} else {
				// if no caption then store as blank
				$image_caption = "";
			}
			if (ereg(' subcaption="[^"]+" ', $line)){
				$image_subcaption = ereg_replace('(.+)( subcaption=")([^"]+)(")(.+)', '\\3', $line);
			} else {
				// if no image_subcaption then store as blank
				$image_subcaption = "";
			}
			$image_filename = ereg_replace('(.+)(src=")([^"]+)(")(.+)', '\\3', $line);
			$images[$image_filename][1] = $image_caption;
			$images[$image_filename][2] = $image_subcaption;
		}
	}

}
// close xml file
fclose ($xml_handle);

// echo array
foreach ($images AS $image_filename=> $image_caption){

	$src = GALLERY_WEB_PATH."$dir/thumbs/$image_filename";

?>
	<li>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td rowspan="4" width="60" valign="top" align="center"><img src="<?php echo $src; ?>" alt="<?php echo $image_caption[1]; ?>" /></td>
				<td rowspan="4" width="10" valign="top">&nbsp;</td>
			</tr>
			<tr>
				<td valign="top"><a onclick="return confirm('Are you sure you want to delete <?php echo $image_filename; ?>?');" href="imagery/gallery_delete.php?image=<?php echo urlencode($image_filename); ?>&amp;folder=<?php echo $dir; ?>/"><img src="presentation/delete.gif" onmouseover="this.src='presentation/delete_hover.gif'" onmouseout="this.src='presentation/delete.gif'" title="Delete" alt="Delete" class="actionimg" width="59" height="14" /></a>

				<strong><?php echo $image_caption[1]; ?></strong><br />
				<?php echo $image_caption[2]; ?>
				</td>
			</tr>
			<tr>
				<td height="10" valign="middle"><img src="presentation/blank.gif" width="100%" height="1" /></td>
			</tr>
			<tr>
				<td valign="top">
					<form action="imagery/gallery_rename.php?image=<?php echo urlencode($image_filename); ?>&amp;folder=<?php echo $dir; ?>/" method="post">
						<input type="text" name="caption" style="width:98.5%" /><br />
						<input type="text" name="subcaption" style="width:98.5%" /><br />
						<input type="submit" value="Change Caption" />
					</form>
				</td>
			</tr>
		</table><br />
	</li>
<?php
}

?>
</ul>
<br />
<?php // paging links
$total_results = $line_number;
$total_pages = ceil($total_results / $items_per_page);
if ($total_pages > 1) {
	if ($page_number > 1) {
		$page = $page_number - 1;
		if ($page > 1) {
			$prev = " - <a href='imagery/gallery_detail.php?page=$page&amp;folder=$dir'>Previous</a> ";
		} else {
			$prev = " - <a href='imagery/gallery_detail.php?folder=$dir'>Previous</a> ";
		}
	} else {
		$prev  = " - Previous "; // we're on page one, don't show 'previous' link
	}

	// print 'next' link only if we're not
	// on the last page
	if ($page_number < $total_pages) {
		$page = $page_number + 1;
		$next = " | <a href='imagery/gallery_detail.php?page=$page&amp;folder=$dir'>Next</a> ";
	} else {
		$next = " | Next"; // we're on the last page, don't show 'next' link
	}

	$start = $page_number - 3;
	$start = max(1, $start);

	$end  = $start + $num_links - 1;
	$end  = min($total_pages, $end);

	$page_numbers = "<strong> - Page $page_number of $total_pages</strong>";

	$pagingLink = array();
	for($page = $start; $page <= $end; $page++)	{
		if ($page == $page_number) {
			$pagingLink[] = " <span class='paging_selected'><strong>$page</strong></span> ";   // no need to create a link to current page
		} else {
			if ($page == 1) {
				$pagingLink[] = " <span class='paging_page'><a href='imagery/gallery_detail.php?folder=$dir'>$page</a></span> ";
			} else {
				$pagingLink[] = " <span class='paging_page'><a href='imagery/gallery_detail.php?page=$page&amp;folder=$dir'>$page</a></span> ";
			}
		}

	}

	$pagingLink = implode(' ', $pagingLink);

	// return the page navigation link
	$pagingLink = $pagingLink . $prev . $next . $page_numbers;
	
	if ( $pagingLink ) {
		echo "<div class=\"paging_bottom\">";
		echo $pagingLink;
		echo "</div>";
	}
	
}
?>
<?php echo $footer; ?>