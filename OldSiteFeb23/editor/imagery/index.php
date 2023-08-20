<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';
require_once 'library/_page.class.php';

$_page = new Page($menus);

$header = $_page->getHeader();
$footer = $_page->getFooter();

if(isset($_POST["imageCategory"])) {
	foreach($_POST["imageCategory"] as $key => $cat) {
		$sql = "SELECT image_id, image_url, image_category FROM tbl_images WHERE image_id = $key";
		$category = getQuery($sql, 'Could not get image: ');
		if($category["image_category"] != $cat) {
			$pdo = create_pdo();
			$thisUpdate = $pdo->prepare("UPDATE tbl_images SET image_category =:cat WHERE image_id = :key");
			$thisUpdate->bindValue(":cat", $cat);
			$thisUpdate->bindValue(":key", $key);
			$thisUpdate->execute();
		}
	}
}
if(isset($_GET["deleteImage"])) {
	$key = $_GET["deleteImage"];
	$pdo = create_pdo();
	$thisUpdate = $pdo->prepare("UPDATE tbl_images SET image_category =:cat WHERE image_id = :key");
	$thisUpdate->bindValue(":cat", 0);
	$thisUpdate->bindValue(":key", $key);
	$thisUpdate->execute();
}


echo $header; ?>
<h2><?php echo SITE_NAME; ?> - View Images</h2>

<div id="panelOne"><p><strong class="blue">Instructions:</strong> Do you need some simple instructions on how to use this page? if so <a href="javascript:showInstructions();">click here</a>!</p></div>
	<div id="panelTwo">
	<p><strong class="blue">Simple Instructions:</strong> <a href="javascript:hideInstructions();">Hide these instructions!</a></p>

	<p><strong class="blue">View Images:</strong> Below is a list of images that are currently in your "images" folder on your website. Some or all of these images may exist on your web pages.</p>
	<p>To replace one of these images with a new one click on the image you wish to replace / update and follow the on screen instructions to make the changes.</p>
	<p>If you simply just want to use an image on a page, copy the location using the "Copy Location" button, then add it to a page using the edit pages link in the menu.</p>
	</div>
	<script type="text/javascript">
	<!--
	$(document).ready(function() {
		$("#panelTwo").hide();
	});
	function showInstructions() {
		$("#panelOne").slideUp("slow");
		$("#panelTwo").slideDown("slow");
	}
	function hideInstructions() {
		$("#panelOne").slideDown("slow");
		$("#panelTwo").slideUp("slow");
	}
	//-->
</script>


<?php

$action = $_GET['action'];
if ( $action ) {
	echo "<div id=\"user-notice\">";
	switch ($action){
		case 'imagesuccess':
			echo "<p>Image replaced successfully</p>";
			break;
		case 'newimagesuccess':
			$filename = $_GET['filename'];
			/*echo "<p>Image uploaded successfully.  To use this image in a page copy and paste the following location: <strong>".IMAGE_FOLDER.$filename."</strong></p>";*/

			echo "<p>Your file was uploaded successfully to: <strong>".IMAGE_FOLDER.$filename."</strong> (<a href=\"javascript:copy(document.getElementById('user-notice-url').value);\">copy</a>/<a href=\"javascript:userNoticeHide();\">hide</a>)<br />
			<small>The location of this image has been copied, all you need to do is paste it into the page you want.</small></p>";
			echo "<form action=\"#\"><input type=\"hidden\" id=\"user-notice-url\" value=\"".IMAGE_FOLDER.$filename."\" /></form>";
			break;
	}
	echo "</div>";
?>
<script type="text/javascript">
<!--
$(document).ready(function() {
	$("#user-notice").hide();
	if ($("#user-notice").is(":hidden")) {
		if ( document.getElementById("user-notice-url") ) {
			copy(document.getElementById("user-notice-url").value);
		}
		$("#user-notice").slideDown("slow");
		$("#user-notice").animate( { backgroundColor: "#DC9E26" }, 600 );
		$("#user-notice").animate( { backgroundColor: "#2683DC" }, 2000 );
		$("#user-notice").animate( { backgroundColor: "#DC9E26" }, 600 );
		$("#user-notice").animate( { backgroundColor: "#2683DC" }, 2000 );
		$("#user-notice").animate( { backgroundColor: "#DC9E26" }, 600 );
		$("#user-notice").animate( { backgroundColor: "#2683DC" }, 2000 );
	}
});
function userNoticeHide() {
	$("#user-notice").dequeue();
	$("#user-notice").stop();
	$("#user-notice").slideUp("slow");
}
//-->
</script>
<?php } ?>

