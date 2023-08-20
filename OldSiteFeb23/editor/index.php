<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_page.class.php';

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header;

echo "<h2>".SITE_NAME." - Web Site Management</h2>";

echo "<div class=\"article-home\">";
echo "<p><strong>Is there more we can do for you?</strong><br />
Along with this editor we can provide you with a fully built custom designed shop tailored to your business requirements, but it doesn't end there we can create systems for you to improve the way you work, reducing the time you need to spend administrating, saving you money.</p>";
echo "<div class=\"list\"><ul><li><a href=\"contact.php\">Contact SCK Web Works</a></li></ul></div>";
echo "<p><strong>Statistics for your Web Site:</strong><br />
Find out how many visitors are looking at your site and to find out how they got there, using what keywords / key phrases.</p>";
echo "<div class=\"list\"><ul><li><a href=\"core/stats.php\" target=\"_blank\">View your Web Site Statistics</a></li></ul></div>";
echo "</div>";

echo "<div class=\"list\">";

if($_SESSION['user_admin']=='Yes'){
	$items = array('access');
	$items = array_merge($items, $menus);
} else {
	$items = $_SESSION['user_allowed_modules'];
}
foreach ($items as $item){
	if (file_exists($item.'/homepage.inc.php')){
		require_once $item.'/homepage.inc.php';
	}
}
echo "</div>";

echo $footer; ?>

