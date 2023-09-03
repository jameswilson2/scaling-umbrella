<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
include $_SERVER['DOCUMENT_ROOT'] . '/library/phpmailer/PHPMailer.php';
include $_SERVER['DOCUMENT_ROOT'] . '/library/phpmailer/SMTP.php';
include $_SERVER['DOCUMENT_ROOT'] . '/library/phpmailer/Exception.php';

// Service routing
$serviceTable = array(
    "Support" => "support@kencomp.net",
    "Sales" => "sales@kencomp.net",
	"Accounts" => "accounts-group@kencomp.net",
	"Other" => "sales@kencomp.net",
);



// Include the Captcha class
require_once $_SERVER['DOCUMENT_ROOT'] . '/library/captcha.php';

function redirectToPage($pageName) {
	// Get the current protocol (http or https)
	$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';

	// Get the current domain and path
	$baseUrl = $protocol . $_SERVER['HTTP_HOST'] . '/';

	// Combine the base URL, desired page, and any additional argument
	$redirectUrl = $baseUrl . $pageName;

	// Redirect the user
	header("Location: $redirectUrl");
	exit; // Make sure to exit after sending the header
}

$pageToRedirect = 'contact.php';

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
		redirectToPage($pageToRedirect . '?e=1');
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
	$emailTo = $serviceTable[htmlspecialchars($service)];
	$emailToName = "Kencomp Internet LTD";
	$emailSubject = "New Web Form Submission";

	$mail = new PHPMailer;
	$mail->isSMTP(); 
	$mail->SMTPDebug = 0; // 0 = off (for production use) - 1 = client messages - 2 = client and server messages
	$mail->Host = $emailHost; // use $mail->Host = gethostbyname('smtp.gmail.com'); // if your network does not support SMTP over IPv6
	$mail->Port = 25; // TLS only
	$mail->SMTPSecure = false; // ssl is depracated
	$mail->SMTPAuth = false;
	$mail->setFrom($emailFrom, $emailFromName);
	$mail->addAddress($emailTo, $emailToName);
	$mail->Subject = $emailSubject;
	$mail->msgHTML("Name: $name <br> Email: $email <br> Phone: $phone <br> Service: $service <br> Address: $address <br> Message: $message");
	$mail->AltBody = 'HTML messaging not supported';


    // Send email
	if(!$mail->send()){
		if (isset($_GET['d'])) {
			$debugCode = $_GET['d'];
			if($debugCode=1){
				echo "Mailer Error: " . $mail->ErrorInfo;
			} else {
				redirectToPage($pageToRedirect . '?e=2');
			}
		}		
	}else{
		redirectToPage($pageToRedirect . '?s=1');
	}
}
?>