<div class="content_cut">

<?php
$location = IMAGE_PATH;
// open current directory
$dp = opendir($location);
// loop through the directory
$file_list = Array();

while (false !== ($entry = readdir($dp))) {
	if (is_file($location.$entry) && eregi('.jpe?g$', $entry)) {
		// $entry is a jpg or jpeg file...
		$match = 0;
		foreach ($disallowed_images as $dis_images) {
			if (strtolower($dis_images) == strtolower($entry) ){
				$match++;
			}
		}
		if ($match==0) {
			$file_list[] = $entry;
		}
	}
}
// Close directory
closedir($dp);

$file_lowercase = array_map('strtolower', $file_list);
array_multisort($file_lowercase, SORT_ASC, SORT_STRING, $file_list);

foreach($file_list as $file) {
	$sql = "SELECT image_url FROM tbl_images WHERE image_url='$file'";

	$matchedImages = getQuery($sql, 'Could not get images: ');
	if(mysql_num_rows($matchedImages) == 0) {
		$pdo = create_pdo();
		$thisUpdate = $pdo->prepare("INSERT INTO tbl_images (image_url) VALUES (:url)");
		$thisUpdate->bindValue(":url", $file);
		$thisUpdate->execute();
	}
}

$sql = "SELECT image_url FROM tbl_images";
$allImages = getQuery($sql, 'Could not get images: ');
while($image = mysql_fetch_array($allImages)) {
	if(!in_array($image["image_url"],$file_list)) {
		$url = $image["image_url"];
		$sql = "DELETE FROM tbl_images WHERE image_url = '$url'";
		$result = getQuery($sql, 'Deleted');
	}
}

$sql = "SELECT image_category_id, image_category_name FROM tbl_images_categories order by image_category_name";
$allCategories = getQuery($sql, 'Could not get images: ');

?>

<div id="newitems">
	<p><img src="presentation/icon_plus.gif" alt="Add" width="16" height="15" title="Add" class="minicon" /> <strong><a href="imagery/upload_caller.php?type=upload">Upload new image</a></strong> <img src="presentation/icon_plus.gif" alt="Add" width="16" height="15" title="Add" class="minicon" /> <strong><a href="imagery/edit-category.php">Add/Edit Categories</a></strong> <img src="presentation/icon_expand.png" alt="Expand" width="11" height="17" title="Add" class="minicon" /> <strong><a href="javascript:expandAllCats();">Expand All Categories</a></strong></p>
