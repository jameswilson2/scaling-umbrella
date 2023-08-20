<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_page.class.php';
require_once 'library/_extras_dbconn.inc.php';

// values for
// WEB_ROOT & $CSS needed

$newsletter_title = safeAddSlashes($_POST['newsletter_title']);
$newsletter_content = safeAddSlashes($_POST['elm1']);

if (isset($_POST["elm1"])) {

	// if nid is set, update,
	// else create new post and assign nid to it
	$nid = $_POST['nid'];
	if ($nid==-1){
		// new post - create

		$sql = "INSERT INTO tbl_newsletter SET
				newsletter_title = '$newsletter_title',
				newsletter_content = '$newsletter_content',
				newsletter_status = 'draft',
				newsletter_date = NOW()";
		$result = @mysql_query($sql);
		if (!$result) {
			echo "<p>Error creating new newsletter: " .
			mysql_error() . '</p>';
		}
		$nid = mysql_insert_id();


	// redirect to edit page will success text

	header('location:'.$_SERVER['PHP_SELF'].'?action=newnewsletter&nid='.$nid);
	exit();

	} else {
		// existing post - update

		$sql = "UPDATE tbl_newsletter SET
				newsletter_title = '$newsletter_title',
				newsletter_content = '$newsletter_content',
				newsletter_status = 'draft',
				newsletter_date = NOW()
				WHERE newsletter_id = '$nid'";

		$result = @mysql_query($sql);
		if (!$result) {
			echo "<p>Error updating newsletter: " .
			mysql_error() . '</p>';
		}


		header('location:'.$_SERVER['PHP_SELF'].'?action=updatenewsletter&nid='.$nid);
		exit();
	}


}

if (isset($_GET['new'])){
	// new file so don''t load from database
	$nid = -1;
	$newsletter_title = "";
	$newsletter_content = "";
} else {
	// file exists already so load content from database
	$nid = $_GET['nid'];

	$sql = "SELECT newsletter_title, newsletter_content
			FROM tbl_newsletter
			WHERE newsletter_id = '$nid'";

	$post = mysql_query($sql);

	$post = mysql_fetch_array($post);
	$newsletter_title = htmlspecialchars($post['newsletter_title']);
	$newsletter_content = $post['newsletter_content'];

}

$action = $_GET['action'];

switch ($action){
	case 'updatenewsletter':
		$action_text = "<p>Newsletter updated successfully!</p>";
		break;

	case 'newnewsletter':
		$action_text = "<p>New newsletter saved successfully!</p>";
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
		elements : "elm1",
		theme : "advanced",
		relative_urls : false, // Default value
		remove_script_host : false,
		document_base_url : '$base_href',
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,embed_video_link,template",

		// Theme options
		theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,|,template",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,embed_video_link,cleanup,help,code",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,|,fullscreen",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",

		// Example content CSS (should be your site CSS)
		content_css : "$css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "$base_href/editor/files/lists/template_list.js",
		external_link_list_url : "$base_href/editor/files/lists/link_list.js",
		external_image_list_url : "$base_href/editor/imagery/tiny_imagelist.php",
		media_external_list_url : "$base_href/editor/files/lists/media_list.js",
		
		width: "792"
 
	});
</script>
<!-- /TinyMCE -->
EOD;

$page_title = "Edit Newsletter";
$page_descr = "";
$page_keywo = "";

$page = new Page($menus);

$page->addScript($script);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>

<h2>Edit Newsletter</h2>

	<?php
	if ( $action_text ) {
		echo "<div id=\"user-notice\">";
		echo $action_text;
		echo "</div>";
	?>
	<?php } ?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="sck-form">
		<p>
			<label for="newsletter_title">Title:</label>
			<input type="text" id="newsletter_title" name="newsletter_title" value="<?php echo $newsletter_title; ?>" class="field" />
		</p>

			<textarea name="elm1" id="elm1" cols="60" rows="15"><?php echo htmlentities($newsletter_content); ?></textarea>

		<p>
		<input type="hidden" name="nid" id="nid" value="<?php echo $nid; ?>" />
		<input type="button" id="btnBack" name="btnCancel" value="Back" onclick="window.location.href='newsletter/index.php'" /><input type="submit" id="btnAction" name="btnAction" value="Save" />
		</p>
	</form>

<?php echo $footer; ?>
