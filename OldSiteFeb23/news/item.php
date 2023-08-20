<?php
require_once 'library/security/_access.inc.php';
require_once 'library/_paging.class.php';
require_once 'files/_php_builder.class.php';

if (!isset($_GET['nid']) || $_GET['nid']==''){
	header('location:'.WEB_ROOT.'news/');
	exit;
}

$news_id = $_GET['nid'];

$sql = "SELECT news_title, news_content, news_date
		FROM tbl_news
		WHERE news_status='Active'
			AND news_id='$news_id'
		ORDER BY news_date DESC";

$content = "";

$articles = getPublicQuery($sql, 'Could not get article: ');

$article = mysql_fetch_array($articles);
$news_id = $article['news_id'];
$news_title = htmlspecialchars($article['news_title']);
$news_content = $article['news_content'];
$news_date = htmlspecialchars($article['news_date']);
$news_date = date('l jS \of F Y' ,strtotime($news_date));



$content = <<<EOD
		<h2>$news_title</h2>
		$news_content
		<p><em>$news_date</em></p>
		<p><a href="news/">Back to Latest News</a></p>
EOD;

$title = SITE_NAME." - $news_title";
$description = '$news_title';
$keywords = '';

$page = new PHPBuilder();

$page->setTitle($title);
$page->setDescription($description);
$page->setKeywords($keywords);
$page->setContent($content);

$page_content = $page->buildPage();

echo $page_content;

?>