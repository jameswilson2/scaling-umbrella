<?php
require_once '_editor_config.php';
require_once '_secure.inc.php';
include '_comments_dbconn.inc.php';

if (!isset($_GET['faq_id']) || $_GET['faq_id']==''){
	header('location:'WEB_ROOT.'editor/faq/index.php');
	exit;
}

$faq_id = $_GET['faq_id'];

$sql = "DELETE FROM tbl_faq WHERE faq_id='$faq_id'";

$result = getQuery($sql, 'Could not delete FAQ: ');

header('location:'WEB_ROOT.'editor/faq/index.php');
exit;

?>

