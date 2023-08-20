<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'gallery/_gallery_config.inc.php';
require_once 'gallery/_gallery.class.php';
require_once 'library/_page.class.php';

$table = new GalleryTable();

$content = $table->getTable();

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>
<h2>Manage Gallery</h2>
<div id="newitems">

	<p><img src="presentation/icon_plus.gif" alt="Add" width="16" height="15" title="Add" class="minicon" /> <strong><a href="gallery/edit.php?new=1">Create a new gallery item</a></strong></p>

</div>

<?php echo $content; ?>
<?php echo $footer; ?>