<?php
require_once 'library/security/_secure.inc.php';
require_once 'library/_dates.class.php';
require_once 'library/_page.class.php';

// values for
// WEB_ROOT & $CSS needed

$news_summary = safeAddSlashes($_POST['news_summary']);
$news_status = $_POST['news_status'];
if ($news_status!='Active'){
	$news_status='Inactive';
}

if (isset($_POST["elm1"])) {

	// if nid is set, update,
	// else create new post and assign nid to it
	$nid = $_POST['nid'];
	if ($nid==-1){
		// new post - create

		$news_title = $_POST['news_title'];
		$news_title = safeAddSlashes($news_title);

		$dateValidator = new dateValidator();
		$news_date = $dateValidator->getPOSTDate('news_date');

		$news_content = $_POST['elm1'];
		$news_content = safeAddSlashes($news_content);

		$sql = "INSERT INTO tbl_news SET
				news_title = '$news_title',
				news_summary = '$news_summary',
				news_content = '$news_content',
				news_status = '$news_status',
				news_date = '$news_date'";
		$result = getQuery($sql, 'Error creating new item:');
		$nid = mysql_insert_id();


	// redirect to edit page will success text

	header('location:'.$_SERVER['PHP_SELF'].'?action=newnews&nid='.$nid);
	exit();

	} else {
		// existing post - update

		$news_title = $_POST['news_title'];
		$news_title = safeAddSlashes($news_title);

		$dateValidator = new dateValidator();
		$news_date = $dateValidator->getPOSTDate('news_date');

		$news_content = $_POST['elm1'];
		$news_content = safeAddSlashes($news_content);

		$sql = "UPDATE tbl_news SET
				news_title = '$news_title',
				news_summary = '$news_summary',
				news_content = '$news_content',
				news_status = '$news_status',
				news_date = '$news_date'
				WHERE news_id = '$nid'";

		$result = getQuery($sql, 'Error updating news:');

		header('location:'.$_SERVER['PHP_SELF'].'?action=updatenews&nid='.$nid);
		exit();
	}


}

if (isset($_GET['new'])){
	// new file so don''t load from database
	$nid = -1;
	$news_title = "";
	$news_summary = "";
	$news_content = "";
	$news_status = "";
	$news_date = date('Y-m-d');

} else {
	// file exists already so load content from database
	$nid = $_GET['nid'];

	$sql = "SELECT news_title, news_summary, news_content, news_date, news_status
			FROM tbl_news
			WHERE news_id = '$nid'";

	$post = getQuery($sql, 'Could not get news item: ');

	$post = mysql_fetch_array($post);
	$news_title = htmlspecialchars($post['news_title']);
	$news_summary = htmlspecialchars($post['news_summary']);
	$news_content = $post['news_content'];
	$news_status = $post['news_status'];
	$news_date = $post['news_date'];

}

$action = $_GET['action'];

switch ($action){
	case 'updatenews':
		$action_text = "<p>News item updated successfully!</p>";
		break;

	case 'newnews':
		$action_text = "<p>New news item saved successfully!</p>";
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
		relative_urls : true, // Default value
		document_base_url : '$base_href',
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

		// Theme options
		theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,|,template",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,|,fullscreen",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",

		// Example content CSS (should be your site CSS)
		content_css : "$css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "$base_href/editor/files/lists/template_list.js",
		external_link_list_url : "$base_href/editor/files/lists/link_list.js",
		external_image_list_url : "$base_href/editor/files/lists/image_list.js",
		media_external_list_url : "$base_href/editor/files/lists/media_list.js",


	});
</script>
<!-- /TinyMCE -->
EOD;

$page = new Page($menus);

$page->addScript($script);

$_header = $page->getHeader();
$_footer = $page->getFooter();

echo $_header; ?>
<h2>Edit News Item</h2>

	<?php
	if ( $action_text ) {
		echo "<div id=\"user-notice\">";
		echo $action_text;
		echo "</div>";
	?>
	<?php } ?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="sck-form">
		<p>
			<label for="news_title">Title:</label>
			<input type="text" id="news_title" name="news_title" value="<?php echo $news_title; ?>" class="field" />
		</p>
		<p>
			<label for="news_summary">Summary:</label>
			<textarea name="news_summary" id="news_summary"><?php echo $news_summary; ?></textarea>
		</p>
		<p>
			<textarea name="elm1" id="elm1" cols="60" rows="15"><?php echo htmlentities($news_content); ?></textarea>
		</p>
		<?php
			$picker = new datePicker();
			$picker->setStartYear(1);
			$picker->setEndYear(1);
			$picker->setCurrentDate($news_date);
			$picker->setFieldName('news_date');
		?>
		<p>
			<label for="news_date">Date:</label>
			<span class="other"><?php echo $picker->getDateSelector(); ?></span>
		</p>
		<p>
			<label for="news_status">Active:</label>
			<?php if ($news_status=='Active'): ?>
				<span class="other"><input type="checkbox" name="news_status" id="news_status" value="Active" checked="checked" /></span>
			<?php else: ?>
				<span class="other"><input type="checkbox" name="news_status" id="news_status" value="Active" /></span>
			<?php endif; ?>
		</p>
		<p>
		<input type="hidden" name="nid" id="nid" value="<?php echo $nid; ?>" />
		<input type="button" id="btnBack" name="btnCancel" value="Back" onclick="window.location.href='news/index.php'" /><input type="submit" id="btnAction" name="btnAction" value="Save" />
		</p>
	</form>

<?php echo $_footer; ?>
