<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_dates.class.php';
require_once 'library/_page.class.php';

// values for
// WEB_ROOT & $CSS needed

$errors = array();
$area_id = safeAddSlashes($_POST['area_id']);
$area_name = safeAddSlashes($_POST['area_name']);
$area_coords = safeAddSlashes($_POST['area_coords']);
$area_expanded = safeAddSlashes($_POST['area_expanded']);

if (isset($_POST['area_id'])) {
	
	
	if($area_name == "") { $errors['title'] = "Please enter a area name"; }
	
	if(count($errors) == 0) {
		if ($area_id==-1){
			// new post - create
		

			$sql = "INSERT INTO tbl_maparea SET
					area_name = '$area_name',
					area_coords = '$area_coords',
					area_expanded = '$area_expanded'";
			$result = getQuery($sql, 'Error creating new item:');
			$area_id = mysql_insert_id();


		// redirect to edit page will success text

		header('location:'.$_SERVER['PHP_SELF'].'?action=itemcreated&area_id='.$area_id);
		exit();

		} else {
			// existing post - update

			$poi_title = $_POST['poi_title'];
			$poi_title = safeAddSlashes($poi_title);

			$poi_content = $_POST['poi_content'];
			$poi_content = safeAddSlashes($poi_content);

			$sql = "UPDATE tbl_maparea SET
					area_name = '$area_name',
					area_coords = '$area_coords',
					area_expanded = '$area_expanded'
					WHERE area_id = '$area_id'";

			$result = getQuery($sql, 'Error updating point of interest:');

			header('location:'.$_SERVER['PHP_SELF'].'?action=itemedited&area_id='.$area_id);
			exit();
		}
	}

}

if (isset($_GET['new'])){
	// new file so don''t load from database
	$area_id = -1;
	$area_name = "";
	$area_coords = "[new google.maps.LatLng(54.30210131512976,-2.7789615000000367),new google.maps.LatLng(54.347351843378284,-2.7782753623048393),new google.maps.LatLng(54.34735231433352,-2.699997356445351),new google.maps.LatLng(54.30250226423789,-2.700684114257797)]";
	$area_expanded = "[new google.maps.LatLng(54.29007959526996,-2.678024610351599),new google.maps.LatLng(54.29048059443989,-2.799561373047027),new google.maps.LatLng(54.35935730978086,-2.798874309570351),new google.maps.LatLng(54.36055721251987,-2.675964875976547)]";
} else {
	// file exists already so load content from database
	$area_id = $_GET['area_id'];

	$sql = "SELECT *
			FROM tbl_maparea
			WHERE area_id = '$area_id'";

	$post = getQuery($sql, 'Could not get coverage area: ');

	$post = mysql_fetch_array($post);
	$area_name = htmlspecialchars($post['area_name']);
	$area_coords = $post['area_coords'];
	$area_expanded = $post['area_expanded'];
}

$action = $_GET['action'];

switch ($action){
	case 'itemcreated':
		$action_text = "<p>New Coverage Area Added</p>";
		break;
	case 'itemedited':
		$action_text = "<p>Coverage Area Saved</p>";
		break;
}

$base_href = WEB_ROOT;
$css = CSS;

$page = new Page($menus);

$page->addScript($script);

$_header = $page->getHeader();
$_footer = $page->getFooter();

echo $_header; ?>
<h2><?php echo isset($_GET['new'])?"Create":"Edit"?> Coverage Area</h2>

	<?php
	if ( $action_text ) {
		echo "<div id=\"user-notice\">";
		echo $action_text;
		echo "</div>";
	}
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="sck-form" onsubmit="onSubmitForm(this);">
		<p>
			<label for="area_name">Name:</label>
			<?php if(isset($errors['title'])){ echo "<p class='error'>".$errors['title']."</p>";}?>
			<input type="text" id="area_name" name="area_name" value="<?php echo $area_name; ?>" class="field" />
		</p>
		
		<legend>Map</legend>
			<input type="hidden" name="area_coords" id="area_coords" value="<?php echo str_replace('\n','',$area_coords); ?>" />
			<input type="hidden" name="area_expanded" id="area_expanded" value="<?php echo str_replace('\n','',$area_expanded); ?>" />

			<div id="map" class="submission" style="overflow:hidden; background:silver; height:500px;"></div>
			<!-- &amp;key=<?php echo MAPS_KEY; ?> -->
			<script src="http://maps.google.com/maps/api/js?v=3.1&amp;sensor=false" type="text/javascript"></script>
			<script type="text/javascript">
			// <![CDATA[
			var itemMarker;
			var map;
			var areapoly;
			var areapaths;
			var expandpoly;
			var expandpaths;
			var area = <?php echo $area_coords; ?>;
			var expanded = <?php echo $area_expanded; ?>;
			var changedelay;
			$(document).ready(function(){
				latlngbounds = new google.maps.LatLngBounds();
				map = new google.maps.Map(document.getElementById('map'), { mapTypeId:google.maps.MapTypeId.ROADMAP });
				for(var j = 0;j < area.length;j++) {
					latlngbounds.extend(area[j]);
				}
				
				expandpoly = new google.maps.Polygon({
					paths: expanded,
					strokeColor: '#ffffff',
					strokeOpacity: 0.6,
					strokeWeight: 5,
					fillColor: '#ffffff',
					fillOpacity: 0.35,
					editable:true,
					draggable:true
				});
				expandpoly.setMap(map);
				expandpaths = expandpoly.getPaths();
				for(var i = 0; i < expandpaths.getLength(); i++) {
					var path = expandpaths.getAt(i);
					google.maps.event.addListener(path, 'set_at', function() {
						if(changedelay) {
							clearTimeout(changedelay)
						}
						changedelay = setTimeout(function() {
							setArrayValues("area_expanded",expandpaths);
						},100);
					});
				}
				
				areapoly = new google.maps.Polygon({
					paths: area,
					strokeColor: '#14b9d6',
					strokeOpacity: 0.6,
					strokeWeight: 5,
					fillColor: '#14b9d6',
					fillOpacity: 0.35,
					editable:true,
					draggable:true
				});
				areapoly.setMap(map);
				areapaths = areapoly.getPaths();
				
				for(var i = 0; i < areapaths.getLength(); i++) {
					var path = areapaths.getAt(i);
					google.maps.event.addListener(path, 'set_at', function() {
						if(changedelay) {
							clearTimeout(changedelay)
						}
						changedelay = setTimeout(function() {
							setArrayValues("area_coords",areapaths);
						},100);
					});
				}
				
				map.fitBounds(latlngbounds);
			});
			
			function setArrayValues(id,paths) {
				var inp = document.getElementById(id);
				var newval = '[';
				var first = true;
				for(var i = 0; i < paths.getLength(); i++) {
					var path = paths.getAt(i);
					for(var j = 0; j < path.getLength(); j++) {
						var point = path.getAt(j);
						if(first) { first = false; } else { newval += ","; };
						newval += "new google.maps.LatLng("+point.lat()+","+point.lng()+")";
					}
				}
				newval += ']';
				inp.value = newval;
			}

			function onSubmitForm(form){
				return true;
			}
			// ]]>
			</script>
			<div class="clear">&nbsp;</div>
		</fieldset>
		
		<p>
		<input type="hidden" name="area_id" id="area_id" value="<?php echo $area_id; ?>" />
		<input type="button" id="btnBack" name="btnCancel" value="Back" onclick="window.location.href='coverage/index.php'" /><input type="submit" id="btnAction" name="btnAction" value="Save" />
		</p>
	</form>

<?php echo $_footer; ?>
