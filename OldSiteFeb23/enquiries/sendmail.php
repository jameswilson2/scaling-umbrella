<?php
require_once 'library/security/_access.inc.php';
require_once 'library/captcha.class.php';
require_once 'library/class.phpmailer.php';

// Spam Eradication
// 1. Applys a new header, just before the message gets sent so it has all of the correct info in
// 2. Checks to see if any < or any html tags have been removed
// 3. Checks for any bad words
// 4. Checks to see if the message is over 3 characters long

//Set variables
$name = "";
$email = "";
$telephone = "";
$comments = "";
$service = "";
$address = "";
$postcode = "";

$errors = "";

if ( !empty( $_POST['formName'] ) ) {
	$name = $_POST['formName'];
	$_SESSION['name'] = $name;
} else {
	$_SESSION['name'] = "";
	$errors .= "&name=2";
}

if ( !empty( $_POST['formEmail'] ) ) {
  $email = $_POST['formEmail'];
  $_SESSION['email'] = $email;
  $emailValid = isValidEmailAddress($email);
  if ($emailValid == 0){
  	$errors .= "&email=1";
  }
} else {
	$_SESSION['email'] = "";
	$errors .= "&email=3";
}

if ( !empty( $_POST['formTele'] ) ) {
	$telephone = $_POST['formTele'];
	$_SESSION['telephone'] = $telephone;
} else {
	$_SESSION['telephone'] = "";
}

$service = $_POST['formService'];
$_SESSION['servicerequired'] = $service;

$address = $_POST['formAddress'];
$_SESSION['address'] = $address;

$postcode = $_POST['formPostcode'];
$_SESSION['postcode'] = $postcode;

if ( !empty( $_POST['formMessage'] ) ) {
	$message = $_POST['formMessage'];
	$_SESSION['message'] = $message;
	
	if ( strlen($message) < 4 ) {
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

if ($errors!="") {

	header('location:'.WEB_ROOT.'enquiries/index.php?error=1'.$errors);
	exit();

} else {

	// Send email
	$email_content = "Your Name: $name\r\n";
	$email_content .= "Email Address: $email\r\n";
	$email_content .= "Telephone Number: $telephone\r\n";
	$email_content .= "Address: $address\r\n";
	$email_content .= "Postcode: $postcode\r\n";
	$email_content .= "Service Required: $service\r\n";
	$email_content .= "\r\n";
	$email_content .= "Message:\r\n$message\r\n";
	$email_content .= "\r\n";
	$now = date("l jS F Y \a\\t H:i \h\\r\s");
	$email_content .= "Date: $now\r\n";
	$email_content .= "Client IP: {$_SERVER['REMOTE_ADDR']}\r\n";

	$subject = 'Enquiry from '.SITE_NAME;

	$mail = new phpmailer();

	$mail->IsSMTP();
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->Host       = SMTP_HOSTNAME; 			// sets the SMTP server
	$mail->Port       = SMTP_PORT;                    // set the SMTP port for the GMAIL server
	$mail->Username   = SMTP_USERNAME; 			// SMTP account username
	$mail->Password   = SMTP_PASSWORD;        // SMTP account password

	$mail->SetFrom(CONTACT_EMAIL, SITE_NAME);
	//$mail->AddReplyTo(REPLY_EMAIL, SITE_NAME);
	$mail->AddReplyTo($email, SITE_NAME);

	$mail->Subject = $subject;

	$mail->Body = $email_content;

	$mail->AddAddress(CONTACT_EMAIL);

	$mail->Send();


	$name = safeAddSlashes($name);
	$email = safeAddSlashes($email);
	$telephone = safeAddSlashes($telephone);
	$service = safeAddSlashes($service);
	$address = safeAddSlashes($address);
	$postcode = safeAddSlashes($postcode);
	$message = safeAddSlashes($message);


	$sql = "INSERT INTO tbl_contact SET
			contact_name='$name',
			contact_email='$email',
			contact_phone='$telephone',
			contact_message='$message',
			contact_date=NOW(),
			contact_status='New',
			contact_service='$service',
			contact_postcode='$postcode',
			contact_address='$address'";

	$result = @mysql_query($sql);

	if (!$result){
		exit('Could not save contact data: '.mysql_error());
	}

	// Clear session data
	unset($_SESSION['name']);
	unset($_SESSION['email']);
	unset($_SESSION['telephone']);
	unset($_SESSION['service']);
	unset($_SESSION['address']);
	unset($_SESSION['postcode']);
	unset($_SESSION['message']);
	unset($_SESSION['captcha_done']);
	
	header('location:'.WEB_ROOT.'enquiries/thankyou.htm');
	exit();
}

?>
