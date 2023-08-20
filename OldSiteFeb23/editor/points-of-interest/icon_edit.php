<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_dates.class.php';
require_once 'library/_page.class.php';
require_once 'points-of-interest/_poi.config.php';
require_once 'points-of-interest/_poi_images.inc.php';

// values for
// WEB_ROOT & $CSS needed

$errors = array();

$icon_id = $_POST["icon_id"];
$icon_title = safeaddslashes($_POST['icon_title']);
$icon_width = safeaddslashes($_POST['icon_width']);
$icon_height = safeaddslashes($_POST['icon_height']);
$icon_hotspot = safeaddslashes($_POST['icon_hotspot']);

if (isset($_POST["icon_id"])){
	
	
	$cur_image = safeAddSlashes($_POST['cur_image']);
	$cur_over_image = safeAddSlashes($_POST['cur_over_image']);
	$delete = $_POST['delete_image'];
	$delete_over = $_POST['delete_over_image'];
	$icon_image = uploadImage(new_image);
	
	if ($icon_image != ''){
		if ($cur_image != ''){
			deleteImage($cur_image);
		}
	} else {
		if ($delete != ''){
			deleteImage($cur_image);
			$icon_image = '';
		} else {
			$icon_image = $cur_image;
		}
	}
	
	
	$icon_over_image = uploadImage(new_over_image);
	
	if ($icon_over_image != ''){
		if ($cur_over_image != ''){
			deleteImage($cur_over_image);
		}
	} else {
		if ($delete_over != ''){
			deleteImage($cur_over_image);
			$icon_over_image = '';
		} else {
			$icon_over_image = $cur_over_image;
		}
	}
	
	if($icon_title == "") { $errors['title'] = "Please enter a title"; }
	if($icon_image == "") { $errors['image'] = "Please enter an image"; }
	if($icon_over_image == "") { $errors['overimage'] = "Please enter an image for the over state"; }
	if($icon_width == "") { $errors['width'] = "Please enter a width"; }
	if($icon_height == "") { $errors['height'] = "Please enter a height"; }
	if($icon_hotspot == "") { $errors['hotspot'] = "Please enter a hotspot"; }
	
	if(count($errors) == 0) {
		// check if info_id set - if not then set as new - INSERT  $info_id==-1
		if ($_POST['icon_id']==-1){
			$sql = "INSERT INTO tbl_poi_icons SET
					icon_title = '$icon_title',
					icon_image = '$icon_image',
					icon_over_image = '$icon_over_image',
					icon_width = '$icon_width',
					icon_height = '$icon_height',
					icon_hotspot = '$icon_hotspot'";

			$result = @mysql_query($sql);
			if (!$result){
				exit('Could not add new icon: '.mysql_error());
			}
			$icon_id = mysql_insert_id();

			header('location:'.WEB_ROOT.'editor/points-of-interest/icon_edit.php?action=itemcreated&icon_id='.$icon_id);
			exit();

		} else {
			$icon_id = $_POST['icon_id'];
			$sql = "UPDATE tbl_poi_icons SET
					icon_title = '$icon_title',
					icon_image = '$icon_image',
					icon_over_image = '$icon_over_image',
					icon_width = '$icon_width',
					icon_height = '$icon_height',
					icon_hotspot = '$icon_hotspot'
					WHERE icon_id = '$icon_id'";

			$result = @mysql_query($sql);
			if (!$result){
				exit('Could not update icon: '.mysql_error());
			}

			header('location:'.WEB_ROOT.'editor/points-of-interest/icon_edit.php?action=update&icon_id='.$icon_id);
			exit();
		}
	}
}

