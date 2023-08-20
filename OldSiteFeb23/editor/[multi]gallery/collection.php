<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'gallery/_gallery_config.inc.php';
require_once 'gallery/_collection.class.php';
require_once 'library/_page.class.php';

$table = new CollectionTable();

$content = $table->getTable();

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>
<h2>Manage Photo Collection</h2>
<div id="newitems">

	<p><img src="presentation/icon_plus.gif" alt="Add" width="16" height="15" title="Add" class="minicon" /> <strong><a href="gallery/collection_edit.php?new=1">Create a new collection item</a></strong></p>

</div>

<?php echo $content; ?>
<?php echo $footer; ?>