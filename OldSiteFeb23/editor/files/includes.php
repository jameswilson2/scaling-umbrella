<?php
require_once 'library/security/_secure.inc.php';
require_once 'files/_include_browser.class.php';
require_once 'library/_page.class.php';

$page = new Page($menus);

$include_browser = new IncludeBrowser();
$content = $include_browser->getContent();

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header;

echo $content;

echo $footer;