<?php
require_once 'library/security/_access.inc.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'library/_format.inc.php';
require_once 'news/_links.inc.php';

$sql = "SELECT news_id, news_title, news_summary, news_date
		FROM tbl_news
		WHERE news_status='Active'
		ORDER BY news_date DESC LIMIT 4";
		
$articles = getQuery($sql);

$count = 0;
$items = '';
while ($article = mysql_fetch_array($articles)){
	$count++;
	$news_id = $article['news_id'];
	$news_title = htmlspecialchars($article['news_title']);
	$news_summary = encodetext($article['news_summary']);
	$news_date = htmlspecialchars($article['news_date']);
	$news_date = date('D, d M Y H:i:s O' ,strtotime($news_date));

	$link = WEB_ROOT . buildNewsLink($news_id, $news_title);

	$items .= <<<EOD
	<item>
		<title>$news_title</title>
		<link>$link</link>
		<description>$news_summary</description>
		<pubDate>$news_date</pubDate>
	</item>
EOD;

}

header("Content-Type: application/rss+xml");
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>"; ?>
<rss version="2.0">
<channel>
	<language>en-GB</language>
	<webmaster><?php echo CONTACT_EMAIL;?></webmaster>
	<managingEditor><?php echo CONTACT_EMAIL;?></managingEditor>
	<category>Website Design &amp; Development</category>
	<title><?php echo SITE_NAME;?> Latest News</title>
	<link><?php echo WEB_ROOT . "/news";?></link>
	<description>Website design and development company in Kendal, Cumbria</description>
	<?php echo $items;?>
</channel>
</rss>