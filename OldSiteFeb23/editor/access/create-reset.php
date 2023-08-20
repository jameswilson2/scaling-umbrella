<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_page.class.php';
require_once 'library/class.phpmailer.php';
require_once 'files/tree.inc.php';
require_once 'library/html/HtmlSelectElement.class.php';

if($_SESSION['user_admin'] != 'Yes'){
	echo "Permission Denied";
	exit;
}

if ($_SERVER['REQUEST_METHOD'] == "POST"){

	$user_id = safeaddslashes($_POST['user_id']);
	$key = safeaddslashes(sha1(time() . mt_rand(0, 2147483647)));
	
	$expiry = 604800;
	$expiry_date_string = strftime("%a %e %b %G %r", time() + $expiry);
	
	$result = getQuery("INSERT INTO tbl_password_reset SET
						password_reset_user_id = '$user_id',
						password_reset_key = '$key',
						password_reset_expiry = DATE_ADD(NOW(), INTERVAL 7 DAY)");
	$reset_id = mysql_insert_id();
	
	$url = WEB_ROOT."editor/access/reset-password.php?id=$reset_id&key=$key";
	
	$action_text = "Created password reset URL";
	
	$result = getQuery("SELECT * FROM tbl_user WHERE user_id = '$user_id'");
	$row = mysql_fetch_array($result);
	$username = $row['user_name'];
	$email = $row['user_email'];
	
	$content = <<<EOD
	
	<table border="0" cellpadding="4" cellspacing="0">
		<tr><td><strong>Name</strong></td><td>$username</td></tr>
		<tr><td><strong>Email Address</strong></td><td>$email</td></tr>
		<tr><td><strong>Reset expiry date</strong></td><td>$expiry_date_string</td></tr>
		<tr><td><strong>URL</strong><td><a href="$url">Password Reset Form</a></td></tr>
	</table>
EOD;

}
else{
	
	$select = new HtmlSelectElement();
	$select->setAttribute("name", "user_id");
	$select->setAttribute("id", "user_id");
	$result = getQuery("SELECT * FROM tbl_user");
	while($row = mysql_fetch_array($result)){
		$select->AddOption($row['user_id'], $row['user_name']);
	}
	$select_user_html = $select->toString();
	
$content = <<<EOD
	<form name="user_edit" action="access/create-reset.php" method="post" id="sck-form">
		<p>
			<label for="use_id">Name</label>
			$select_user_html
		</p>
		<p><input type="submit" value="Create password reset" /></p>
	</form>
EOD;

}

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>

<h2>Create Password Reset URL</h2>

<?php
if ( $action_text ) {
	echo "<div id=\"user-notice\">";
	echo "<p>$action_text</p>";
	echo "</div>";
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
	$("#user-notice").hide();
	if ($("#user-notice").is(":hidden")) {
		$("#user-notice").slideDown("slow");
		$("#user-notice").animate( { backgroundColor: "#DC9E26" }, 600 );
		$("#user-notice").animate( { backgroundColor: "#2683DC" }, 2000 );
	}
});
//]]>
</script>
<?php } ?>
<?php echo $content; ?>
<?php echo $footer; ?>