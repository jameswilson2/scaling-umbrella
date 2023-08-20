<?php require_once 'library/security/_secure.inc.php';
require_once 'access/_users.class.php';
require_once 'library/_page.class.php';

$table = new UserTable();

$content = $table->getTable();

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>
<h2>Users and Access</h2>
<div id="newitems">

	<p><img src="presentation/icon_plus.gif" alt="Add" width="16" height="15" title="Add" class="minicon" /> <strong><a href="access/edit.php?new=1">Add new user</a></strong></p>

</div>
<?php echo $content; ?>

<?php echo $footer; ?>