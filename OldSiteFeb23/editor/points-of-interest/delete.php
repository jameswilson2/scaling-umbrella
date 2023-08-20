<?php require_once 'library/security/_secure.inc.php';

$icon_id = safeaddslashes($_GET['icon_id']);
$poi_id = safeaddslashes($_GET['poi_id']);

if($icon_id != "") {
	$sql = "DELETE FROM tbl_poi_icons WHERE icon_id='$icon_id'";
	$result = getQuery($sql);
	header('location:'.WEB_ROOT.'editor/points-of-interest/icon_index.php?action=deleteditem');
	exit;
}
if($poi_id != "") {
	$sql = "DELETE FROM tbl_poi WHERE poi_id='$poi_id'";
	$result = getQuery($sql);
	header('location:'.WEB_ROOT.'editor/points-of-interest/index.php?action=deleteditem');
	exit;
}
exit;

?>