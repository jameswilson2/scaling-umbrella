<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';
require_once 'library/_page.class.php';


if (!isset($_GET['folder']) || $_GET['folder']==''){
	header('location:header.php');
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

$location = HEADER_PATH;
$dir = $_GET['folder'];

$_page = new Page($menus);

$header = $_page->getHeader();
$footer = $_page->getFooter();

echo $header; ?>
<h2><?php echo SITE_NAME; ?> - Edit Flash Headers</h2>

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
	<p><img src="presentation/icon_plus.gif" alt="Add" width="16" height="15" title="Add" class="minicon" /> <a href="imagery/upload_caller.php?type=header&amp;extra=<?php echo $dir; ?>">Upload a new image</a></p>
</div>

<script type="text/javascript" src="files/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="files/lists/image_list.js"></script>
<script type="text/javascript" src="files/lists/link_list.js"></script>
<script type="text/javascript" src="files/lists/media_list.js"></script>

<?php
$base_url = WEB_ROOT;
$sublocation = $location.$dir.'/';
$gallery_xml = $sublocation.'content.xml';

// open xml file line by line and extract data

$images = array();

$document = new DOMDocument();
$document->load($gallery_xml);

$path = new DOMXPath($document);
foreach($path->query("/document/gallery/photo") as $node){
	$src = $node->getAttribute('src');
	$images[$src]['caption'] = $node->getAttribute('caption');
	$images[$src]['url'] = $node->getAttribute('href');
}

$i = 0;

foreach ($images AS $image_filename => $image_info){
	
	$i++;
	
	$attrs = $image_info;
	$filename = $image_filename;
	$filename_html = htmlspecialchars($filename);
	
	$preview_image_url = "imagery/_header_thumbnail.php?image=$filename_html&amp;folder=$dir/";
	
	$caption_html = $attrs["caption"];
	$caption_html_text = htmlentities($caption_html, ENT_COMPAT, "UTF-8");
	
	$link = $attrs["url"];
	$link_html = htmlspecialchars($link, ENT_COMPAT, "UTF-8");
	
	$css = CSS;
	
	echo <<<EOD
	<div class="gallery-item">
		
		<div class="gallery-image">
			<img src="$preview_image_url" alt="" />
		</div>
		
		<div class="gallery-caption">
			$caption_html
		</div>
		
		<div class="gallery-link">
			Link URL: $link_html
		</div>
		
		<div class="gallery-edit">
		
			<ul class="links">
				<li><a href="#gallery-edit-caption_$i" class="tab">Edit Caption</a></li>
				<li><a href="#gallery-edit-link_$i" class="tab">Edit Link URL</a></li>
				<li><a href="imagery/header_delete.php?folder=$dir&image=$filename_html" onclick="return confirm('Are you sure you want to delete $filename_html?');">Delete</a></li>
			</ul>
			
			<div class="gallery-edit-caption tab-content" id="gallery-edit-caption_$i">
				
				<form action="imagery/header_rename.php?image=$filename_html&amp;folder=$dir/" method="post">
					<textarea name="caption" id="gallery-edit-caption_$i-caption">$caption_html_text</textarea>
					<input type="submit" value="Save Change" />
					
					<script type="text/javascript">
	                    tinyMCE.init({
	                        // General options
	                        mode : "exact",
	                        elements : "gallery-edit-caption_$i-caption",
	                        theme : "advanced",
	                        relative_urls : true, // Default value
	                        document_base_url : '$base_url',
	                        plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
	                        cleanup: false,
	                        
	                        // Theme options
	                        theme_advanced_buttons1 : "newdocument,bold,italic,underline,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,template,cut,copy,paste",
	                        theme_advanced_buttons2 : "pastetext,pasteword,search,replace,bullist,numlist,outdent,indent,blockquote,undo,redo,link,unlink,anchor,image,cleanup,help,code",
	                        theme_advanced_buttons3 : "tablecontrols,hr,removeformat,visualaid,sub,sup,charmap,fullscreen",
	                        theme_advanced_toolbar_location : "top",
	                        theme_advanced_toolbar_align : "left",
	                        theme_advanced_statusbar_location : "bottom",

	                        width : "594",
	                        height : "400",
	                        
	                        // Example content CSS (should be your site CSS)
	                        content_css : "$css",
	                        
	                        // Drop lists for link/image/media/template dialogs
	                        template_external_list_url : "$base_url/editor/files/lists/template_list.js",
	                        external_link_list_url : "$base_url/editor/files/lists/link_list.js",
	                        external_image_list_url : "$base_url/editor/files/lists/image_list.js",
	                        media_external_list_url : "$base_url/editor/files/lists/media_list.js"

	                    });
	                </script>
					
				</form>
				
			</div>
			
			<div class="gallery-edit-link tab-content" id="gallery-edit-link_$i">
				<form action="imagery/header_rename.php?image=$filename_html&amp;folder=$dir/" method="post">
	                <input type="text" name="url" style="width:98.5%" value="$link_html" /><br />
	                <input type="submit" value="Save Change" />
	            </form>
			</div>
		</div>
	</div>
EOD;
}
?>
<script type="text/javascript">
(function(){
	
	$(".tab-content").hide();
	
	var activeTab = null;
	var activeTabLink = null;
	
	function blurActiveTab(){
		if(activeTab !== null){
			activeTab.slideUp(200);
			$(activeTabLink).removeClass("active");
			activeTab = null;
		}
	}
	
	$(".links a.tab").click(function(event){
		
		event.preventDefault();
		
		if(this == activeTabLink){
			return;
		}
		
		blurActiveTab();
		
		activeTab = $(this.getAttribute("href").replace(/^.*#/, "#"));
		activeTab.slideDown(400);
		
		activeTabLink = this;
		$(activeTabLink).addClass("active");
	});
	
})();
</script>

<br />
<?php // paging links
$total_results = $line_number;
$total_pages = ceil($total_results / $items_per_page);
if ($total_pages > 1) {
	if ($page_number > 1) {
		$page = $page_number - 1;
		if ($page > 1) {
			$prev = " - <a href='imagery/header_detail.php?page=$page&amp;folder=$dir'>Previous</a> ";
		} else {
			$prev = " - <a href='imagery/header_detail.php?folder=$dir'>Previous</a> ";
		}
	} else {
		$prev  = " - Previous "; // we're on page one, don't show 'previous' link
	}

	// print 'next' link only if we're not
	// on the last page
	if ($page_number < $total_pages) {
		$page = $page_number + 1;
		$next = " | <a href='imagery/header_detail.php?page=$page&amp;folder=$dir'>Next</a> ";
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
				$pagingLink[] = " <span class='paging_page'><a href='imagery/header_detail.php?folder=$dir'>$page</a></span> ";
			} else {
				$pagingLink[] = " <span class='paging_page'><a href='imagery/header_detail.php?page=$page&amp;folder=$dir'>$page</a></span> ";
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