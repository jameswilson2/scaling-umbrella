<?php
require_once 'library/security/_secure.inc.php';

if (isset($_GET['cid'])){
	$cid = $_GET['cid'];
	$action = $_GET['action'];
	switch ($action){
		case 'approve':
			$sql = "UPDATE tbl_comments
					SET comment_status = 'approved'
					WHERE comment_id = '$cid'";
			$result = getQuery($sql, 'Error moderating comment: ');
		break;

		case 'reject':
			$sql = "UPDATE tbl_comments
					SET comment_status = 'rejected'
					WHERE comment_id = '$cid'";
			$result = getQuery($sql, 'Error moderating comment: ');
		break;

	}
}
header('location:'.WEB_ROOT.'editor/comments/index.php');
exit();

?>