<?php 
require_once 'library/_page.class.php';
require_once 'files/_php_builder.class.php';

function safeAddSlashes($string){
	return mysql_real_escape_string($string);
}

function getQuery($sql, $error_message='Could not get query: '){

	$result = @mysql_query($sql);

	if (!$result) {

		$page = new PageSimple();

		$header = $page->getHeader();
		$footer = $page->getFooter();

		echo $header;

		echo "<p>&nbsp;</p><p>&nbsp;</p>";
		echo "<h1 align=\"center\">Database Error</h1>";
		echo "<p align=\"center\">MySQL Response: ".mysql_error().".</p>";
		echo "<h2>PHP Callstack</h2>";
		echo "<pre style=\"witdh:100%; height:150px; overflow:auto; padding:1em; border:1px inset black;\"><code>";
		debug_print_backtrace();
		echo "</code></pre>";
		
		echo "<p align=\"center\"><a href=\"javascript:history.go(-1);\">Please go back to the previous page and try again!</a>";

		echo $footer;

		exit;
	}

	return $result;
}


function getSimpleQuery($sql, $error_message='Could not get query: '){

	$result = @mysql_query($sql);

	if (!$result) {
		exit($error_message.mysql_error());
	}

	return $result;
}


function getPublicQuery($sql, $error_message='Could not get query: '){

	$result = @mysql_query($sql);

	if (!$result) {

		$content = "<p>&nbsp;</p><p>&nbsp;</p>";
		$content .= "<h3 align=\"center\">Sorry there has been an error!</h3>";
		$content .= "<p align=\"center\"><strong>".$error_message."</strong>".mysql_error().".</p>";
		$content .= "<p align=\"center\"><a href=\"javascript:history.go(-1);\">Please go back to the previous page and try again!</a>";

		$page = new PHPBuilder();

		$page->setTitle($title);
		$page->setDescription($description);
		$page->setKeywords($keywords);
		$page->setContent($content);

		$page_content = $page->buildPage();

		echo $page_content;

		exit;
	}

	return $result;
}
?>
