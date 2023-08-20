<?php require_once 'library/security/_secure.inc.php';
require_once 'coverage/_coverage.class.php';
require_once 'library/_page.class.php';

$table = new CoverageTable();

$content = $table->getTable();

$action = $_GET['action'];

switch ($action){
	case 'itemcreated':
		$action_text = "<p>New Coverage Area Added</p>";
		break;
	case 'itemedited':
		$action_text = "<p>Coverage Area Saved</p>";
		break;
}

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>

<?php
if ( $action_text ) {
	echo "<div id=\"user-notice\">";
	echo $action_text;
	echo "</div>";
}
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

<h2>Manage Coverage Areas</h2>
<p><img src="presentation/icon_plus.gif" alt="Add" width="16" height="15" title="Add" class="minicon" /> <strong><a href="coverage/edit.php?new=1">Create a new coverage area</a></strong></p>

<?php echo $content; ?>

<?php echo $footer; ?>