if (isset($_GET['new'])){

	// initialise zero variables

	$icon_id = "-1";
	$title = "";
	$image = '';
	$over_image = '';
	$width = "";
	$height = "";
	$hotspot= "";

} else if($_POST['icon_id'] == "-1") {
	$icon_id = $icon_id;
	$title = $icon_title;
	$image = $icon_image;
	$over_image = $icon_over_image;
	$width = $icon_width;
	$height = $icon_height;
	$hotspot = $icon_hotspot;
} else {
	$icon_id = $_GET['icon_id'];

	$sql = "SELECT *
			FROM tbl_poi_icons
			WHERE icon_id='$icon_id'";

	$result = @mysql_query($sql);
	if (!$result){
		exit('Could not get item detail from database: '.mysql_error());
	}

	if (mysql_num_rows($result)!=1){
		//header('location:icon_index.php');
		//exit();
	}
	$row = mysql_fetch_array($result);

	$title = htmlspecialchars($row['icon_title']);
	$image = $row['icon_image'];
	$over_image = $row['icon_over_image'];
	$width = $row['icon_width'];
	$height = $row['icon_height'];
	$hotspot = $row['icon_hotspot'];
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

$page = new Page($menus);

$_header = $page->getHeader();
$_footer = $page->getFooter();

echo $_header; ?>
<h2><?php echo isset($_GET['new'])?"Create":"Edit"?> Point of Interest Icon</h2>

	<?php
	if ( $action_text ) {
		echo "<div id=\"user-notice\">";
		echo $action_text;
		echo "</div>";
	?>
	<?php } ?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="sck-form" enctype="multipart/form-data">
		<?php if(isset($errors['title'])){ echo "<p class='error'>".$errors['title']."</p>"; } ?>
		<p>
			<label for="icon_title">Title:</label>
			<input type="text" id="icon_title" name="icon_title" value="<?php echo $title; ?>" class="field" />
		</p>
		<input type="hidden" name="MAX_FILE_SIZE" value="10240000" />
		<?php if(isset($errors['image'])){ echo "<p class='error'>".$errors['image']."</p>";} ?>
		<p>
			<input type="hidden" name="cur_image" value="<?php echo $image; ?>" />
			<label><strong>Image:</strong></label>
			<span class="other"><input type="file" name="new_image" id="new_image" />
			<?php if ($image!=''): ?>
			<span class="icon_image">
				<img src="<?php echo IMAGE_WEB_PATH.$image; ?>" /><br />
				<input type="checkbox" name="delete_image" value="delete" /> Delete this image
			</span>
			<?php endif ?>
			</span>
		</p>
		<?php if(isset($errors['overimage'])) { echo "<p class='error'>".$errors['overimage']."</p>"; } ?>
		<p>
			<input type="hidden" name="cur_over_image" value="<?php echo $over_image; ?>" />
			<label><strong>Over Image:</strong></label>
			<span class="other"><input type="file" name="new_over_image" id="new_over_image" />
			<?php if ($over_image!=''): ?>
			<span class="icon_image">
				<img src="<?php echo IMAGE_WEB_PATH.$over_image; ?>" /><br />
				<input type="checkbox" name="delete_over_image" value="delete" /> Delete this image
			</span>
			<?php endif ?>
			</span>
		</p>
		<?php if(isset($errors['width'])) { echo "<p class='error'>".$errors['width']."</p>"; } ?>
		<p>
			<label for="icon_width">Width:</label>
			<input type="text" id="icon_width" name="icon_width" value="<?php echo $width; ?>" class="field" />
		</p>
		<?php if(isset($errors['height'])) { echo "<p class='error'>".$errors['height']."</p>"; }?>
		<p>
			<label for="icon_height">Height:</label>
			<input type="text" id="icon_height" name="icon_height" value="<?php echo $height; ?>" class="field" />
		</p>
		<?php if(isset($errors['hotspot'])) { echo "<p class='error'>".$errors['hotspot']."</p>"; }?>
		<div>
			<label for="icon_hotspot">Hotspot:</label>
			<input type="hidden" id="icon_hotspot" name="icon_hotspot" value="<?php echo $hotspot; ?>" class="field" />
			<div class="canvasfield">
				<div id="canvas-cover"></div>
				<img src="<?php echo IMAGE_WEB_PATH.$image; ?>" style="background:#000; width:<?php echo $width*4; ?>px; height:<?php echo $height*4; ?>px" id="imageMapIt"/>
				<div id="buttonSection"></div>
				<script src="behaviour/underscore-min.js"></script>
				<script src="behaviour/fabric.js"></script>
				<script src="behaviour/imageMapGenerator.js"></script>
				<script type="text/javascript">
					$('#icon_width').blur(function() {
						$a = $(this);
						$a.val($a.val().replace(/\D*/,''));
						$('#imageMapIt').width($a.val()*4);
						setupImageMapGenerator('#canvas-cover', '#imageMapIt', '#icon_hotspot', 'polygon', '#buttonSection', 4);
					});
					$('#icon_height').blur(function() {
						$a = $(this);
						$a.val($a.val().replace(/\D*/,''));
						$('#imageMapIt').height($a.val()*4);
						setupImageMapGenerator('#canvas-cover', '#imageMapIt', '#icon_hotspot', 'polygon', '#buttonSection', 4);
					});
					setupImageMapGenerator('#canvas-cover', '#imageMapIt', '#icon_hotspot', 'polygon', '#buttonSection', 4);
				</script>
			</div>
		</div>
		<p>
		<input type="hidden" name="icon_id" id="icon_id" value="<?php echo $icon_id; ?>" />
		<input type="button" id="btnBack" name="btnCancel" value="Back" onclick="window.location.href='points-of-interest/icon_index.php'" /><input type="submit" id="btnAction" name="btnAction" value="Save" />
		</p>
	</form>

<?php echo $_footer; ?>
