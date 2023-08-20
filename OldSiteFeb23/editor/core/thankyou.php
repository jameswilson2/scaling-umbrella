<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_page.class.php';

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>

<h2>Thank you for contacting us</h2>
<p>Thank you for your email.</p>
<p>We will get back to you shortly.</p>
<p><em>The SCK Web Works Team</em></p>

<?php echo $footer; ?>
