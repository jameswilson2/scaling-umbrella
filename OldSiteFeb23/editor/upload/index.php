<?php
require_once 'library/security/_secure.inc.php';
require_once 'upload/_upload.config.php';
require_once 'library/_page.class.php';

$files = array();

foreach(scandir(UPLOAD_PATH) as $filename){
	$full_filename = UPLOAD_PATH . $filename;
	if(is_file($full_filename)){
		
		$path_parts = pathinfo($full_filename);
		$stats = stat($full_filename);
		
		$files[] = array(
			"basename" => $path_parts["basename"],
			"extension" => strtolower($path_parts["extension"]),
			"path_parts" => $path_parts,
			"filesize" => $stats["size"],
			"last_modified" => $stats["mtime"],
			"stats" => $stats
		);
	}
}

function compare_field($a, $b, $field_name){
	if($a[$field_name] === $b[$field_name]){
		return 0;
	}
	return ($a[$field_name] < $b[$field_name] ? -1 : 1);
}

function compare_filename($a, $b){
	return compare_field($a, $b, "basename");
}

function compare_type($a, $b){
	return compare_field($a, $b, "extension");
}

function compare_filesize($a, $b){
	return compare_field($a, $b, "filesize");
}

function compare_last_modified($a, $b){
	return compare_field($a, $b, "last_modified");
}

if(isset($_GET["order_by"])){
	$order_by = $_GET["order_by"];
}
else{
	$order_by = "last_modified";
}

if(isset($_GET["order"])){
	$order = $_GET["order"];
}
else{
	$order = "desc";
}

switch($order_by){
	case "type":
		$compare_function = "compare_type";
		break;
	
	case "filesize":
		$compare_function = "compare_filesize";
		break;
	
	case "last_modified":
		$compare_function = "compare_last_modified";
		break;
	
	case "filename":
	default:
		$compare_function = "compare_filename";
		$order_by = "filename";
		break;
}

if(isset($compare_function)){
	
	usort($files, $compare_function);
	
	if($order === "desc"){
		$files = array_reverse($files);
	}
}

function render_header_link($name, $label){
	
	global $order_by;
	global $order;
	
	$url = Url::parseFromRequest();
	
	$url->setQueryVar("order", ($order == "asc" ? "desc" : "asc"));
	$url->setQueryVar("order_by", $name);
	
	if($name == $order_by){
		$sort_icon_class = ($order == "asc" ? "order-asc-icon" : "order-desc-icon");
		$sort_icon_html = "<span class=\"$sort_icon_class\">&nbsp;</span>";
	}
	else{
		$sort_icon_html = "";
	}
	
	$label_html = htmlentities($label);
	$url_html = htmlentities($url->render());
	
	return "$sort_icon_html<a href=\"$url_html\">$label_html</a>";;
}

function render_file_icon($file){
	
	switch($file["extension"]){
		case "jpg":
		case "gif":
		case "png":
			$class = "image-x-generic";
			break;
		case "html":
			$class = "text-html";
			break;
		case "zip":
			$class = "package-x-generic";
			break;
		case "doc":
		case "docx":
		case "odt":
			$class = "x-office-document";
			break;
		case "mpg":
		case "avi":
		case "flv":
		case "mp4":
		case "wmv":
			$class = "video-x-generic";
			break;
		case "xls":
		case "ods":
			$class = "x-office-spreadsheet";
			break;
		case "ppt":
			$class = "x-office-presentation";
			break;
		case "exe":
			$class = "application-x-exe";
			break;
		case "mp3":
		case "ogg":
		case "wav":
			$class = "audio-x-generic";
			break;
		default:
			$class = "text-x-generic";
	}
	
	return "<span class=\"file-icon file-icon-{$class}\">&nbsp;</span>";
}

$page = new Page($menus);
$header = $page->getHeader();
$footer = $page->getFooter();
echo $header; 
?>
<h1><?php echo SITE_NAME; ?> - Upload Files</h1>
<p>Your files are located in the <strong><?php echo WEB_ROOT.UPLOAD_FOLDER; ?></strong> folder on your website.</p>

