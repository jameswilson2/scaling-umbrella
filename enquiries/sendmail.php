<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Generate a new captcha question and answer
    $captchaValue1 = rand(1, 10);
    $captchaValue2 = rand(1, 10);
    $captchaAnswer = $captchaValue1 + $captchaValue2;
    $captchaQuestion = "$captchaValue1 + $captchaValue2";

    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $service = $_POST["service"];
    $address = $_POST["address"];
    $message = $_POST["message"];
    $userCaptcha = $_POST["captcha"];

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
    $to = "your@email.com"; // Replace with your email address
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
    include("../contact.html");
}
?>