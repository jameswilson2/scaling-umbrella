<?php
require_once 'library/security/_access.inc.php';
require_once 'library/_paging.class.php';
require_once 'library/_format.inc.php';
require_once 'news/_links.inc.php';
require_once 'files/_php_builder.class.php';


$sql = "SELECT news_id, news_title, news_summary, news_date
		FROM tbl_news
		WHERE news_status='Active'
		ORDER BY news_date DESC";

if (isset($_GET['page']) && $_GET['page']!=""){
	$page=$_GET['page'];
} else {
	$page=1;
}

$content = "<h2>Latest News</h2>";

$self = "news/";
$pager = new FriendlyPager($sql, '5', $self, '', $page);

$sql = $pager->getPagedQuery();
$paging_links = $pager->getPagingLinks();

$articles = getPublicQuery($sql, 'Could not get articles: ');

while ($article = mysql_fetch_array($articles)){
	$news_id = $article['news_id'];
	$news_title = htmlspecialchars($article['news_title']);
	$news_summary = encodetext($article['news_summary']);
	$news_date = htmlspecialchars($article['news_date']);
	$news_date = date('l jS \of F Y' ,strtotime($news_date));

	$link = buildNewsLink($news_id, $news_title);

	$content .= "<h3>$news_title</h3>
				<p>$news_summary</p>
				<p>$news_date - <a href=\"$link\">View</a></p>";
}

$title = SITE_NAME." Latest News";
$description = 'Latest News';
$keywords = '';

$content .= $paging_links;


$page = new PHPBuilder();

$page->setTitle($title);
$page->setDescription($description);
$page->setKeywords($keywords);
$page->setContent($content);

$page_content = $page->buildPage();

echo $page_content;

?>