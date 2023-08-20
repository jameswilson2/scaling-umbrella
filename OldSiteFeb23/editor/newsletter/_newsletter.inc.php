<?php
function subscribeForm(){
	if (isset($_POST['sub_email'])){
		$email = $_POST['sub_email'];
		$email_strip = strip_tags($_POST['sub_email']);
		if (isValidEmail($email) && $email == $email_strip){
			subscribeEmail($email);
			header('location:'.WEB_ROOT.'newsletter/subscribe/?result=success');
			exit();
		} else {
			header('location:'.WEB_ROOT.'newsletter/subscribe/?result=fail');
			exit();
		}
	} else {
		if ($_GET['result']=='success'){
			$sender_email = NEWSLETTER_EMAIL;
			$content = <<<EOD
		<h2>Newsletter Signup</h2>
		<p>Signup successful!</p>
		<p>Please add $sender_email to your address book to avoid your newsletter being flagged as spam.</p>
EOD;
		} else {
			if ($_GET['result']=='fail'){
				$error="<div class=\"sck-error-p\">Please enter a valid email address.</div>";
			} else {
				$error="";
			}
			$content = <<<EOD
		<h2>Newsletter Signup</h2>
		<form id="sck-form" method="post" action="newsletter/subscribe/">
		$error
		<p>
			<label for="sub_email">Email address:</label>
			<input type="text" name="sub_email" id="sub_email" class="field" />
		</p>
		<p><input name="SignUpNow" type="submit" id="SendEmail" value="Signup now!" />
		<input name="ClearForm" type="reset" id="ClearForm" value="Clear Form" /></p>
		</form>
EOD;
		}

		return $content;
	}
}


function unsubscribeForm(){
	if (isset($_REQUEST['sub_email'])){
		$email = $_REQUEST['sub_email'];
		$email_strip = strip_tags($_REQUEST['sub_email']);
		if (isValidEmail($email) && $email == $email_strip){
			$email = safeAddSlashes($email);
			$sql = "SELECT member_email
					FROM tbl_member
					WHERE member_email='$email'";
			$result = mysql_query($sql);
			if (!result){
				exit('Could not get email addresses from database: '.mysql_error());
			}
			if (mysql_num_rows($result)==0){
				header('location:'.WEB_ROOT.'newsletter/unsubscribe/?result=failnoemail');
				exit();
			} else {
				$sql = "DELETE FROM tbl_member
						WHERE member_email='$email'";
				$result = mysql_query($sql);
				header('location:'.WEB_ROOT.'newsletter/unsubscribe/?result=success');
				exit();
			}
		} else {
			header('location:'.WEB_ROOT.'newsletter/unsubscribe/?result=fail');
			exit();
		}
	} else {
		if ($_GET['result']=='success'){
			$content = <<<EOD
		<h2>Unsubscribe from Newsletter</h2>
		<p>Your email address has been successfully removed for the mailing list</p>
EOD;
		} else {
			if ($_GET['result']=='fail'){
				$error="<div class=\"sck-error-p\">Please enter a valid email address.</div>";
			} elseif ($_GET['result']=='failnoemail'){
				$error="<div class=\"sck-error-p\">The email address you specified has not been found.  Please check the formatting and capitalisation carefully and resubmit.</div>";
			} else {
				$error="";
			}
			$content = <<<EOD
		<h2>Unsubscribe from Newsletter</h2>
		<form id="sck-form" method="post" action="newsletter/unsubscribe/">
		$error
		<p>
			<label for="sub_email">Email address:</label>
			<input type="text" name="sub_email" id="sub_email" class="field" />
		</p>
		<p><input name="SignUpNow" type="submit" id="SendEmail" value="Unsubscribe" />
		<input name="ClearForm" type="reset" id="ClearForm" value="Clear Form" /></p>
		</form>
EOD;
		}

		return $content;
	}
}


function subscribeEmail($email){
	$sql = "SELECT member_email FROM tbl_member WHERE member_email='$email'";
	$result = mysql_query($sql);
	if (!$result){
		exit('Could not fetch member list from database: '.mysql_error());
	}
	if (mysql_num_rows($result)==0){
		$email = safeAddSlashes($email);
		$sql = "INSERT INTO tbl_member
				SET member_email='$email',
				member_status='active'";
		$result = mysql_query($sql);
		if (!$result){
			exit('Could not add subscriber: '.mysql_error());
		}
		return TRUE;
	} else {
		return FALSE;
	}
}


function isValidEmail( $email = null ) {
	// A positive match will yield a result of 1
	return preg_match( "/^
	[\d\w\/+!=#|$?%{^&}*`'~-]
	[\d\w\/\.+!=#|$?%{^&}*`'~-]*@
	[A-Z0-9]
	[A-Z0-9.-]{1,61}
	[A-Z0-9]\.
	[A-Z]{2,6}$/ix", $email );
}

?>