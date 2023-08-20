<?php
require_once 'library/security/_access.inc.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'newsletter/_newsletter.inc.php';
require_once 'files/_php_builder.class.php';

if ($_GET['action'] == 'unsubscribe'){
	$content = unsubscribeForm();
	$title =  SITE_NAME.' Newsletter - Unsubscribe';
} else {
	$content = subscribeForm();
	$title =  SITE_NAME.' Newsletter - Subscribe';
}


$page = new PHPBuilder();

$page->setTitle($title);
$page->setDescription($description);
$page->setKeywords($keywords);
$page->setContent($content);

$page_content = $page->buildPage();

echo $page_content;
?>