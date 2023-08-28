<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
include $_SERVER['DOCUMENT_ROOT'] . '/library/phpmailer/PHPMailer.php';
include $_SERVER['DOCUMENT_ROOT'] . '/library/phpmailer/SMTP.php';
include $_SERVER['DOCUMENT_ROOT'] . '/library/phpmailer/Exception.php';


// Include the Captcha class
require_once $_SERVER['DOCUMENT_ROOT'] . '/library/captcha.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $service = $_POST["service"];
    $address = $_POST["address"];
    $message = $_POST["message"];
    $userCaptcha = $_POST["captcha"];
    $captchaAnswer = $_POST["captchaAnswer"];

    // Captcha validation
    if ($userCaptcha != $captchaAnswer) {
        die("Captcha verification failed.");
    }

    // Basic email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Basic phone number validation
    if (!preg_match("/^\d{10}$/", $phone)) {
        die("Invalid phone number.");
    }

    // Prevent HTML code insertion
    $name = htmlspecialchars($name);
    $email = htmlspecialchars($email);
    $phone = htmlspecialchars($phone);
    $service = htmlspecialchars($service);
    $address = htmlspecialchars($address);
    $message = htmlspecialchars($message);

	$emailHost = "mail.kencomp.net";
	$emailFrom = "enquires@kencomp.net";
	$emailFromName = "Web Enquiry";
	$emailTo = "james@me.aa4.co.uk";
	$emailToName = "Kencomp Internet LTD";
	$emailSubject = "New Contact Form Submission";;

	$mail = new PHPMailer;
	$mail->isSMTP(); 
	$mail->SMTPDebug = 2; // 0 = off (for production use) - 1 = client messages - 2 = client and server messages
	$mail->Host = $emailHost; // use $mail->Host = gethostbyname('smtp.gmail.com'); // if your network does not support SMTP over IPv6
	$mail->Port = 25; // TLS only
	$mail->SMTPSecure = false; // ssl is depracated
	$mail->SMTPAuth = false;
	$mail->setFrom($emailFrom, $emailFromName);
	$mail->addAddress($emailTo, $emailToName);
	$mail->Subject = $emailSubject;
	$mail->msgHTML("Name: $name\nEmail: $email\nPhone: $phone\nService: $service\nAddress: $address\nMessage: $message";); //$mail->msgHTML(file_get_contents('contents.html'), __DIR__); //Read an HTML message body from an external file, convert referenced images to embedded,
	$mail->AltBody = 'HTML messaging not supported';


    // Send email
	if(!$mail->send()){
		echo "Mailer Error: " . $mail->ErrorInfo;
	}else{
		echo "Message sent!";
	}
}
?>
