<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_page.class.php';

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>

<h2>Webmail Login</h2>
<p class="center">To log-in to WebMail please enter your email address and password below.</p>

<form action="http://webmail.sck-webworks.co.uk/" method="post" target="_blank" id="sck-form">
	<p>
		<label for="username"><strong>Email Address:</strong></label>
		<input type="text" name="username" id="username" size="30" maxlength="200" class="field" />
	</p>
	<p>
		<label for="password"><strong>Password:</strong></label>
		<input type="password" name="password" id="password" size="30" maxlength="200" class="field" />
	</p>
	<p>
		<input type="submit" name="sub" value="Log-in Now!" />
	</p>
</form>
<div class="clear"></div>

<?php echo $footer; ?>



