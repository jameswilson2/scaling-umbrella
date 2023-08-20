<?php require_once 'library/security/_secure.inc.php';

$area_id = safeaddslashes($_GET['area_id']);

if($area_id != "") {
	$sql = "DELETE FROM tbl_maparea WHERE area_id='$area_id'";
	$result = getQuery($sql);
	header('location:'.WEB_ROOT.'editor/coverage/index.php?action=deleteditem');
	exit;
}
exit;

?>