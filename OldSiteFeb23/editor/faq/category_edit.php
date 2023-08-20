<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_extras_dbconn.inc.php';

if (isset($_GET['category_id']) && (int)$_GET['category_id']>0){
	$category_id = $_GET['category_id'];
	$newname = $_POST['NewTypeName'.$category_id];
	$newname = ucwords(trim($newname));
	$sql = "SELECT category_name FROM tbl_faq_category WHERE category_name='$newname'";
	$result = getQuery($sql);
	$rows = mysql_num_rows($result);
	$row = mysql_fetch_array($result);
	$oldman = $row['type_name'];
	if ($rows!=0 && strtolower($oldman)==strtolower($newname)){
		header('location:'.WEB_ROOT.'editor/faq/category.php?action=renamemanexists');
		exit();
	} else {
		$newname = safeAddSlashes($newname);
		$sql = "UPDATE tbl_faq_category SET
				category_name = '$newname'
				WHERE category_id = '$category_id'";
		$result = getQuery($sql);
		header('location:'.WEB_ROOT.'editor/faq/category.php?action=renameman');
		exit();
	}
}

?>