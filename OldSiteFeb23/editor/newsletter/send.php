<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_page.class.php';
require_once 'library/class.phpmailer.php';
require_once 'library/_extras_dbconn.inc.php';

echo "Do not refresh the page! Sending emails now, please wait...\n<br/>";
flush();
ob_flush();

if (!isset($_GET['nid'])){
	exit('newsletter not found');
}
$nid = $_GET['nid'];

$test = $_GET['test'];

set_time_limit(3600);

// Instantiate it
$mail = new phpmailer();

// Modify this
$yourEmail = NEWSLETTER_EMAIL;
$yourName = SITE_NAME;


$sql = "SELECT member_email FROM tbl_member WHERE member_status='active'";
$recipients = @mysql_query($sql);
if (!$recipients){
	exit('Unable to get memberlist from database: '.mysql_error());
}

$sql2 = "SELECT newsletter_title, newsletter_content FROM tbl_newsletter WHERE newsletter_id = '$nid'";
$newsletters = @mysql_query($sql2);
if (!$newsletters){
	exit('Unable to get newsletter from database: '.mysql_error());
}

$newsletter = mysql_fetch_array($newsletters);
$subject = $newsletter['newsletter_title'];
$content = $newsletter['newsletter_content'];

// Define who the message is from
$mail->From = $yourEmail;
$mail->FromName = $yourName;

// Set the subject of the message
$mail->Subject = $subject;

// Add the HTML body of the message
ob_start();
include("./_header_email.php");
$header = ob_get_contents();
ob_end_clean();

ob_start();
include("./_footer_email.php");
$footer = ob_get_contents();
ob_end_clean();

$html = $header;
$html .= $content;
$html .= $footer;
$txt = ereg_replace("</?h[1-6]>|</?p>|<br />|</?ul>|</li>","\r\n",$html);
$txt = strip_tags($txt);

$i=0;

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header;
?>
<h2>Sending Emails</h2>
<?php

if ($test!='yes'){
	while ($recipient = mysql_fetch_array($recipients)){
		$i++;

		$email = $recipient['member_email'];

		$html_final = ereg_replace('##EMAIL##', $email, $html);
		$html_final = ereg_replace('##WEB ROOT##', WEB_ROOT, $html_final);

		// Add message to body
		$mail->Body = $html_final;

		$txt_final = ereg_replace('##EMAIL##', $email, $txt);
		$txt_final = ereg_replace('##WEB ROOT##', WEB_ROOT, $txt_final);

		$mail->AltBody = $txt_final;

		// Add a recipient address
		$mail->AddAddress($email, $name);

		// Send the message
		if(!$mail->Send()){
			echo ('Mail sending failed to '. $email .'<br />');
		} else {
			echo ('Mail sent successfully '. $email .'<br />');
		}
		$mail->clearAddresses();

		if ($i>=10){
			$i = 0;
			sleep(10);
		}
	}

	echo '<p>Finished sending emails</p>';

	$sql3 = "UPDATE tbl_newsletter SET newsletter_status = 'sent', newsletter_date = NOW() WHERE newsletter_id = '$nid'";
	$newsletters = @mysql_query($sql3);
	if (!$newsletters){
		exit('Unable to update newsletter status: '.mysql_error());
	}

} else {
	$email = TEST_EMAIL;

	$html_final = ereg_replace('##EMAIL##', $email, $html);
	$html_final = ereg_replace('##WEB ROOT##', WEB_ROOT, $html_final);

	// Add message to body
	$mail->Body = $html_final;

	$txt_final = ereg_replace('##EMAIL##', $email, $txt);
	$txt_final = ereg_replace('##WEB ROOT##', WEB_ROOT, $txt_final);

	$mail->AltBody = $txt_final;

	// Add a recipient address
	$mail->AddAddress($email, $name);

	// Send the message
	if(!$mail->Send()){
	    echo ('Mail sending failed to '. $email .'<br />');
	} else {
		echo ('Mail sent successfully '. $email .'<br />');
	}
    $mail->clearAddresses();

	echo '<p>Finished sending emails</p>';
}

echo "<p><a href='newsletter/index.php'>Back to newsletter listings</a></p>";

?>
<?php echo $footer; ?>