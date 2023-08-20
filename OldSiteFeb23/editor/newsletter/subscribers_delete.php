<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_page.class.php';
require_once 'library/_extras_dbconn.inc.php';

if(!isset($_GET['member_id'])){
	header('location:'.WEB_ROOT.'editor/newsletter/subscribers.php');
	exit();
}

$member_id = safeaddslashes($_GET['member_id']);

$sql = "DELETE FROM tbl_member WHERE member_id='$member_id'";
$result = @mysql_query($sql);


header('location:'.WEB_ROOT.'editor/newsletter/subscribers.php?success');
exit();

?>