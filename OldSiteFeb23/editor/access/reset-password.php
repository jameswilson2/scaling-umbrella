<?php
require_once 'library/security/_access.inc.php';
require_once 'library/_page.class.php';

$id = safeaddslashes($_GET['id']);
$key = safeaddslashes($_GET['key']);

$form_action = $_SERVER['PHP_SELF'] . "?id=$id&key=$key";
$form_errors = array();
$form_completed = false;

$sql = "SELECT * 
		FROM tbl_password_reset
		LEFT JOIN tbl_user ON password_reset_user_id = user_id
		WHERE password_reset_id = '$id' AND password_reset_expiry > NOW()";
$result = getQuery($sql);
if(!$result){
	echo "Your password reset request has expired!";
	exit;
}

$row = mysql_fetch_array($result);
if(!$row){
	echo "Your password reset request has expired!";
	exit;
}

$user_id = $row['user_id'];
$username = $row['user_name'];

if($key != $row['password_reset_key']){
	echo "Invalid key";
	exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	
	$password = safeaddslashes($_POST['password']);
	$confirm_password = safeaddslashes($_POST['confirm_password']);
	
	if($password == $confirm_password){
		
		$sql = "UPDATE tbl_user SET user_password = PASSWORD('$password') WHERE user_id = '$user_id'";
		$result = getQuery($sql);
		
		$sql = "DELETE FROM tbl_password_reset WHERE password_reset_id = '$id'";
		$result = getQuery($sql);
		
		$form_completed = true;
		
		header("location: ". WEB_ROOT.'/editor/index.php');
	}
	else{
		$form_errors['password'] = "Your new password and confirm new password do not match";
	}
}

function displayFormError($inputName){
	global $form_errors;
	if(isset($form_errors[$inputName])){
		$message = $form_errors[$inputName];
		echo <<<EOD
			<div class="sck-error-p">$message</div>
EOD;
	}
}

$page = new Page();

$page->setTitle('Reset Password');
$page->showSidebar(false);
$header = $page->getHeader();
$footer = $page->getFooter();

echo $header;
?>
	
		<h1>Reset Login Password</h1>

		
		<?php if(!$form_completed):?>
		<form method="post" action="<?php echo $form_action;?>" id="sck-form">
		
			<p>
				<label for="username">Name:</label>
				<input type="text" disabled="disabled" id="username" value="<?php echo $username; ?>" class="field"/>
			</p>
		
			<?php displayFormError("password"); ?>
			<p>
				<label for="password">New Password:</label>
				<input type="password" id="password" name="password" class="field" />
			</p>
			
			<p>
				<label for="confirm_password">Confirm New Password:</label>
				<input type="password" id="confirm_password" name="confirm_password" class="field" />
			</p>
			
			<p><input type="submit" value="Reset Password" /></p>
		</form>
		<?php else:?>
		<?php endif;?>

<?php echo $footer; ?>
