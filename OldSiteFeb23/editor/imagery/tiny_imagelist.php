<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';
?>
var tinyMCEImageList = new Array(<?php
		$sql = "SELECT image_category_id, image_category_name FROM tbl_images_categories";
		$allCategories = getQuery($sql, 'Could not get categories: ');
		$firstCat = true;
		while($category = mysql_fetch_array($allCategories)) {
			$catName = $category["image_category_name"];
			$catId = $category["image_category_id"];
			$sql = "SELECT image_url, image_id FROM tbl_images WHERE image_category = $catId";
			$catImages = getQuery($sql, 'Could not get images: ');
			if(mysql_num_rows($catImages) > 0) {
				if($firstCat) {
					$firstCat = false;
				} else {
					echo ",";
				}
				$firstImg = true;
				while($image = mysql_fetch_array($catImages)) {
					if($firstImg) {
						$firstImg = false;
					} else {
						echo ",";
					}
					$url = $image["image_url"];
					echo "['$url','".IMAGE_FOLDER.$url."','$catName']";
				}
			}
		}
	?>);