<?php
if (isset($_SESSION["action_flash"])){
	
	$action = $_SESSION["action_flash"];
	unset($_SESSION["action_flash"]);
	
	echo "<div id=\"user-notice\">";
	switch ($action) {
		case 'success':
			$file = $_GET['filename'];
			$location = UPLOAD_FOLDER.$file;
			echo "<p>Your file was uploaded successfully to: <strong>$location</strong> (<a href=\"javascript:copy(document.getElementById('user-notice-url').value);\">copy</a>/<a href=\"javascript:userNoticeHide();\">hide</a>)</p>";
			echo "<form action=\"#\"><input type=\"hidden\" id=\"user-notice-url\" value=\"$location\" /></form>";
			break;

		case 'delete':
			echo "<p>Your file has been deleted!</p>";
			break;
	}
	echo "</div>";
}
?>

<script type="text/javascript">
//<![CDATA[
	ZeroClipboard.setMoviePath('<?php echo WEB_ROOT . 'editor/zeroclipboard/ZeroClipboard.swf';?>');
//]]>
</script>

<script type="text/javascript">
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
</script>

<div id="newitems">
	<p><img src="presentation/file_normal.gif" alt="Upload New File" width="16" height="14" title="Upload New File" class="minicon" /> <strong><a href="javascript:;" onclick="expand('newbox')">Upload a File</a></strong></p>
	<div id="newbox" class="hide">
		<div class="newitem">
			<form action="upload/process.php" method="post" enctype="multipart/form-data">
			<p><strong>File:</strong> <input type="file" name="newFile" id="newFile" />
			<input type="submit" id="createNewFile" name="createNewFile" value="Upload File" onclick="this.form.submit();this.disabled=true;this.value='Uploading please wait!';" /></p>
			</form>
		</div>
	</div>
</div>

<table border="0" cellpadding="0" cellspacing="0" class="table files">
<tr class="rowstrong">
	<td><?php echo render_header_link("filename", "Filename");?></td>
	<td><?php echo render_header_link("type", "Type");?></td>
	<td><?php echo render_header_link("last_modified", "Last Modified");?></td>
	<td><?php echo render_header_link("filesize", "File Size");?></td>
	<td></td>
</tr>
<?php
foreach($files as $file){

	$url = WEB_ROOT.UPLOAD_FOLDER.$file["basename"];
	$url_html = htmlentities($url);
	
	$last_modified_html = htmlentities(date("m/d/Y H:i:s", $file["stats"]["mtime"]));
	
	$file_size = $file["stats"]["size"];
	
	if($file_size >= 1024*1024){
		$file_size_html = htmlentities(round($file_size / (1024*1024), 2)) . " MB";
	}
	else{
		$file_size_html = htmlentities(round($file_size / 1024, 2)) . " KB";
	}
	
	$type_html = htmlentities(strtoupper($file["path_parts"]["extension"]));
	
	$name = $file["basename"];
	$name_html = htmlentities($name);
	$name_url_html = htmlentities(urlencode($name));
	
	$file_icon_html = render_file_icon($file);
	
echo <<<EOD
<tr class="row">
	<td class="file-link-column">$file_icon_html <a href="$url_html" class="file-url">$name_html</a></td>
	<td>$type_html</td>
	<td>$last_modified_html</td>
	<td>$file_size_html</td>
	<td><a onclick="return confirm('Are you sure you want to delete <?php echo $name_html; ?>?');" href="upload/delete.php?file=$name_url_html"><img src="presentation/delete.gif" onmouseover="this.src='presentation/delete_hover.gif'" onmouseout="this.src='presentation/delete.gif'" title="Delete" alt="Delete" class="actionimg" width="59" height="14" /></a></td>
</tr>
EOD;
}
?>
</table>

<script type="text/javascript">
(function(){
	
	var clip = new ZeroClipboard.Client();
	var count = 0;
	var active;
	
	function showCopyLocation(){
		
		if(active){
			$(active).remove();
			active = null;
		}
		
		var container = document.createElement("div");
		container.className = "copy-location";
		
		var button = document.createElement("div");
		button.className = "copy-location-button";
		
		var url = $(".file-url", this).attr("href");
		
		button.onmouseover = function(){
			clip.glue(button);
			clip.setText(url);
			clip.setHandCursor(true);
		}
		
		container.appendChild(button);
		$(this).append(container);
		
		active = container;
	}
	
	$("table.files .file-link-column").hover(showCopyLocation, $.noop);
})();

</script>

<?php echo $footer; ?>