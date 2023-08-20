<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'library/_page.class.php';

if (isset($_POST['faq_question'])){

	$faq_question = safeaddslashes($_POST['faq_question']);
	$faq_answer = safeaddslashes($_POST['faq_answer']);
	$faq_category_id = safeaddslashes($_POST['faq_category_id']);
	$faq_status = safeaddslashes($_POST['faq_status']);
	if ($faq_status!='active'){
		$faq_status='inactive';
	}

	// check if info_id set - if not then set as new - INSERT  $info_id==-1
	if ($_POST['faq_id']==-1){
		$sql = "INSERT INTO tbl_faq SET
				faq_category_id = '$faq_category_id',
				faq_question = '$faq_question',
				faq_answer = '$faq_answer',
				faq_status = '$faq_status'";

		$result = getQuery($sql, 'Could not add new FAQ: ');
		$faq_id = mysql_insert_id();

		header('location:'.$_SERVER['PHP_SELF'].'?action=new&faq_id='.$faq_id);
		exit();

	} else {
		$faq_id = $_POST['faq_id'];
		$sql = "UPDATE tbl_faq SET
				faq_category_id = '$faq_category_id',
				faq_question = '$faq_question',
				faq_answer = '$faq_answer',
				faq_status = '$faq_status'
				WHERE faq_id = '$faq_id'";

		$result = getQuery($sql, 'Could not update FAQ: ');

		header('location:'.$_SERVER['PHP_SELF'].'?action=update&faq_id='.$faq_id);
		exit();
	}
}


if (isset($_GET['new'])){

	// initialise zero variables

	$faq_id = "-1";
	$faq_category_id = "";
	$faq_question = "";
	$faq_answer = "";
	$faq_status = "inactivefaq_id";


} else {
	$faq_id = $_GET['faq_id'];

	$sql = "SELECT *
			FROM tbl_faq
			WHERE faq_id='$faq_id'";
	$result = getQuery($sql, 'Could not get FAQ detail from database: ');
	if (mysql_num_rows($result)!=1){
		header('location:faq.php');
		exit();
	}
	$row = mysql_fetch_array($result);

	$faq_category_id = $row['faq_category_id'];
	$faq_question = htmlspecialchars($row['faq_question']);
	$faq_answer = htmlspecialchars($row['faq_answer']);
	$faq_status = $row['faq_status'];

}

$action = $_GET['action'];

switch ($action){
	case 'update':
		$action_text = "<p>FAQ details updated successfully!</p>";
		break;

	case 'new':
		$action_text = "<p>New FAQ saved successfully!</p>";
		break;
}

$sql = "SELECT category_id, category_name FROM tbl_faq_category ORDER BY category_name ASC";
$cats = getQuery($sql, 'Could not get categories from database: ');

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>

<h2>Edit FAQ Details</h2>

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

<form name="faq_edit" action="faq/edit.php" method="post" id="sck-form">
	<p>
	<label for="faq_category_id">Category:</label>
	<select name="faq_category_id">
	<option>Please Select</option>
	<?php
		while ($cat = mysql_fetch_array($cats)){
			$_category_id = $cat['category_id'];
			$_category_name = htmlspecialchars($cat['category_name']);
			if ($_category_id==$faq_category_id){
				echo "<option value='$_category_id' selected='selected'>$_category_name</option>";
			} else {
				echo "<option value='$_category_id'>$_category_name</option>";
			}
		}
	?>
	</select>
	</p>
	<p>
	<label for="faq_question">Question:</label>
	<input type="text" id="faq_question" name="faq_question" value="<?php echo $faq_question; ?>" class="field" />
	</p>
	<p>
	<label for="faq_answer">Answer:</label>
	<textarea name="faq_answer" id="faq_answer" rows="10" cols="25"><?php echo $faq_answer; ?></textarea>
	</p>
	<p>
		<label for="faq_status">Active:</label>
		<?php if ($faq_status=='active'): ?>
			<span class="other"><input type="checkbox" name="faq_status" value="active" checked="checked" /></span>
		<?php else: ?>
			<span class="other"><input type="checkbox" name="faq_status" value="active" /></span>
		<?php endif; ?>
	</p>
	<p>
		<input type="submit" value="Save details" />
		<input type="hidden" id="faq_id" name="faq_id" value="<?php echo $faq_id; ?>" />
	</p>
</form>
<?php echo $footer; ?>