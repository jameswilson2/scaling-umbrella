<?php
require_once 'library/security/_access.inc.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'faq/_faq_public.class.php';
require_once 'files/_php_builder.class.php';

$faq_list = new FAQList();


$content = $faq_list->getContent();


$title = $faq_list->getTitle();
$description = 'FAQ';
$keywords = '';

$content = <<<EOD
<script src="behaviour/jquery.pack.js" type="text/javascript"></script>
<script src="behaviour/jquery.accordion.pack.js" type="text/javascript"></script>
<script type="text/javascript">
<!--
jQuery().ready(function(){
	jQuery('#jquery-accordion').Accordion({
		showSpeed: 600,
		hideSpeed: 300,
		active: false,
		alwaysOpen: false
	});
});
//-->
</script>

<h1>Frequently Asked Questions</h1>

<p>We have collated a comprehensive list of questions that we are asked. If we have made any omissions and you wish to add to this list, or if you would like to find out more about our services, <a href="enquiries/">contact us</a> for more details.</p>

$content

<script type="text/javascript">
<!--
window.onload = function () {
	if ( document.location.href ) {
		hrefString = document.location.href;
	} else {
		hrefString = document.location;
	}
	if ( document.getElementById("nav") != null )  {
		setActiveMenu(document.getElementById("nav").getElementsByTagName("a"), "");
	}
}
-->
</script>

EOD;

$page = new PHPBuilder();

$page->setTitle($title);
$page->setDescription($description);
$page->setKeywords($keywords);
$page->setContent($content);

$page_content = $page->buildPage();

echo $page_content;


?>