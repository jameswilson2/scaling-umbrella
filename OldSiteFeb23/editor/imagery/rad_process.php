<?php
require_once 'library/security/_access.inc.php';
require_once 'imagery/_images.config.php';

// recieves a POST from the central RAD - save to temporary location in editor

?><html>
<head><title>Rad Upload Plus</title></head>
<body  bgcolor="FFFFCC">
<?php

/*
 * SET THE SAVE PATH by editing the line below. Make sure that the path
 * name ends with the correct file system path separator ('/' in linux and
 * '\\' in windows servers (eg "c:\\temp\\uploads\\" )
 */

$file = $_FILES['userfile'];

$filename = $_GET['filename'];
$k = count($file['name']);

$save_path = EDITABLE_ROOT."editor/temp/";

@mkdir($save_path);

for($i=0 ; $i < $k ; $i++){
	copy($file['tmp_name'][$i], $save_path.$filename);
}
?>
</body>
</html>

?>