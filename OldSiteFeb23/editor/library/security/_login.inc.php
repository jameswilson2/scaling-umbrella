<?php
require_once 'library/_page.class.php';

$page = new Page($menus);

$page->setTitle('Please log in for access');
$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>

<h2>Please log in for access to the editor</h2>

<?php if(isset($errorMessage)): ?>
<div class="sck-error-box">
	<p><strong><?php echo $errorMessage; ?></strong></p>
</div>
<?php endif; ?>

<form action="<?php echo $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']; ?>" method="post" id="sck-form">

    <input type="hidden" name="sckeditor-login" value="1" />
    
	<p>
		<label for="email">Email address:</label>
		<input type="text" name="email" class="field" />
	</p>
	<p>
		<label for="password">Password:</label>
		<input type="password" name="password" class="field" />
	</p>
	<div class="other">
	    <input type="checkbox" name="stay_logged_in" id="_stay_logged_in" value="1"><label for="_stay_logged_in">Stay logged in</label>
	</div>
	<p>
		<input type="submit" value="Login" style="padding:0.5em;" />
	</p>
</form>
<div class="clear"></div>

<?php echo $footer; ?>
