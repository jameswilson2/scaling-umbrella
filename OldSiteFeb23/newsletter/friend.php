<?php
require_once 'library/security/_access.inc.php';
require_once 'library/class.phpmailer.php';

$email = $_POST['email'];
$name = $_POST['name'];

$email_content = "Hi $email\n";
$email_content .= "$name has recommended this website.  Visit ".WEB_ROOT."\n";
$email_content .= "Thank you, ".SITE_NAME;

$headers = "From: ".SITE_NAME." <".CONTACT_EMAIL.">";

mail($email, 'Recommendation from '.$name, $email_content, $headers );

$email = htmlspecialchars($email);

$content = <<<EOD
<p>Message sent successfully to $email</p>

EOD;

require '../_header.inc.php';

echo $content;

require '../_footer.inc.php'; ?>