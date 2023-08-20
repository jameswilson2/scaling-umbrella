<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_page.class.php';
require_once 'library/class.phpmailer.php';
require_once 'files/tree.inc.php';

if (isset($_POST['user_name'])){

	$user_name = safeaddslashes($_POST['user_name']);
	$user_email = safeaddslashes($_POST['user_email']);
	$user_password = safeaddslashes($_POST['user_password']);
	$user_admin = safeaddslashes($_POST['user_admin']);
	$user_web_start = safeaddslashes($_POST['user_web_start']);
	$user_allowed_modules = $_POST['user_allowed_modules'];

	$user_allowed_modules = implode(',', $user_allowed_modules);

	if ($user_admin!='Yes'){
		$user_admin='No';
	}


	// check if info_id set - if not then set as new - INSERT  $info_id==-1
	if ($_POST['user_id']==-1){
		if($user_password==''){
			//$user_password = generatePassword(8);
		}
		$sql = "INSERT INTO tbl_user SET
				user_name = '$user_name',
				user_email = '$user_email',
				user_admin = '$user_admin',
				user_web_start = '$user_web_start',
				user_allowed_modules = '$user_allowed_modules',
				user_password=PASSWORD('$user_password')";

		$result = getQuery($sql, 'Could not add new user: ');
		$user_id = mysql_insert_id();

		// send new user email

		$mail = new phpmailer();

		$yourEmail = CONTACT_EMAIL;
		$yourName = SITE_NAME;

		$subject = "Editor access to ".SITE_NAME;

		$mail->From = $yourEmail;
		$mail->FromName = $yourName;
		$mail->Subject = $subject;

		$message = '';
		$message .= "Hi $user_name\n";
		$message .= "\n";
		$message .= "This is an automatically generated email - please do not reply.\n";
		$message .= "An editor account has been created for you at ".SITE_NAME.".\n";
		$message .= "Your username is: ".$user_email."\n";
		$message .= "Your password is: ".$user_password."\n";
		$message .= "\n";

		$mail->Body = $message;
		$mail->AddAddress($user_email, $user_name);

		$mail->Send();

		header('location:'.$_SERVER['PHP_SELF'].'?action=new&user_id='.$user_id);
		exit();

	} else {
		$user_id = $_POST['user_id'];

		if($user_password!=''){
			$pass_sql = ", user_password=PASSWORD('$user_password')";
		} else {
			$pass_sql = '';
		}

		$old_web_start = $_POST['old_web_start'];

		if($old_web_start!=$user_web_start){
			$pass_sql .= ", user_disallowed_folders='', user_disallowed_files=''";
		}

		$sql = "UPDATE tbl_user SET
				user_name = '$user_name',
				user_email = '$user_email',
				user_admin = '$user_admin',
				user_web_start = '$user_web_start',
				user_allowed_modules = '$user_allowed_modules'".$pass_sql."
				WHERE user_id = '$user_id'";

		$result = getQuery($sql, 'Could not update user: ');

		header('location:'.$_SERVER['PHP_SELF'].'?action=update&user_id='.$user_id);
		exit();
	}
}


if (isset($_GET['new'])){

	// initialise zero variables

	$user_id = "-1";
	$user_name = "";
	$user_email = "";
	$user_password = "";
	$user_admin = "";
	$user_web_start = "";
	$user_allowed_modules = $menus;


} else {
	$user_id = $_GET['user_id'];

	$sql = "SELECT *
			FROM tbl_user
			WHERE user_id='$user_id'";
	$result = getQuery($sql, 'Could not get detail from database: ');
	if (mysql_num_rows($result)!=1){
		header('location:'.WEB_ROOT.'editor/access/index.php');
		exit();
	}
	$row = mysql_fetch_array($result);

	$user_name = htmlspecialchars($row['user_name']);
	$user_email = htmlspecialchars($row['user_email']);
	$user_admin = $row['user_admin'];
	$user_web_start = htmlspecialchars($row['user_web_start']);
	$user_allowed_modules = htmlspecialchars($row['user_allowed_modules']);
	$user_allowed_modules = explode(',', $user_allowed_modules);
}

$action = $_GET['action'];

switch ($action){
	case 'update':
		$action_text = "<p>User details updated successfully!</p>";
		break;

	case 'new':
		$action_text = "<p>New user saved successfully!</p>";
		break;
}

$tree = traverseDirTree('', '', $user_web_start, null,'selectBox');

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>

<h2>Edit User Details</h2>

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

<form name="user_edit" action="access/edit.php" method="post" id="sck-form">
	<p>
	<label for="user_name">Name:</label>
	<input type="text" id="user_name" name="user_name" value="<?php echo $user_name; ?>" class="field" />
	</p>
	<p>
	<label for="user_email">Email:</label>
	<input type="text" id="user_email" name="user_email" value="<?php echo $user_email; ?>" class="field" />
	</p>
	<div class="sck-note-p">Leave blank if unchanged or to automatically create</div>
	<p>
	<label for="user_password">Password:</label>
	<input type="text" id="user_password" name="user_password" value="<?php echo $user_password; ?>" class="field" />
	</p>
	<p>
	<label for="user_web_start">Editor Start</label>
	<select name="user_web_start">
	<?php if($user_web_start==''): ?>
		<option value="" selected="selected">Root</option>
	<?php else: ?>
		<option value="">Root</option>
	<?php endif; ?>
	<?php echo $tree; ?>
	</select>
	</p>
	<p>
		<label for="user_admin">Admin:</label>
		<?php if ($user_admin=='Yes'): ?>
			<span class="other"><input type="checkbox" name="user_admin" value="Yes" checked="checked" /></span>
		<?php else: ?>
			<span class="other"><input type="checkbox" name="user_admin" value="Yes" /></span>
		<?php endif; ?>
	</p>
	<fieldset>
	<legend><strong>Editor Modules</strong></legend>
	<?php
		foreach($menus as $item){
			if(in_array($item, $user_allowed_modules)){
				echo "<p><label>$item</label><span class=\"other\"><input type=\"checkbox\" value=\"$item\" checked=\"checked\" name=\"user_allowed_modules[]\" /></span></p>";
			} else {
				echo "<p><label>$item</label><span class=\"other\"><input type=\"checkbox\" value=\"$item\" name=\"user_allowed_modules[]\" /></span></p>";
			}
		}
	?>
	</fieldset>
	<p>
		<input type="submit" value="Save details" />
		<input type="hidden" id="old_web_start" name="old_web_start" value="<?php echo $user_web_start; ?>" />
		<input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id; ?>" />
	</p>
</form>
<?php echo $footer; ?>