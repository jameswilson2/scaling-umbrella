<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'library/_page.class.php';
require_once 'gallery/_gallery_config.inc.php';
require_once 'gallery/_gallery_images.inc.php';
require_once 'gallery/phMagick.php';

if (isset($_POST['gallery_priority'])){

	$gallery_name = safeaddslashes($_POST['gallery_name']);
	$gallery_priority = safeaddslashes($_POST['gallery_priority']);
	$elm1 = safeaddslashes($_POST['elm1']);
	$elm2 = safeaddslashes($_POST['elm2']);

	$cur_image = safeAddSlashes($_POST['cur_image']);
	$delete = $_POST['delete'];
	$new_image = uploadImage(new_image);

	if ($new_image != ''){
		if ($cur_image != ''){
			deleteImage($cur_image);
		}
	} else {
		if ($delete != ''){
			deleteImage($cur_image);
			$new_image = '';
		} else {
			$new_image = $cur_image;
		}
	}


	// check if info_id set - if not then set as new - INSERT  $info_id==-1
	if ($_POST['gallery_id']==-1){
		$sql = "INSERT INTO tbl_gallery SET
				gallery_url = '$new_image',
				gallery_name = '$gallery_name',
				gallery_caption = '$elm1',
				gallery_description = '$elm2',
				gallery_priority = '$gallery_priority'";

		$result = @mysql_query($sql);
		if (!$result){
			exit('Could not add new item: '.mysql_error());
		}
		$gallery_id = mysql_insert_id();


		header('location:'.$_SERVER['PHP_SELF'].'?action=new&gallery_id='.$gallery_id);
		exit();

	} else {
		$gallery_id = $_POST['gallery_id'];
		$sql = "UPDATE tbl_gallery SET
				gallery_url = '$new_image',
				gallery_name = '$gallery_name',
				gallery_caption = '$elm1',
				gallery_description = '$elm2',
				gallery_priority = '$gallery_priority'
				WHERE gallery_id = '$gallery_id'";

		$result = @mysql_query($sql);
		if (!$result){
			exit('Could not update item: '.mysql_error());
		}

		header('location:'.$_SERVER['PHP_SELF'].'?action=update&gallery_id='.$gallery_id);
		exit();
	}
}

if (isset($_GET['new'])){

	// initialise zero variables

	$gallery_id = "-1";
	$gallery_caption = "";
	$gallery_description = "";
	$gallery_url = "";
	$gallery_priority = '';
	$gallery_name = '';

} else {
	$gallery_id = $_GET['gallery_id'];

	$sql = "SELECT *
			FROM tbl_gallery
			WHERE gallery_id='$gallery_id'";

	$result = @mysql_query($sql);
	if (!$result){
		exit('Could not get item detail from database: '.mysql_error());
	}

	if (mysql_num_rows($result)!=1){
		header('location:index.php');
		exit();
	}
	$row = mysql_fetch_array($result);

	$gallery_name = htmlspecialchars($row['gallery_name']);
	$gallery_priority = htmlspecialchars($row['gallery_priority']);
	$gallery_caption = $row['gallery_caption'];
	$gallery_description = $row['gallery_description'];
	$gallery_url = htmlspecialchars($row['gallery_url']);


}

$action = $_GET['action'];

switch ($action){
	case 'update':
		$action_text = "<p>Item details updated successfully!</p>";
		break;

	case 'new':
		$action_text = "<p>New item saved successfully!</p>";
		break;
}


$base_href = WEB_ROOT;
$css = CSS;

$script = <<<EOD
<!-- TinyMCE -->
<script type="text/javascript" src="files/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "exact",
		elements : "elm1,elm2",
		theme : "advanced",
		relative_urls : true, // Default value
		document_base_url : '$base_href',
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,embed_video_link,template",

		// Theme options
		theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,|,template",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,embed_video_link,cleanup,help,code",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,|,fullscreen",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",

		width : "576",
		height : "400",


		// Example content CSS (should be your site CSS)
		content_css : "$css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "$base_href/editor/files/lists/template_list.js",
		external_link_list_url : "$base_href/editor/files/lists/link_list.js",
		external_image_list_url : "$base_href/editor/imagery/tiny_imagelist.php",
		media_external_list_url : "$base_href/editor/files/lists/media_list.js"


	});
</script>
<!-- /TinyMCE -->
EOD;

$page = new Page($menus);

$page->addScript($script);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>
<style type="text/css">
.mceLayout { float:right; }
</style>
<h2>Edit Item Details</h2>
<?php echo $action_text; ?>
<!-- name="task_edit" id="task_edit" -->
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="sck-form" enctype="multipart/form-data">
	<p>
	<label for="gallery_name">Name:</label>
	<input type="text" id="gallery_name" name="gallery_name" value="<?php echo $gallery_name; ?>" class="field" />
	</p>
	<p><label><strong>Caption:</strong></label>
	<textarea name="elm1" id="elm1" cols="60" rows="15"><?php echo htmlentities($gallery_caption); ?></textarea></p>

	<p><label><strong>Description:</strong></label>
	<textarea name="elm2" id="elm2" cols="60" rows="15"><?php echo htmlentities($gallery_description); ?></textarea></p>
	
	<p>
	<input type="hidden" name="MAX_FILE_SIZE" value="10240000" />
	<input type="hidden" name="cur_image" value="<?php echo $gallery_url; ?>" />
	<label><strong>Image:</strong></label>
	<span class="other"><input type="file" name="new_image" id="new_image" />
	<?php if ($gallery_url!=''): ?>
	<span class="reservations_image">
		<img src="<?php echo GALLERY_PATH.'thumbs/'.$gallery_url; ?>" /><br />
		<input type="checkbox" name="delete" value="delete" /> Delete this image
	</span>
	<?php endif ?>
	</span>
	</p>
	<p>
	<label for="priority">Position:</label>
	<input type="text" id="gallery_priority" name="gallery_priority" value="<?php echo $gallery_priority; ?>" class="field" />
	</p>
	<p>
		<input type="submit" value="Save details" />
		<input type="hidden" id="gallery_id" name="gallery_id" value="<?php echo $gallery_id; ?>" />
	</p>
</form>



<?php echo $footer; ?>