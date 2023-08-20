<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'library/_page.class.php';
require_once 'library/_dates.class.php';
require_once 'gallery/_gallery_config.inc.php';
require_once 'gallery/_gallery_images.inc.php';
require_once 'gallery/phMagick.php';

if (isset($_POST['collection_name'])){

	$collection_name = safeaddslashes($_POST['collection_name']);
	$collection_description = safeaddslashes($_POST['collection_description']);

	$collection_status = $_POST['collection_status'];
	if ($collection_status!='Active'){
		$collection_status='Inactive';
	}

	$dateValidator = new dateValidator();
	$collection_date = $dateValidator->getPOSTDate('collection_date');

	$cur_image = safeAddSlashes($_POST['cur_image']);
	$delete = $_POST['delete'];
	$new_image = uploadImage('new_image', 'collection');

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
	if ($_POST['collection_id']==-1){
		$sql = "INSERT INTO tbl_collection SET
				collection_url = '$new_image',
				collection_name = '$collection_name',
				collection_description = '$collection_description',
				collection_date = '$collection_date',
				collection_status='$collection_status'";

		$result = getQuery($sql);
		$collection_id = mysql_insert_id();


		header('location:'.$_SERVER['PHP_SELF'].'?action=new&collection_id='.$collection_id);
		exit();

	} else {
		$collection_id = $_POST['collection_id'];
		$sql = "UPDATE tbl_collection SET
				collection_url = '$new_image',
				collection_name = '$collection_name',
				collection_description = '$collection_description',
				collection_date = '$collection_date',
				collection_status='$collection_status'
				WHERE collection_id = '$collection_id'";

		$result = getQuery($sql);

		header('location:'.$_SERVER['PHP_SELF'].'?action=update&collection_id='.$collection_id);
		exit();
	}
}

if (isset($_GET['new'])){

	// initialise zero variables

	$collection_id = "-1";
	$collection_caption = "";
	$collection_url = "";
	$collection_date = date('Y-m-d');

} else {
	$collection_id = $_GET['collection_id'];

	$sql = "SELECT *
			FROM tbl_collection
			WHERE collection_id='$collection_id'";

	$result = getQuery($sql);

	if (mysql_num_rows($result)!=1){
		header('location:index.php');
		exit();
	}
	$row = mysql_fetch_array($result);

	$collection_name = htmlspecialchars($row['collection_name']);
	$collection_description = htmlspecialchars($row['collection_description']);
	$collection_url = htmlspecialchars($row['collection_url']);
	$collection_date = htmlspecialchars($row['collection_date']);
	$collection_status = htmlspecialchars($row['collection_status']);


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

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>

<h2>Edit Item Details</h2>
<?php echo $action_text; ?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="sck-form" enctype="multipart/form-data">

	<p>
	<label for="collection_name">Name:</label>
	<input type="text" id="collection_name" name="collection_name" value="<?php echo $collection_name; ?>" class="field" />
	</p>

	<p>
	<label for="collection_description">Description:</label>
	<textarea name="collection_description" id="collection_description"><?php echo $collection_description; ?></textarea>
	</p>

	<p>
	<input type="hidden" name="MAX_FILE_SIZE" value="10240000" />
	<input type="hidden" name="cur_image" value="<?php echo $collection_url; ?>" />
	<label><strong>Image:</strong></label>
	<span class="other"><input type="file" name="new_image" id="new_image" />
	<?php if ($collection_url!=''): ?>
	<span class="reservations_image">
		<img src="<?php echo GALLERY_PATH.'thumbs/'.$collection_url; ?>" /><br />
		<input type="checkbox" name="delete" value="delete" /> Delete this image
	</span>
	<?php endif ?>
	</span>
	</p>
		<?php
			$picker = new datePicker();
			$picker->setStartYear(1);
			$picker->setEndYear(1);
			$picker->setCurrentDate($collection_date);
			$picker->setFieldName('collection_date');
		?>
		<p>
			<label for="collection_date">Date:</label>
			<span class="other"><?php echo $picker->getDateSelector(); ?></span>
		</p>
	<p>
		<label for="collection_status">Active:</label>
		<?php if ($collection_status=='Active'): ?>
			<span class="other"><input type="checkbox" name="collection_status" id="collection_status" value="Active" checked="checked" /></span>
		<?php else: ?>
			<span class="other"><input type="checkbox" name="collection_status" id="collection_status" value="Active" /></span>
		<?php endif; ?>
	</p>
	<p>
		<input type="button" id="btnBack" name="btnCancel" value="Back" onclick="window.location.href='<?php echo WEB_ROOT; ?>editor/gallery/collection.php'" /><input type="submit" value="Save details" />
		<input type="hidden" id="collection_id" name="collection_id" value="<?php echo $collection_id; ?>" />
	</p>
</form>



<?php echo $footer; ?>