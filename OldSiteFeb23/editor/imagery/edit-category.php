<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';
require_once 'library/_page.class.php';

$_page = new Page($menus);

$header = $_page->getHeader();
$footer = $_page->getFooter();

echo $header; ?>

<?php
	if(isset($_POST["categoryName"])) {
		foreach($_POST["categoryName"] as $key => $cat) {
			if($cat != "") {
				if($key == "0") {
					$pdo = create_pdo();
					$thisSelect = $pdo->prepare("SELECT image_category_id, image_category_name FROM tbl_images_categories WHERE image_category_name = :cat");
					$thisSelect->bindValue(":cat", $cat);
					$thisSelect->execute();
					$category = $thisSelect->fetchAll();
					if(count($category) ==0) {
						$pdo = create_pdo();
						$thisUpdate = $pdo->prepare("INSERT INTO tbl_images_categories (image_category_name) VALUES (:name)");
						$thisUpdate->bindValue(":name", $cat);
						$thisUpdate->execute();
					} else {
						echo "<p>A Category of that name already exists</p>";
					}
				} else {
					$sql = "SELECT image_category_id, image_category_name FROM tbl_images_categories WHERE image_category_id = $key";
					$category = getQuery($sql, 'Could not get images: ');
					$thiscategory = mysql_fetch_array($category);
					if($thiscategory["image_category_name"] != $cat) {
						$pdo = create_pdo();
						$thisUpdate = $pdo->prepare("UPDATE tbl_images_categories SET image_category_name =:name WHERE image_category_id = :key");
						$thisUpdate->bindValue(":name", $cat);
						$thisUpdate->bindValue(":key", $key);
						$thisUpdate->execute();
					}
				}
			}
		}
	}
?>
<h2><?php echo SITE_NAME; ?> - Edit Image Categories</h2>


<?php

$sql = "SELECT image_category_id, image_category_name FROM tbl_images_categories";
$allCategories = getQuery($sql, 'Could not get images: ');
$catCount = mysql_num_rows($allCategories);
$lastCat = 0;
?>
<form action="imagery/edit-category.php" class="imageCategoryForm" method="post">
<?php
while($category = mysql_fetch_array($allCategories)) {
	$catName = $category["image_category_name"];
	$catId = $category["image_category_id"];
	echo "<div><label>Category: </label><input value='$catName' name='categoryName[$catId]' /></div>";
	$lastCat = $catId;
}
?>
<div><label>New Category</label><input name="categoryName[0]" /></div>
<a href="imagery/index.php"><input type="button" value="Back" /></a>
<input type="submit" value="Submit" class="photoRight" />
</form>

<?php echo $footer; ?>