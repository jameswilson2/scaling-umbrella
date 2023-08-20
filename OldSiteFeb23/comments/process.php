<?php
require_once 'library/security/_access.inc.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'library/verification/_random_text.inc.php';
require_once 'library/captcha.class.php';

// Spam Eradication
// 1. Applys a new header, just before the message gets sent so it has all of the correct info in
// 2. Checks to see if any < or any html tags have been removed
// 3. Checks for any bad words
// 4. Checks to see if the message is over 3 characters long

//Set variables
$name = "";
$email = "";
$message = "";

// This is the Blacklist of spam words!
$blacklist = array(
	'anal', 'cialis', 'cunt', 'erection', 'drugs', 'gay', 'ejaculation', 'casino', 'casinos', 'naked', 'clit', 'incest', 'penis', 'pharmacy', 'poker', 'porn', 'pussy', 'rape', 'ringtones', 'shemale', 'shit', 'viagra'
);

$errors = "";

if ( !empty( $_POST['formName'] ) ) {
	$name = $_POST['formName'];
	$_SESSION['name'] = $name;
	$name_strip = strip_tags($_POST['formName']);
	if ( $name != $name_strip ) {
		$errors .= "&name=1";
	}
} else {
	$_SESSION['name'] = "";
	$errors .= "&name=2";
}

if ( !empty( $_POST['formEmail'] ) ) {
  $email = $_POST['formEmail'];
  $_SESSION['email'] = $email;
  $emailValid = isValidEmail($email);
  if ($emailValid == 0){
  	$errors .= "&email=1";
  }
  $email_strip = strip_tags($_POST['formEmail']);
	if ( $email != $email_strip ) {
		$errors .= "&email=2";
	}
} else {
	$_SESSION['email'] = "";
	$errors .= "&email=3";
}

if ( !empty( $_POST['formMessage'] ) ) {
  $message = $_POST['formMessage'];
  $_SESSION['message'] = $message;
  $message_strip = strip_tags($_POST['formMessage']);

	// V.2 Spam Detection
	foreach ($blacklist as $word) {
		if (str_contains($message_strip, $word, true)) {
			$errors .= "&mes=1";
			break;
		}
	}

	if ( $message != $message_strip || strlen($message) < 4 ) {
		$errors .= "&mes=1";
	}
} else {
	$_SESSION['message'] = "";
	$errors .= "&mes=2";
}

if(!$_SESSION['captcha_done']){

	$human_test_key = $_POST['human_test_key'];
	$human_test = Captcha::getFromSession($human_test_key);
	$human_test_solution = $_POST['human_test'];

	if(!$human_test || $human_test->getAnswer() != $human_test_solution){
		$errors .= "&human_test=1";
	}
	else{
		$_SESSION['captcha_done'] = true;
	}
}

function str_contains($haystack, $needle, $ignoreCase = false) {
	if ($ignoreCase) {
		$haystack = strtolower($haystack);
		$needle = strtolower($needle);
	}
	// use regex to find if word exists within the message

	if(ereg('[^A-Za-z]?'.$needle.'[^A-Za-z]', $haystack)||$needle == $haystack||ereg('[^A-Za-z]'.$needle.'[^A-Za-z]?', $haystack))
	{
		return (true);
	} else {
		return (false);
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

if ($errors!="") {

	header('location:'.WEB_ROOT.'comments/index.php?error=1'.$errors);
	exit();

} else {

// save to database as pending

$_name = safeAddSlashes($name);
$_email = safeAddSlashes($email);
$_message = safeAddSlashes($message);

$sql = "INSERT INTO tbl_comments SET
		comment_name = '$_name',
		comment_email = '$_email',
		comment_text = '$_message',
		comment_datetime = NOW(),
		comment_status = 'pending'";

$result = @mysql_query($sql);

if (!result){
	exit('Error submitting comment to database: '.mysql_error());
}

	$email_content = "Your have a new comment \r\n";
	$email_content .= "Follow the link to view ".WEB_ROOT."editor/comments/index.php?filter_status=pending\r\n";
	$email_content .= "Name: $name\r\n";
	$email_content .= "Email Address: $email\r\n";
	$email_content .= "\r\n";
	$email_content .= "Comment:\r\n$message\r\n";
	$email_content .= "\r\n";
	$now = date("l jS F Y \a\\t H:i \h\\r\s");
	$email_content .= "Date: $now\r\n";
	$email_content .= "Client IP: {$_SERVER['REMOTE_ADDR']}\r\n";


// inform of new comment

	mail(CONTACT_EMAIL, 'New comment to '.SITE_NAME, $email_content, 'From: '.CONTACT_EMAIL );

// clear session data

	unset($_SESSION['name']);
	unset($_SESSION['email']);
	unset($_SESSION['message']);
	unset($_SESSION['captcha_done']);

	header('location:'.WEB_ROOT.'comments/thankyou.htm');
	exit();
}

?>
