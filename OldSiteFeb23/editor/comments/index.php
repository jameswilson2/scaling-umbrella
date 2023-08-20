<?php
require_once 'library/security/_secure.inc.php';
require_once 'comments/_comments_ajax.class.php';
require_once 'library/_page.class.php';



$table = new CommentTable();

$content = $table->getTable();

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>
<h2>Manage Customer Comments</h2>
<?php echo $content; ?>

<?php echo $footer; ?>