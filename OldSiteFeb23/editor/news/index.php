<?php
require_once 'library/security/_secure.inc.php';
require_once 'news/_news.class.php';
require_once 'library/_page.class.php';

$table = new NewsTable();

$content = $table->getTable();

$action = $_GET['action'];

switch ($action){
	case 'rebuild':
		$action_text = "<p>Homepage rebuilt successfully!</p>";
		break;
}

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>
<h2>News</h2>
<?php
if ( $action_text ) {
	echo "<div id=\"user-notice\">";
	echo $action_text;
	echo "</div>";
?>
<script type="text/javascript">
<!--
$(document).ready(function() {
	$("#user-notice").hide();
	if ($("#user-notice").is(":hidden")) {
		$("#user-notice").slideDown("slow");
		$("#user-notice").animate( { backgroundColor: "#DC9E26" }, 600 );
		$("#user-notice").animate( { backgroundColor: "#2683DC" }, 2000 );
	}
});
//-->
</script>
<?php } ?>
<div id="newitems">

	<p><img src="presentation/icon_plus.gif" alt="Add" width="16" height="15" title="Add" class="minicon" /> <strong><a href="news/edit.php?new=1">Create a new news item</a></strong></p>

</div>

<?php echo $content; ?>
<?php echo $footer; ?>