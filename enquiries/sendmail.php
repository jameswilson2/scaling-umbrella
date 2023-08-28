<?php
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

    // Send email
    $to = "james@me.aa4.co.uk"; // Replace with your email address
    $subject = "New Contact Form Submission";
    $email_message = "Name: $name\nEmail: $email\nPhone: $phone\nService: $service\nAddress: $address\nMessage: $message";

    // Additional headers
    $headers = "From: $email" . "\r\n";

    // Send email
    if (mail($to, $subject, $email_message, $headers)) {
        echo "Thank you for contacting us. Your message has been sent.";
    } else {
        echo "Sorry, there was an error sending your message.";
    }

    // Include the new captcha question in the HTML form
    include("../contact.php");
}
?>