</div>
<script type="text/javascript">
function setDuplicateSel(selid, item) {
	console.log(selid);
	$('#'+selid).attr('selectedIndex',$(item).attr('selectedIndex'));
}
function expandAllCats() {
	$('.categoryBody').stop(true,true);
	$('.categoryHead:not(.active)').click();
}
function getImagesForCat(catid) {
	$.ajax({
		url:'imagery/catlist.php?catid='+catid,
		dataType:'json',
		success:function(data) {
			var myhtml = "";
			$.each(data.images,function(i, a){
				myhtml += '<div class="image_box" data-id="'+a.image_id+'"><p class="image_header"><span class="jshidden">Click filename replace: </span><a href="imagery/upload_caller.php?image='+a.image_url+'&amp;type=replace" class="replaceLink">'+a.image_url+'</a><div class="copy-img-url">Image Location: <input type="text" value="<?php echo IMAGE_FOLDER; ?>'+a.image_url+'" size="20" class="select-all" readonly /></div></p><p><select name="imageCategory['+a.image_id+']" id="imageCategory_'+a.image_id+'">';
				
				$.each(data.categories,function(i, a) {
					if(a.catoptid == catid) {
						myhtml += "<option value='"+a.catoptid+"' selected='selected'>"+a.catoptname+"</option>";
					} else {
						myhtml += "<option value='"+a.catoptid+"'>"+a.catoptname+"</option>";
					}
				});
				myhtml += '</select><input type="submit" value="Update Category" /></p><p class="image_image"><a class="image_thumblink" href="../<?php echo IMAGE_FOLDER ?>'+a.image_url+'" rel="images" title="<?php echo IMAGE_FOLDER; ?>'+a.image_url+'"><img src="../<?php echo IMAGE_FOLDER; ?>'+a.image_url+'" alt="Enlarge" title="Enlarge" /></a></p></div>';
				if((i-2) % 3 == 0 && (i-2) != 0) {
					myhtml += '<div class="clear"></div>';
				}
			});
			$cat = $('#category'+catid);
			$cat.html(myhtml + "<div class='clear'></div>");
			$cat.find('.image_box').children(':not(.image_header)').hide();
			$cat.find('.image_image').show().addClass('jsimg').children('a').fancybox({
					'titleShow':true,
					'titlePosition':'inside',
					'titleFormat': function(title, currentArray, currentIndex, currentOpts) { 
					$a = $('.image_thumblink').filter('[href*="' + title + '"]');
					$ib = $a.parents('.image_box');
					var content = "<div class='popTitle'>";
					content += "<div class='replace_link'><a href='"+$ib.find('.replaceLink').attr('href')+"'><input type='button' value='Replace' /></a></div>";
					content += '<div class="copy-img-url">' + $ib.find('.copy-img-url').html() + '</div>';
					content += '<div class="del-img-url"><a href="imagery/index.php?deleteImage=' + $ib.attr('data-id') + '" onclick="return confirm(\'Are you sure you want to delete this image?\')"><input type="button" value="Delete" /></a></div>'; 
					$sel = $ib.find('select');
					content += '<div>Category: <select onchange="setDuplicateSel(\''+$sel.attr('id')+'\', this)">' + $sel.html() + '</select><a href="javascript:$(\'#imagesForm\').submit();"><input type="button" value="Submit" /></a></div>';
					content += "</div>";
					return content
				}
			});
		}
	});
}
</script>
<form action="imagery/index.php" id="imagesForm" method="post">
<?php
while($category = mysql_fetch_array($allCategories)) {
	$catName = $category["image_category_name"];
	$catId = $category["image_category_id"];
	
	$sql = "SELECT image_url, image_id FROM tbl_images WHERE image_category = $catId";
	$catImages = getQuery($sql, 'Could not get categories: ');
	$imageCount = mysql_num_rows($catImages);
	echo "<div class='categoryHead'>$catName ($imageCount)</div><div class='categoryBody' data-catid='$catId' id='category$catId'><div class='center'><img src='presentation/loading.gif' alt='Loading' /></div><div class='clear'></div></div><script type='text/javascript'>getImagesForCat($catId)</script>";
}
?>
</form></div>
<script type="text/javascript">
$('.categoryBody').hide();
$('.categoryHead').click(function() {
	$a = $(this);
	if($a.hasClass('active')) {
		$a.removeClass('active').next('.categoryBody').animate({'height':'hide'},200);
	} else {
		$a.addClass('active').next('.categoryBody').animate({'height':'show'},200);
	}
});
</script>

<?php echo $footer; ?>