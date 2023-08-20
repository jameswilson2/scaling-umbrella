<?php
require_once 'library/security/_access.inc.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'files/_php_builder.class.php';


$sql = "SELECT gallery_id, gallery_url, gallery_name, gallery_caption
		FROM tbl_gallery
		ORDER BY gallery_priority ASC";



$result = getQuery($sql);

$title = SITE_NAME.' - Gallery';


while ($row = mysql_fetch_array($result)){
	$gallery_id = $row['gallery_id'];
	$gallery_name = htmlspecialchars($row['gallery_name']);
	$gallery_url = htmlspecialchars($row['gallery_url']);
	$gallery_caption = $row['gallery_caption'];
	$ns_gn = strtolower(str_replace(' ', '-', $gallery_name));

	$image = <<<EOD
		<li><a href="#gallery$gallery_id"><img src="gallery/images/thumbs/$gallery_url" alt="$gallery_name" width="148" height="87" /></a></li>
EOD;
	
	$tabcontent = <<<EOD
		<div class="tab_content cs" id="gallery$gallery_id">
			$gallery_caption
			<p><a href="gallery/item/$gallery_id/$ns_gn" class="btn pbtn">Read more<span class="icon"></span></a>
			<a href="enquiries/" class="btn pbtn">Make an Enquiry<span class="icon"></span></a></p>
		</div>
EOD;
	
	$tabscontent .= $tabcontent;
	$images .= $image;

}

$site_name = SITE_NAME;
$portfoliocontent = file_get_contents('../_finance-content.inc');
$subportfolio = file_get_contents('../_sub_content.inc');
$content = <<<EOD

	<div class="major_left">
		$portfoliocontent
	</div>
	<div class="minor_right">
		$subportfolio
	</div>
	<div class="clearspace"></div>
	<div class="break_line"></div>
	<h2><span class="subcolour">New businesses</span> we have worked with</h2>
	<div class="tabs">
		<ul class="imagetabs cs">
			$images
		</ul>
		$tabscontent
		<div class="clearspace"></div>
	</div>
</div>

EOD;

$page = new PHPBuilder();

$page->setTitle($title);
$page->setDescription($description);
$page->setKeywords($keywords);
$page->setContent($content);

$page_content = $page->buildPage();

echo $page_content;


?>