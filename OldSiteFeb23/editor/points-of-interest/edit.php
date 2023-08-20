<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_dates.class.php';
require_once 'library/_page.class.php';
require_once 'points-of-interest/_poi.config.php';

// values for
// WEB_ROOT & $CSS needed

$errors = array();
$poi_content = safeAddSlashes($_POST['poi_content']);

if (isset($_POST['poi_id'])) {

	// if poi_id is set, update,
	// else create new post and assign poi_id to it
	$poi_id = $_POST['poi_id'];
	$poi_title = $_POST['poi_title'];
	$poi_content = $_POST['poi_content'];
	$poi_latitude = $_POST['poi_latitude'];
	$poi_longitude = $_POST['poi_longitude'];
	$poi_icon = $_POST['poi_icon'];
	
	if($poi_title == "") { $errors['title'] = "Please enter a title"; }
	if($poi_icon == 0) { $errors['icon'] = "Please choose an icon (you may need to make one first"; }
	
	if(count($errors) == 0) {
		if ($poi_id==-1){
			// new post - create

			$poi_title = $_POST['poi_title'];
			$poi_title = safeAddSlashes($poi_title);

			$dateValidator = new dateValidator();
			$news_date = $dateValidator->getPOSTDate('news_date');

			$poi_content = $_POST['poi_content'];
			$poi_content = safeAddSlashes($poi_content);
			

			$sql = "INSERT INTO tbl_poi SET
					poi_title = '$poi_title',
					poi_content = '$poi_content',
					poi_latitude = $poi_latitude,
					poi_longitude = $poi_longitude,
					poi_icon = '$poi_icon'";
			$result = getQuery($sql, 'Error creating new item:');
			$poi_id = mysql_insert_id();


		// redirect to edit page will success text

		header('location:'.$_SERVER['PHP_SELF'].'?action=itemcreated&poi_id='.$poi_id);
		exit();

		} else {
			// existing post - update

			$poi_title = $_POST['poi_title'];
			$poi_title = safeAddSlashes($poi_title);

			$poi_content = $_POST['poi_content'];
			$poi_content = safeAddSlashes($poi_content);

			$sql = "UPDATE tbl_poi SET
					poi_title = '$poi_title',
					poi_content = '$poi_content',
					poi_latitude = $poi_latitude,
					poi_longitude = $poi_longitude,
					poi_icon = '$poi_icon'
					WHERE poi_id = '$poi_id'";

			$result = getQuery($sql, 'Error updating point of interest:');

			header('location:'.$_SERVER['PHP_SELF'].'?action=itemedited&poi_id='.$poi_id);
			exit();
		}
	}

}

if (isset($_GET['new'])){
	// new file so don''t load from database
	$poi_id = -1;
	$poi_title = "";
	$poi_latitude = 54.355356;
	$poi_longitude = -2.934837;
	$poi_content = "";
	$poi_icon = 0;
} else {
	// file exists already so load content from database
	$poi_id = $_GET['poi_id'];

	$sql = "SELECT *
			FROM tbl_poi
			WHERE poi_id = '$poi_id'";

	$post = getQuery($sql, 'Could not get point of interest: ');

	$post = mysql_fetch_array($post);
	$poi_title = htmlspecialchars($post['poi_title']);
	$poi_content = htmlspecialchars($post['poi_content']);
	$poi_latitude = $post['poi_latitude'];
	$poi_longitude = $post['poi_longitude'];
	$poi_icon = $post['poi_icon'];
}

$action = $_GET['action'];

