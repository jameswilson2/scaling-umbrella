<?php require_once 'library/security/_secure.inc.php';
require_once 'enquiries/_enquiries.class.php';
require_once 'library/_page.class.php';

$table = new EnquiryTable();

$content = $table->getTable();

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>
<h2>Manage Customer Enquiries</h2>
<?php echo $content; ?>

<?php echo $footer; ?>