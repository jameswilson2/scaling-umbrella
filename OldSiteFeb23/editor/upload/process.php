<?php
require_once 'library/security/_secure.inc.php';
require_once 'upload/_upload.config.php';
require_once 'upload/_upload.inc.php';

$filename = uploadFile('newFile');

if ($filename==""){
	$page = new PageSimple();

	$header = $page->getHeader();
	$footer = $page->getFooter();

	echo $header;

	echo "<p>&nbsp;</p><p>&nbsp;</p>";
	echo "<h3 align=\"center\">Sorry there has been an error!</h3>";
	echo "<p align=\"center\"><a href=\"javascript:history.go(-1);\">Please go back to the previous page an try again!</a>";

	echo $footer;

	exit;
}

header('location:'.WEB_ROOT.'editor/upload/index.php?action=success&filename='.$filename);
exit;
?>