switch ($action){
	case 'itemcreated':
		$action_text = "<p>New Point of Interest Added</p>";
		break;
	case 'itemedited':
		$action_text = "<p>Point of Interest Saved</p>";
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
		elements : "poi_content",
		theme : "advanced",
		relative_urls : true, // Default value
		document_base_url : '$base_href',
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,embed_video_link,template",

		// Theme options
		theme_advanced_buttons1 : "newdocument,bold,italic,underline,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,template,cut,copy,paste,pastetext,pasteword,search,replace,bullist,numlist,outdent,indent,blockquote",
		theme_advanced_buttons2 : "undo,redo,link,unlink,anchor,image,embed_video_link,cleanup,help,code,tablecontrols,hr,removeformat,visualaid,sub,sup,charmap,fullscreen",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",

		width : "785",		

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

$_header = $page->getHeader();
$_footer = $page->getFooter();

echo $_header; ?>
<h2><?php echo isset($_GET['new'])?"Create":"Edit"?> Point of Interest</h2>

	<?php
	if ( $action_text ) {
		echo "<div id=\"user-notice\">";
		echo $action_text;
		echo "</div>";
	}
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="sck-form" onsubmit="onSubmitForm(this);">
		<p>
			<label for="poi_title">Title:</label>
			<?php if(isset($errors['title'])){ echo "<p class='error'>".$errors['title']."</p>";}?>
			<input type="text" id="poi_title" name="poi_title" value="<?php echo $poi_title; ?>" class="field" />
		</p>
		<p>
			<label for="poi_content">Content:</label>
			<textarea name="poi_content" id="poi_content"><?php echo $poi_content; ?></textarea>
		</p>
		
		<div class="field">
			<?php if(isset($errors['icon'])) { echo "<p class='error'>".$errors['icon']."</p>"; }?>
			<div class="field-label">
			<label for="poi_icon" class="field-required">Icon</label>
			</div>
			<div class="field-input">
			<select id="poi_icon" name="poi_icon">
				<option value="">-- Please Select --</option>
			<?php
			$sql = "SELECT icon_id, icon_title FROM tbl_poi_icons";
			$result = getQuery($sql);
			while($row = mysql_fetch_array($result)){
				$icon_id = $row['icon_id'];
				$icon_title = htmlspecialchars($row['icon_title']);
				if($icon_id==$poi_icon){
					echo "<option value=\"$icon_id\" selected=\"selected\">$icon_title</option>";
				} else {
					echo "<option value=\"$icon_id\">$icon_title</option>";
				}
			}
			?>
			</select>
			</div>
		</div>
		<div class="clear"></div>
		<fieldset class="fullwidth">
		<legend>Location</legend>
		<div class="p">
			<p>Drag the cursor to the accurate location. Zoom in to pin-point your business exactly:</p>
			<input type="hidden" name="poi_latitude" id="poi_latitude" value="<?php echo $poi_latitude; ?>" />
			<input type="hidden" name="poi_longitude" id="poi_longitude" value="<?php echo $poi_longitude; ?>" />

			<div id="map" class="other submission" style="overflow:hidden; background:silver; height:400px;"></div>
			<!-- &amp;key=<?php echo MAPS_KEY; ?> -->
			<script src="http://maps.google.com/maps/api/js?v=3.1&amp;sensor=false" type="text/javascript"></script>
			<script type="text/javascript">
			// <![CDATA[
			var itemMarker;
			var map;
			$(document).ready(function(){
				var myOptions = {
				  center: new google.maps.LatLng(<?php echo $poi_latitude; ?>,<?php echo $poi_longitude; ?>),
				  zoom:7,
				  mapTypeId:google.maps.MapTypeId.ROADMAP
				};
				var mapdiv = document.getElementById("map");
				map = new google.maps.Map(mapdiv, myOptions);
				
				itemMarker = new google.maps.Marker({
					map: map,
					position:myOptions.center,
					draggable:true
				});
			});

			function onSubmitForm(form){
				var position = itemMarker.getPosition();
				$("#poi_latitude").attr("value", position.lat());
				$("#poi_longitude").attr("value", position.lng());
				return true;
			}
			// ]]>
			</script>
			<div class="clear">&nbsp;</div>
		</div>
		</fieldset>
		
		<p>
		<input type="hidden" name="poi_id" id="poi_id" value="<?php echo $poi_id; ?>" />
		<input type="button" id="btnBack" name="btnCancel" value="Back" onclick="window.location.href='points-of-interest/index.php'" /><input type="submit" id="btnAction" name="btnAction" value="Save" />
		</p>
	</form>

<?php echo $_footer; ?>
