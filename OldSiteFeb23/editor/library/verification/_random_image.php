<?php

require_once 'library/security/_access.inc.php';

// Include RandomImageText class
require_once 'library/verification/_random_image_text.class.php';

// Instantiate RandomImageText giving the background image
$imageText=new RandomImageText (EDITABLE_ROOT.'editor/library/verification/reg_image.jpg');

// Add the text from the session
$imageText->addText($_SESSION['randomString']);

// Send the right mime type
header ('Content-type: image/jpeg');

// Display the image
ImageJpeg($imageText->getImage());
?>