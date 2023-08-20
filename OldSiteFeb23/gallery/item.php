<?php
require_once 'library/security/_access.inc.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'files/_php_builder.class.php';

$title = 'Not found';
$content = '<div class="main_content"><div class="container cs">This case study item was not found. <a href="gallery/">Please try another</a></div></div>';

if(isset($_GET['id'])) {

$gallery_id = $_GET['id'];
$sql = "SELECT *
		FROM tbl_gallery
		WHERE gallery_id = $gallery_id";

$result = getQuery($sql);


$row = mysql_fetch_array($result);

if($row) {

$gallery_name = $row['gallery_name'];
$gallery_url = htmlspecialchars($row['gallery_url']);
$gallery_content = $row['gallery_description'];

}

$title = $gallery_name . ' - ' . SITE_NAME;
$description = $gallery_caption;

$content = <<<EOD
<img src="gallery/images/thumbs/$gallery_url" alt="" class="photo photoright" />
				<h1>$gallery_name</h1>
				$gallery_content
EOD;



}
$page = new PHPBuilder();

$page->setTitle($title);
$page->setDescription($description);
$page->setKeywords($keywords);
$page->setContent($content);

$page_content = $page->buildPage();

echo $page_content;


?>