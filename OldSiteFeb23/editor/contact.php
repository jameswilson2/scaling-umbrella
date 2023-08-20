<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_page.class.php';

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>

<h2>Contact SCK Webworks</h2>

<p>To contact SCK Webworks via email with any queries or suggestions, please complete your details in the form below.<br />
All fields are required to be filled in.</p>
<?php if($_GET['error']==1): ?>
	<div class="sck-error-box">
		<p><strong>There were errors in submitting your details - please check and complete.</strong><br />
		If you are getting "Considered as SPAM", please remove any HTML formatting from the relevant field. This includes the character &lt; unfortunately.</p>
	</div>
<?php endif ?>
<form id="sck-form" method="post" action="contact_sendmail.php">

	<?php if($_GET['name']==1): ?>
	<div class="sck-error-p">Your name is considered as SPAM - please review.</div>
	<?php elseif($_GET['name']==2): ?>
	<div class="sck-error-p">Please enter your name.</div>
	<?php endif ?>

	<p>
		<label for="formName">Your Name:</label>
		<input type="text" name="formName" id="formName" class="field" value="<?php echo $_SESSION['name']; ?>" />
	</p>

	<?php if($_GET['email']==1): ?>
	<div class="sck-error-p">Please enter a valid email address.</div>
	<?php elseif($_GET['email']==2): ?>
	<div class="sck-error-p">Your email address is considered as SPAM - please review.</div>
	<?php elseif($_GET['email']==3): ?>
	<div class="sck-error-p">Please enter your email address.</div>
	<?php endif ?>

	<p>
		<label for="formEmail">Email Address:</label>
		<input type="text" name="formEmail" id="formEmail" class="field" value="<?php echo $_SESSION['email']; ?>" />
	</p>

	<?php if($_GET['tel']==1): ?>
	<div class="sck-error-p">Your telephone number is considered as SPAM - please review.</div>
	<?php elseif($_GET['tel']==2): ?>
	<div class="sck-error-p">Please enter your telephone number.</div>
	<?php endif ?>

	<p>
		<label for="formTele">Telephone Number:</label>
		<input type="text" name="formTele" id="formTele" class="field" value="<?php echo $_SESSION['telephone']; ?>" />
	</p>

	<?php if($_GET['mes']==1): ?>
	<div class="sck-error-p">Your message is considered as SPAM - please review.</div>
	<?php elseif($_GET['mes']==2): ?>
	<div class="sck-error-p">Please enter your message.</div>
	<?php endif ?>

	<p>
		<label for="formMessage">Your Message:</label>
		<textarea rows="6" cols="20" id="formMessage" name="formMessage" class="field"><?php echo $_SESSION['message']; ?></textarea>
	</p>

	<p>
		<input name="SignUpNow" type="submit" id="SendEmail" value="Send Email Now!" />
		<input name="ClearForm" type="reset" id="ClearForm" value="Clear Form" />
	</p>
	<div class="clear"></div>
</form>

<?php echo $footer; ?>