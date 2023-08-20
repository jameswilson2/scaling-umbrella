<?php
require_once 'library/security/_secure.inc.php';
require_once 'files/_file_browser.class.php';
require_once 'library/_page.class.php';

$page = new Page($menus);

$file_browser = new FileBrowser();
$content = $file_browser->getContent();

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header;

echo $content;

echo $footer;