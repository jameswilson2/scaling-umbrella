<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_page.class.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'newsletter/_subscribers.class.php';

$table = new SubscriberTable();

$content = $table->getTable();

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header;
?>
<h2>Browse Newsletter Subscribers</h2>
<?php echo $content; ?>
<?php echo $footer; ?>