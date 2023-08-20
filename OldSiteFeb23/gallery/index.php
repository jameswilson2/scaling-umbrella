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
	
	$tabcontent = <<<EOD
		<div class="cs scrollitem">
			<div class="left_section"><img src="gallery/images/thumbs/$gallery_url" alt="" class="photo" /></div>
			<div class="right_section">
				<h2>$gallery_name</h2>
				$gallery_caption
				<p><a href="gallery/item/$gallery_id/$ns_gn" class="btn pbtn">&gt; Read full story<span class="icon"></span></a></p>
			</div>
		</div>
EOD;
	
	$tabscontent .= $tabcontent;

}

$site_name = SITE_NAME;
$portfoliocontent = file_get_contents('../_casestudies-content.inc');
$content = <<<EOD

	$portfoliocontent
	<div class="clearspace"></div>
	<p class="h1"><span class="colour">Case Studies</span></p>
	<div class="scroller cs">
		$tabscontent
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