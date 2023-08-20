<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_extras_dbconn.inc.php';

if (isset($_POST['cats'])){
	$cats = $_POST['cats'];
	$cat = explode(',', $cats);
	foreach ($cat AS $category_name){
		$category_name = safeAddSlashes(ucwords(trim($category_name)));
		$sql = "SELECT category_name FROM tbl_faq_category WHERE category_name = '$category_name'";
		$result = getQuery($sql,'Could not get category list from database: ');
		if (mysql_num_rows($result)==0){
			$sql = "INSERT INTO tbl_faq_category SET category_name = '$category_name'";
			$result = getQuery($sql, 'Could not add to category list: ');
		}
	}
	header('location:'.WEB_ROOT.'editor/faq/category.php?action=addsuccess');
	exit();
}
?>