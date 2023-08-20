<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/class.phpmailer.php';

// Spam Eradication
// 1. Applys a new header, just before the message gets sent so it has all of the correct info in
// 2. Checks to see if any < or any html tags have been removed
// 3. Checks for any bad words
// 4. Checks to see if the message is over 3 characters long

//Set variables
$name = "";
$comname = "";
$email = "";
$telephone = "";
$comments = "";

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

if ( !empty( $_POST['formTele'] ) ) {
	$telephone = $_POST['formTele'];
	$_SESSION['telephone'] = $telephone;
	$telephone_strip = strip_tags($_POST['formTele']);
	if ( $telephone != $telephone_strip ) {
		$errors .= "&tel=1";
	}
} else {
	$_SESSION['telephone'] = "";
	$errors .= "&tel=2";
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



function str_contains($haystack, $needle, $ignoreCase = false) {
	if ($ignoreCase) {
		$haystack = strtolower($haystack);
		$needle = strtolower($needle);
	}
	// use regex to find if word exists within the message

	if(ereg('[^A-Za-z0-9]?'.$needle.'[^A-Za-z0-9]', $haystack)||$needle == $haystack||ereg('[^A-Za-z]'.$needle.'[^A-Za-z]?', $haystack))
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

	header('location:'.WEB_ROOT.'editor/core/contact.php?error=1'.$errors);
	exit();

} else {
// send email


	$mail = new phpmailer();


	$yourEmail = $email;
	$yourName = $name;

	$recipient = 'contact@sck-webworks.co.uk';

	$subject = 'Enquiry from '.SITE_NAME.' admin system';

	$email_content = "Your Name: $name\r\n";
	$email_content .= "Company Name: ".SITE_NAME."\r\n";
	$email_content .= "Email Address: $email\r\n";
	$email_content .= "Telephone Number: $telephone\r\n";
	$email_content .= "\r\n";
	$email_content .= "Message:\r\n$message\r\n";
	$email_content .= "\r\n";
	$now = date("l jS F Y \a\\t H:i \h\\r\s");
	$email_content .= "Date: $now\r\n";
	$email_content .= "Client IP: $_SERVER['REMOTE_ADDR']\r\n";

	$mail->From = $yourEmail;
	$mail->FromName = $yourName;
	$mail->Subject = $subject;

	$mail->Body = $email_content;

	$mail->AddAddress($recipient);

	$mail->Send();


// clear session data

	unset($_SESSION['name']);
	unset($_SESSION['email']);
	unset($_SESSION['telephone']);
	unset($_SESSION['message']);

	header('location:'.WEB_ROOT.'editor/core/thankyou.php');
	exit();
}

?>
