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
	$news_title = utf8_encode($article['news_title']);
	$news_summary = utf8_encode($article['news_summary']);
	$news_date = $article['news_date'];
	$news_date = date('D, d M Y H:i:s O' ,strtotime($news_date));
	
	$link = WEB_ROOT . buildNewsLink($news_id, $news_title);
	
	$xml = new XMLWriter();
	$xml->openMemory();
	$xml->setIndent(true);
	
	$xml->startElement("item");
	
	$xml->startElement("title");
	$xml->text($news_title);
	$xml->endElement();
	
	$xml->startElement("link");
	$xml->text($link);
	$xml->endElement();
	
	$xml->startElement("guid");
	$xml->text($link);
	$xml->endElement();
	
	$xml->startElement("description");
	$xml->text($news_summary);
	$xml->endElement();
	
	$xml->startElement("pubDate");
	$xml->text($news_date);
	$xml->endElement();
	
	$xml->endElement();
	
	$items.= $xml->flush();
}

header("Content-Type: application/rss+xml");
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>"; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
	<language>en-GB</language>
	<atom:link href="<?php echo WEB_ROOT ?>news/rss.php" rel="self" type="application/rss+xml" />
	<managingEditor><?php echo CONTACT_EMAIL;?> (Editor)</managingEditor>
	<title><?php echo SITE_NAME;?> Latest News</title>
	<link><?php echo WEB_ROOT . "/news";?></link>
	<description>Latest news from <?php echo SITE_NAME;?></description>
	<?php echo $items;?>
</channel>
</rss>