<?php
require_once 'library/security/_secure.inc.php';

if (isset($_GET['cid'])){
	$comment_id = $_GET['cid'];
	$action = $_GET['action'];
	switch ($action){
		case 'approve':
			$sql = "UPDATE tbl_comments
					SET comment_status = 'approved'
					WHERE comment_id = '$comment_id'";
			$result = getSimpleQuery($sql, 'Error moderating comment: ');
			$status = "<img src=\"presentation/approve_status.gif\" width=\"27\" height=\"14\" alt=\"Approved\" />&nbsp;<img src=\"presentation/status_or.gif\" width=\"7\" height=\"14\" alt=\"\" />&nbsp;<a href=\"comments/moderate.php?cid=$comment_id&amp;action=reject\" onclick=\"return updateElm('comments/moderate_ajax.php?cid=$comment_id&amp;action=reject', 'comment_status_$comment_id');\"><img src=\"presentation/reject_status.gif\" width=\"27\" height=\"14\" alt=\"Reject\" /></a>";
		break;

		case 'reject':
			$sql = "UPDATE tbl_comments
					SET comment_status = 'rejected'
					WHERE comment_id = '$comment_id'";
			$result = getSimpleQuery($sql, 'Error moderating comment: ');
			$status = "<img src=\"presentation/reject_status.gif\" width=\"27\" height=\"14\" alt=\"Rejected\" />&nbsp;<img src=\"presentation/status_or.gif\" width=\"7\" height=\"14\" alt=\"\" />&nbsp;<a href=\"comments/moderate.php?cid=$comment_id&amp;action=approve\" onclick=\"return updateElm('comments/moderate_ajax.php?cid=$comment_id&amp;action=approve', 'comment_status_$comment_id');\"><img src=\"presentation/approve_status.gif\" width=\"27\" height=\"14\" alt=\"Approve\" /></a>";
		break;

	}
}

echo $status;

?>