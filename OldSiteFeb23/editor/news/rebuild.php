<?php
require_once 'library/security/_secure.inc.php';
require_once 'news/_news_config.inc.php';
require_once 'library/_format.inc.php';
require_once 'news/_links.inc.php';

// get news items

$sql = "SELECT news_id, news_title, news_summary, news_date
		FROM tbl_news
		WHERE news_status='Active'
		ORDER BY news_date DESC
		LIMIT 2";

$articles = getQuery($sql, 'Could not get news item: ');

$contents = array();

while ($article = mysql_fetch_array($articles)){
	$news_id = $article['news_id'];
	$news_title = htmlspecialchars($article['news_title']);
	$news_summary = encodetext($article['news_summary']);
	$news_date = htmlspecialchars($article['news_date']);
	$news_date = date('l jS \of F Y' ,strtotime($news_date));

	$link = buildNewsLink($news_id, $news_title);

	$contents[] = "<p><strong>$news_title</strong><br />
				$news_summary<br />
				$news_date - <a href=\"$link\">read more</a></p>";
}

$content = <<<EOD
<div class="article-left">
	{$contents[0]}
</div>
<div class="article-right">
	{$contents[1]}
</div>
<div class="clear"></div>
EOD;

$filename = NEWS_INCLUDE;
$backup = NEWS_INCLUDE.'.BCK';
$tempfilename = "tempfile.htm";

// delete temporary file
@unlink($tempfilename);

$tempfile = fopen($tempfilename, 'w');
if (!$tempfile) {
	exit("<p>Unable to open temporary file for writing!</p>");
}
fwrite($tempfile, $content);
fclose($tempfile);

// delete old backup
@unlink ($backup);

// copy old file to backup
$ok = copy($filename, $backup);

// copy temporary file to correct location
$ok = copy($tempfilename, $filename);

// delete temporary file
@unlink($tempfilename);


// redirect to news home
header('location:'.WEB_ROOT.'editor/news/?action=rebuild');
exit();

?>