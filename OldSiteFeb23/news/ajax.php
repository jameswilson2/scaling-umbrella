<?php
require_once 'library/security/_access.inc.php';
require_once 'library/_format.inc.php';
require_once 'news/_links.inc.php';


$sql = "SELECT news_id, news_title, news_summary, news_date
		FROM tbl_news
		WHERE news_status='Active'
		ORDER BY news_date DESC LIMIT 2";


$articles = getQuery($sql);

$content = '';
while ($article = mysql_fetch_array($articles)){
	$news_id = $article['news_id'];
	$news_title = htmlspecialchars($article['news_title']);
	$news_summary = encodetext($article['news_summary']);
	$news_date = htmlspecialchars($article['news_date']);
	$news_date = date('l jS \of F Y' ,strtotime($news_date));

	$link = buildNewsLink($news_id, $news_title);

	$content .= <<<EOD
	<h3 class="underline_small"><a href="$link">$news_title</a></h3>
	<p><a href="$link">$news_summary</a></p>
EOD;

}

echo $content;

?>