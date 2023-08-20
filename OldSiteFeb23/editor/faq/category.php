<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'library/_page.class.php';

$sql = "SELECT category_id, category_name
		FROM tbl_faq_category ORDER BY category_name ASC";

$cats = getQuery($sql);

$action = $_GET['action'];

switch ($action){
	case 'addsuccess':
		$action_text = "<p>Category added successfully!</p>";
		break;

	case 'delsuccess':
		$action_text = "<p>Category deleted successfully!</p>";
		break;

	case 'renameman':
		$action_text = "<p>Category renamed successfully!</p>";
		break;

	case 'renamemanexists':
		$action_text = "<p>Cannot rename category - already exists!</p>";
		break;

}

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>

<h2>Manage FAQ Categories</h2>

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

	<p><img src="presentation/icon_plus.gif" alt="Add" width="16" height="15" title="Add" class="minicon" /> <strong><a href="javascript:;" onclick="expand('newbox')">Add New Categories</a></strong></p>

	<div id="newbox" class="hide">
		<div class="newitem">
			<form action="faq/category_add.php" method="post">
			<label for="cats"><strong>Add categories:</strong> (Separate items with commas)</label><br />
			<textarea name="cats" id="cats" rows="4" cols="40"></textarea><br />
			<input type="submit" value="Add Categories" onclick="this.form.submit();this.disabled=true;this.value='Adding';" />
			</form>
		</div>
	</div>

</div>

<ul class="filelist">
<?php
	while($cat = mysql_fetch_array($cats)){
		$category_id = $cat['category_id'];
		$category_name = $cat['category_name'];
?>
	<li><a href="javascript:void(0);" onclick="expand('Expand<?php echo $category_id; ?>')" class="actionimg">Rename this Category</a><strong><?php echo $category_name; ?></strong>
	<div id="Expand<?php echo $category_id; ?>" class="hide">
		<div class="newitem">
			<form action="faq/category_edit.php?category_id=<?php echo $category_id; ?>" method="post"><p><strong>New category name:</strong> <input type="text" name="NewTypeName<?php echo $category_id; ?>" id="NewTypeName<?php echo $category_id; ?>" value="" /><input type="submit" id="RenameType<?php echo $category_id; ?>" name="RenameType<?php echo $category_id; ?>" value="Rename Category" onclick="this.form.submit();this.disabled=true;this.value='Renaming';" /></p></form>
		</div>
	</div>
	</li>
<?php	} ?>
</ul>

<?php echo $footer; ?>