<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'library/_page.class.php';
require_once 'gallery/_gallery_config.inc.php';
require_once 'gallery/_gallery_images.inc.php';
require_once 'gallery/phMagick.php';

if (isset($_POST['gallery_priority'])){

	$gallery_collection_id = safeaddslashes($_POST['gallery_collection_id']);
	$gallery_name = safeaddslashes($_POST['gallery_name']);
	$gallery_priority = safeaddslashes($_POST['gallery_priority']);
	$gallery_description = safeaddslashes($_POST['gallery_description']);

	$gallery_status = $_POST['gallery_status'];
	if ($gallery_status!='Active'){
		$gallery_status='Inactive';
	}

	$cur_image = safeAddSlashes($_POST['cur_image']);
	$delete = $_POST['delete'];
	$new_image = uploadImage('new_image');

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
				gallery_collection_id = '$gallery_collection_id',
				gallery_url = '$new_image',
				gallery_name = '$gallery_name',
				gallery_description = '$gallery_description',
				gallery_priority = '$gallery_priority',
				gallery_status='$gallery_status'";

		$result = getQuery($sql);
		$gallery_id = mysql_insert_id();


		header('location:'.$_SERVER['PHP_SELF'].'?action=new&gallery_id='.$gallery_id);
		exit();

	} else {
		$gallery_id = $_POST['gallery_id'];
		$sql = "UPDATE tbl_gallery SET
				gallery_collection_id = '$gallery_collection_id',
				gallery_url = '$new_image',
				gallery_name = '$gallery_name',
				gallery_description = '$gallery_description',
				gallery_priority = '$gallery_priority',
				gallery_status='$gallery_status'
				WHERE gallery_id = '$gallery_id'";

		$result = getQuery($sql);

		header('location:'.$_SERVER['PHP_SELF'].'?action=update&gallery_id='.$gallery_id);
		exit();
	}
}

if (isset($_GET['new'])){

	// initialise zero variables
	if(isset($_GET["gallery_collection_id"])) {
		$gallery_collection_id = $_GET["gallery_collection_id"];
	}
	$gallery_id = "-1";
	$gallery_caption = "";
	$gallery_url = "";
	$gallery_priority = '';

} else {
	$gallery_id = $_GET['gallery_id'];

	$sql = "SELECT *
			FROM tbl_gallery
			WHERE gallery_id='$gallery_id'";

	$result = getQuery($sql);

	if (mysql_num_rows($result)!=1){
		header('location:index.php');
		exit();
	}
	$row = mysql_fetch_array($result);

	$gallery_collection_id = htmlspecialchars($row['gallery_collection_id']);
	$gallery_name = htmlspecialchars($row['gallery_name']);
	$gallery_description = htmlspecialchars($row['gallery_description']);
	$gallery_url = htmlspecialchars($row['gallery_url']);
	$gallery_priority = htmlspecialchars($row['gallery_priority']);
	$gallery_status = htmlspecialchars($row['gallery_status']);


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

$sql = "SELECT collection_id, collection_name FROM tbl_collection ORDER BY collection_name ASC";
$collections = getQuery($sql);

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>

<h2>Edit Item Details</h2>
<?php echo $action_text; ?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="sck-form" enctype="multipart/form-data">

	<p>
	<label for="gallery_collection_id">Bike: <?php echo $gallery_collection_id; ?></label>
	<select name="gallery_collection_id"> 
	<?php
		
		while($collection = mysql_fetch_array($collections)){
			$collection_id = $collection['collection_id'];
			$collection_name = htmlspecialchars($collection['collection_name']);

			if ($collection_id == $gallery_collection_id){
				echo "<option value=\"$collection_id\" selected=\"selected\">$collection_name</option>";
			} else {
				echo "<option value=\"$collection_id\">$collection_name</option>";
			}
		}
	?>
	</select>
	</p>

	<p>
	<label for="gallery_name">Name:</label>
	<input type="text" id="gallery_name" name="gallery_name" value="<?php echo $gallery_name; ?>" class="field" />
	</p>

	<p>
	<label for="gallery_description">Description:</label>
	<textarea name="gallery_description" id="gallery_description"><?php echo $gallery_description; ?></textarea>
	</p>

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
		<label for="gallery_status">Active:</label>
		<?php if ($gallery_status=='Active'): ?>
			<span class="other"><input type="checkbox" name="gallery_status" id="gallery_status" value="Active" checked="checked" /></span>
		<?php else: ?>
			<span class="other"><input type="checkbox" name="gallery_status" id="gallery_status" value="Active" /></span>
		<?php endif; ?>
	</p>
	<p>
		<input type="button" id="btnBack" name="btnCancel" value="Back" onclick="window.location.href='<?php echo WEB_ROOT; ?>editor/gallery/index.php'" /><input type="submit" value="Save details" />
		<input type="hidden" id="gallery_id" name="gallery_id" value="<?php echo $gallery_id; ?>" />
	</p>
</form>



<?php echo $footer; ?>