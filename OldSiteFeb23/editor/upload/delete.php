<?php
require_once 'library/security/_secure.inc.php';
require_once 'upload/_upload.config.php';
require_once 'upload/_upload.inc.php';


if (!isset($_GET['file']) || $_GET['file']==''){
	header('location:'.WEB_ROOT.'editor/upload/index.php');
	exit;
}

$file = $_GET['file'];

deleteFile($file);

header('location:'.WEB_ROOT.'editor/upload/index.php?action=delete');
exit;
?>