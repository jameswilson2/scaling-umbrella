<?php require_once 'library/security/_secure.inc.php';

$contact_id = safeaddslashes($_GET['contact_id']);

$sql = "DELETE FROM tbl_contact WHERE contact_id='$contact_id'";
$result = getQuery($sql);

header('location:'.WEB_ROOT.'editor/enquiries/');
exit;

?>