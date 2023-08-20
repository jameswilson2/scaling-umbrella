<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_page.class.php';
require_once 'files/tree.inc.php';

$user_id = $_GET['user_id'];

$sql = "SELECT *
		FROM tbl_user
		WHERE user_id='$user_id'";
$result = getQuery($sql, 'Could not get details from database: ');
if (mysql_num_rows($result)!=1){
	header('location:'.WEB_ROOT.'editor/access/index.php');
	exit();
}

if (isset($_POST['user_web_start'])){

	$user_web_start = $_POST['user_web_start'];
	if(isset($_POST['user_allowed_folders'])){
		$user_allowed_folders = $_POST['user_allowed_folders'];
	} else {
		$user_allowed_folders = array();
	}

	// need to remove trailing , if present
	$user_disallowed_folders = traverseDirTree($user_web_start, '', $user_allowed_folders, null,'getDisallowedFolders');
	$user_disallowed_folders = ereg_replace(',$', '', $user_disallowed_folders);

	if(isset($_POST['user_allowed_files'])){
		$user_allowed_files = $_POST['user_allowed_files'];
	} else {
		$user_allowed_files = array();
	}

	$user_disallowed_files = traverseDirTree($user_web_start, '', $user_allowed_files, 'getDisallowedFiles', null);
	$user_disallowed_files = ereg_replace(',$', '', $user_disallowed_files);


	// save changes

	$sql = "UPDATE tbl_user SET
		user_disallowed_folders = '$user_disallowed_folders',
		user_disallowed_files = '$user_disallowed_files'
		WHERE user_id = '$user_id'";

	$result = getQuery($sql, 'Could not update user: ');

	header('location:'.$_SERVER['PHP_SELF'].'?action=update&user_id='.$user_id);
	exit();

}

$row = mysql_fetch_array($result);

$user_name = htmlspecialchars($row['user_name']);
$user_web_start = htmlspecialchars($row['user_web_start']);
$user_disallowed_folders = htmlspecialchars($row['user_disallowed_folders']);
$user_disallowed_files = htmlspecialchars($row['user_disallowed_files']);

$user_disallowed_folders = explode(',', $user_disallowed_folders);
$user_disallowed_files = explode(',', $user_disallowed_files);


$action = $_GET['action'];

switch ($action){
	case 'update':
		$action_text = "<p>User details updated successfully!</p>";
		break;

	case 'new':
		$action_text = "<p>New user saved successfully!</p>";
		break;
}

$folders = traverseDirTree($user_web_start, '', $user_disallowed_folders, null,'checkBox');
$files = traverseDirTree($user_web_start, '', $user_disallowed_files, 'fileCheckBox', 'folderList');

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>

<h2>Edit User Permissions</h2>

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

<form name="user_edit" action="access/access.php?user_id=<?php echo $user_id; ?>" method="post" id="sck-form">
	<fieldset>
	<legend><strong>Folders:</strong></legend>
		<?php echo $folders; ?>
	</fieldset>
	<fieldset>
	<legend><strong>Files:</strong></legend>
		<?php echo $files; ?>
	</fieldset>
	<p>
		<input type="hidden" name="user_web_start" value="<?php echo $user_web_start; ?>" />
		<input type="submit" value="Save details" />
	</p>
</form>
<?php echo $footer; ?>