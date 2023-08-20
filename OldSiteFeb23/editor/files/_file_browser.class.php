<?php
require_once 'files/tree.inc.php';
require_once 'files/_file_lists.inc.php';
require_once 'files/_file_builder.class.php';
require_once 'files/_file_rebuilder.class.php';

/* FileBrowser class
build list of pages
build directory tree
each file - check if allowed to edit/delete
each folder - check if allowed to edit/delete

combine config pages (new class?)

perform actions (new file/folder/delete)

*/ 

class FileBrowser {

	// start of tree for current user
	var $tree_start;

	// web root
	var $web_root;

	// server root
	var $server_root;

	// location, relative to $tree_start
	var $location = '';

	// current location, relative to root
	var $current_location;

	// combined array of diallowed pages - no editor access to this user
	var $disallowed_files;

	// combined array of disallowed directories - no editor access to this user
	var $disallowed_folders;

	// array of edit only pages
	var $no_delete;

	// array of edit only directories
	var $no_delete_folders;

	// up a level link
	var $up;

	// current location name
	var $location_name;

	// array of subfolders
	var $folder_list = array();

	// array of files
	var $file_list = array();


	// set up the initial variables - pick up from config and combine with user specific variables
	function FileBrowser(){
		$this->tree_start = $_SESSION['user_web_start'];
		$this->web_root = WEB_ROOT;
		$this->server_root = EDITABLE_ROOT;
		$this->location = $_GET['location'];
		$this->current_location = $this->tree_start.$this->location;

		// combine these with user specific arrays
		$this->disallowed_files = array_merge($GLOBALS["disallowed_files"], $_SESSION['user_disallowed_files']);
		$this->disallowed_folders = array_merge($GLOBALS["disallowed_folders"], $_SESSION['user_disallowed_folders']);
		$this->no_delete = $GLOBALS["no_delete"];
		$this->no_delete_folders = $GLOBALS["no_delete_folders"];

		$this->checkLocation();

		$this->getStatus();

		$this->getAction();

	}


	// check current location is allowed by current user
	function checkLocation(){
		if(!is_dir($this->server_root.$this->current_location)){
			if($this->location==''){
				echo "Permissions Problem!";
				exit;
			} else {
				header('location:'.WEB_ROOT.'editor/files/index.php');
				exit;
			}
		}
		foreach ($this->disallowed_folders as $dis) {
			if (preg_match("#^$dis#", $this->current_location)){
				header('location:'.WEB_ROOT.'editor/files/index.php');
				exit;
			}
		}
	}


	// get action from query string and respond accordingly
	// need method for each action
	function getAction(){
		switch($_GET['action']){
			case 'newfile':
				$this->createNewFile();
				break;

			case 'newfolder':
				$this->createNewFolder();
				break;

			case 'deletefile':
				$this->deleteFile();
				break;

			case 'deletefolder':
				$this->deleteFolder();
				break;

			case 'editfile':
				$this->editFile();
				break;

			case 'rebuild':
				$this->rebuild();
				break;

			default:
				break;
		}
	}


	// check if a Directory is allowed editor access by current user
	// noedit directories
	function isAllowedFolder($directory){
		$match = 0;
		foreach ($this->disallowed_folders as $dis) {
			if ($dis == $this->current_location.$directory){
				return FALSE;
			}
		}
		return TRUE;
	}


	// check if a page is allowed editor access by current user
	// noedit
	function isAllowedFile($file){
		// disallowed files
		foreach ($this->disallowed_files as $dis_file) {
			if ($dis_file == $this->current_location.$file ){
				return FALSE;
			}
		}

		return TRUE;
	}


	// check if a Directory is allowed to be deleted by current user
	// include nodelete and noedit folders + folders containing html files
	function isAllowedDeleteFolder($directory){
		$sublocation = $this->server_root.$this->current_location.$directory;
		$j = 0;
		$subdp = opendir($sublocation);
		$file_list = array();

		while (false !== ($file = readdir($subdp))) {
			if (is_dir($sublocation.$file) && $file!='.' && $file!='..'){
				// contains Directory - no deleting allowed
				$j++;
			}
		}

		if($j==0){
			foreach ($this->no_delete_folders as $dis) {
				if ($dis == $this->current_location.$directory.'/'){
					$j++;
				}
			}
		}

		if($j==0){
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function countFiles($directory){
		$sublocation = $this->server_root.$this->current_location.$directory.'/';
		$i=0;
		$subdp = opendir($sublocation);
		while (false !== ($file = readdir($subdp))) {
			if (is_file($sublocation.$file) && eregi('.html?$', $file)) {
				// is html page
				$i++;
			}
		}
		closedir($subdp);
		return $i;
	}


	// check if a page is allowed to be deleted by current user
	// noedit and nodelete
	function isAllowedDeleteFile($file){
		// no delete files
			foreach ($this->no_delete as $dis_file) {
			if ($dis_file == $this->current_location.$file ){
				return FALSE;
			}
		}

		return TRUE;

	}


	// create new page from POST data
	function createNewFile(){
		$newfile = $_POST['newFile'];
		$location = $this->server_root.$this->current_location;

		// check that filename only contains alphanumeric characters
		if (!ereg('^[0-9a-zA-Z-]+$', $newfile)){
			header('location:'.WEB_ROOT.'editor/files/index.php?location='.$this->location.'&status=newfilefail');
			exit;
		}

		$dp = opendir($location);
		// loop through the directory
		$file_list = Array();
		while (false !== ($entry = readdir($dp))) {
			if (is_file($location.$entry) && eregi('.html?$', $entry)) {
				// $entry is a htm or html page...
				$file_list[] = $entry;
			}
		}
		// Close directory
		closedir($dp);

		// check new filename against current list
		// must check if it''s the same with either extension
		foreach ($file_list as $file){
			if (strtolower($file)==strtolower($newfile)||strtolower($file)==strtolower($newfile.".htm")||strtolower($file)==strtolower($newfile.".html")){
				// page already exists
				header('location:'.WEB_ROOT.'editor/files/index.php?location='.$this->location.'&status=newfileexists');
				exit;
			}
		}

		// create new page

		$newfile = strtolower($newfile);

		$builder = new FileBuilder();

		$builder->setLocation($this->current_location);
		$builder->setFilename($newfile.'.htm');
		$builder->setTitle('Page Title');
		$builder->setDescription('Page Description');
		$builder->setKeywords('Page Keywords');
		$builder->setContent('<p>Content goes here!</p>');

		$builder->buildPage();

		header('location:'.WEB_ROOT.'editor/files/index.php?location='.$this->location.'&status=newfile');
		exit;
	}


	// create new Directory from POST data
	function createNewFolder(){

		$newfolder = $_POST['newFolder'];

		// open current directory
		$dp = opendir($this->server_root.$this->current_location);
		$dir_list = array();
		// loop through the directory
		while (false !== ($directory = readdir($dp))) {
			if (is_dir($this->server_root.$this->current_location . $directory)) {
				// $directory is a directory...
				$dir_list[] = $directory;
			}
		}
		// Close top level directory
		closedir($dp);

		// check new directory against current list

		foreach ($dir_list as $dir){
			if (strtolower($dir)==strtolower($newfolder)){
				// Directory already exists
				header('location:'.WEB_ROOT.'editor/files/index.php?location='.$this->location.'&status=newfolderexists');
				exit;
			}
		}

		// check that Directory only contains alphanumeric characters
		if (ereg('^[0-9a-zA-Z-]+$', $newfolder)){
			// create new Directory
			$newfolder = strtolower($newfolder);
			mkdir($this->server_root.$this->current_location.$newfolder);

			header('location:'.WEB_ROOT.'editor/files/index.php?location='.$this->location.'&status=newfolder');
			exit;
		}

		// fail
		header('location:'.WEB_ROOT.'editor/files/index.php?location='.$this->location.'&status=newfolderfail');
		exit;

	}


	// delete file from GET data
	function deleteFile(){

		$file = $_GET['file'];
		$filename = $this->server_root.$this->current_location.$file;

		if(!is_file($filename)){
			header('location:'.WEB_ROOT.'editor/files/index.php?location='.$this->location.'&status=deletefilefail');
			exit;
		}

		if(!$this->isAllowedFile($file) || !$this->isAllowedDeleteFile($file)){
			header('location:'.WEB_ROOT.'editor/files/index.php?location='.$this->location.'&status=deletefilefail');
			exit;
		}

		$deleted = $filename.'.deleted';
		// copy file to 'deleted' location
		$ok = copy($filename, $deleted);

		// delete current file
		@unlink($filename);

		header('location:'.WEB_ROOT.'editor/files/index.php?location='.$this->location.'&status=deleted');
		exit;

	}


	// delete Directory from GET data
	function deleteFolder(){
		$folder = $_GET['folder'];

		$backup = $this->server_root.'deleted/'.$folder;

		// check there are no htm or html pages in the Directory
		// check allowed to access this Directory
		// check Directory exists
		// check allowed to access this Directory

		if(!is_dir($this->server_root.$this->current_location.$folder)){
			header('location:'.WEB_ROOT.'editor/files/index.php?location='.$this->location.'&status=deletefolderfail');
			exit;
		}


		if (!$this->isAllowedDeleteFolder($folder) || !$this->isAllowedFolder($folder)){
			header('location:'.WEB_ROOT.'editor/files/index.php?location='.$this->location.'&status=deletefolderfail');
			exit;
		}

		$i = 0;
		$file_list = Array();
		$dp = opendir($this->server_root.$this->current_location.$folder);
			while (false !== ($file = readdir($dp))) {
			if (is_file($this->server_root.$this->current_location.$folder.$file)){
				$file_list[] = $file;
				if (eregi('.html?$', $file)) {
					// $file is a htm or html file...
					$i++;
				}
			}
		}
		closedir($dp);

		if ($i != 0){
			// pages still exist
			header('location:'.WEB_ROOT.'editor/files/index.php?location='.$this->location.'&status=deletefolderfail');
			exit;
		} else {
			// copy Directory and all contents to root/deleted/foldername
			// delete Directory and all contents
			@mkdir($this->server_root.'deleted/');
			@mkdir($backup);
			foreach ($file_list as $file){
				$ok = copy($this->server_root.$this->current_location.$folder.$file, $backup.$file);
				unlink($this->server_root.$this->current_location.$folder.$file);
			}
			rmdir($this->server_root.$this->current_location.$folder);

			header('location:'.WEB_ROOT.'editor/files/index.php?location='.$this->location.'&status=deletefoldersuccess');
			exit;

		}

	}


	// edit file - also save file if edited
	function editFile(){
		$file = $_GET['file'];
		if(!$this->isAllowedFile($file)){
			header('location:'.WEB_ROOT.'editor/files/index.php?location='.$this->location.'&status=editfilefail');
			exit;
		}

		// if POST set - save file

		if (isset($_POST["elm1"])) {
			$title = htmlspecialchars(stripslashes($_POST["title"]), ENT_QUOTES, "UTF-8");
			$description = htmlspecialchars(stripslashes($_POST["description"]), ENT_QUOTES, "UTF-8");
			$keywords = htmlspecialchars(stripslashes($_POST["keywords"]), ENT_QUOTES, "UTF-8");
			$content = $_POST["elm1"];

			$builder = new FileBuilder();

			$builder->setLocation($this->current_location);
			$builder->setFilename($file);
			$builder->setTitle($title);
			$builder->setDescription($description);
			$builder->setKeywords($keywords);
			$builder->setContent($content);

			$builder->buildPage();

		}

		// load file data

		$filecontent = file_get_contents($this->server_root.$this->current_location.$file);

		// get everything between content start and end tags
		// MUST CHECK IF CONTENT TAGS ARE PRESENT!!!
		if (!ereg('<!-- CONTENT START -->', $filecontent) || !ereg('<!-- CONTENT END -->', $filecontent)){
			header("Content-Type: text/plain");
			echo "The page is missing <!-- CONTENT START -->  <!-- CONTENT END --> tags!";
			exit;
			//header('location:'.WEB_ROOT.'editor/files/index.php?location='.$this->location.'&status=editfilefail');
		}

		list($header, $footer) = explode("<!-- CONTENT START -->", $filecontent);
		list($content, $footer) = explode("<!-- CONTENT END -->", $footer);
		list($rubbish, $title) = explode("<title>", $header);
		list($title, $rubbish) = explode("</title>", $title);
		list($rubbish, $description) = explode('<meta name="description" content="', $header);
		list($description, $rubbish) = explode('" />', $description);
		list($rubbish, $keywords) = explode('<meta name="keywords" content="', $header);
		list($keywords, $rubbish) = explode('" />', $keywords);

		// load edit form

		$urlencode_file = urlencode($file);
		$title = stripslashes($title);
		$description = stripslashes($description);
		$keywords = stripslashes($keywords);
		$content = htmlspecialchars($content, ENT_COMPAT, "UTF-8");

		$base_href = WEB_ROOT;
		$css = CSS;
		
		$back_link_url = WEB_ROOT . "editor/files/index.php?location=$this->location";
		
		$edit_form = <<<EOD
<!-- TinyMCE -->
<script type="text/javascript" src="files/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="files/lists/image_list.js"></script>
<script type="text/javascript" src="files/lists/link_list.js"></script>
<script type="text/javascript" src="files/lists/media_list.js"></script>
<script type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "exact",
		elements : "elm1",
		theme : "advanced",
		relative_urls : true, // Default value
		document_base_url : '$base_href',
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,embed_video_link,template",
		cleanup: false,
		
		// Theme options
		theme_advanced_buttons1 : "newdocument,bold,italic,underline,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,template,cut,copy,paste,pastetext,pasteword,search,replace,bullist,numlist,outdent,indent,blockquote",
		theme_advanced_buttons2 : "undo,redo,link,unlink,anchor,image,embed_video_link,cleanup,help,code,tablecontrols,hr,removeformat,visualaid,sub,sup,charmap,fullscreen",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",

		width : "792",
		height : "400",


		// Example content CSS (should be your site CSS)
		content_css : "$css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "$base_href/editor/files/lists/template_list.js?rand="+Math.random(),
		external_link_list_url : "$base_href/editor/files/lists/link_list.js?rand="+Math.random(),
		external_image_list_url : "$base_href/editor/imagery/tiny_imagelist.php?rand="+Math.random(),
		media_external_list_url : "$base_href/editor/files/lists/media_list.js?rand="+Math.random()

	});
</script>
<!-- /TinyMCE -->
			$this->status_div
			<div class="important">
				<div class="article-left">
					<p><strong>You are now editing file:</strong> <img src="presentation/file_normal.gif" alt="file icon" width="16" height="14" class="minicon" />$this->location$file</p>
				</div>
				<div class="article-rightalt">
					<p><input type="button" id="btnPBack" name="btnPBack" value="Back" onclick="window.location.href='$back_link_url'" /></p>
				</div>
				<div class="clear"></div>
				<p><small>Once you have updated the Title, Meta and Content save your page with the button at the bottom.</small></p>
			</div>

			<form action="files/index.php?action=editfile&amp;status=editsuccess&amp;location=$this->location&amp;file=$urlencode_file" method="post">
				<table width="100%" border="0" cellpadding="3" cellspacing="0" class="table">
					<tr>
						<td nowrap="nowrap"><strong>Title:</strong></td>
						<td><input type="text" name="title" id="title" value="$title" size="60" class="content_meta" /></td>
					</tr>
					<tr>
						<td nowrap="nowrap"><strong>Meta Description:</strong></td>
						<td><input type="text" name="description" id="description" value="$description" size="60" class="content_meta" /></td>
					</tr>
					<tr>
						<td nowrap="nowrap"><strong>Meta Keywords:</strong></td>
						<td><input type="text" name="keywords" id="keywords" value="$keywords" size="60" class="content_meta" /></td>
					</tr>
				</table>

				<p>Content:</p>
					<textarea name="elm1" id="elm1" cols="60" rows="15">$content</textarea>

				<div class="important">
					<div class="article-left">
						<input type="button" id="btnBack" name="btnBack" value="Back" onclick="window.location.href='$back_link_url'" />
					</div>
					<div class="article-rightalt">
						<input type="submit" id="btnAction" name="btnAction" value="Save this Page" />
					</div>
					<div class="clear"></div>
				</div>

			</form>
EOD;

		$this->content = $edit_form;
	}


	// rebuild website
	function rebuild(){
		$rebuilder = new Rebuilder();
		header('location:'.WEB_ROOT.'editor/files/index.php?location='.$this->location.'&status=rebuildsuccess');
		exit;
	}


	// get parent list and [up] link if required
	function getParents(){
		if($this->location != ''){
			$this->location_name = $this->current_location;
			$current = explode('/', $this->location);
			$level=count($current);
			$parent = '';

			for($i=0; $i<($level-2); $i++){
				$parent .= $current[$i].'/';
			}

			$this->up = $parent;

		} else {
			if($this->tree_start==''){
				$this->location_name = 'Root';
			} else {
				$this->location_name = $this->tree_start;
			}
			$this->up = '-';
		}
	}


	function getBreadcrumbs(){

		if($this->tree_start==''){
			$this->breadcrumbs[] = "<a href=\"files/index.php\">Root</a>";
		} else {
			$this->breadcrumbs[] = "<a href=\"files/index.php\">$this->tree_start</a>";
		}

		$current = explode('/', $this->location);
		$level=count($current);

		$parent = '';
		for($i=0; $i<($level-1); $i++){
			$parent .= $current[$i].'/';
			$this->breadcrumbs[] = "<a href=\"files/index.php?location=$parent\">{$current[$i]}</a>";
		}

		$this->breadcrumbs = implode('->', $this->breadcrumbs);

	}


	// get list of subfolders
	function getFolders(){
		// open current directory
		$dp = opendir($this->server_root.$this->current_location);
		$dir_list = array();
		// loop through the directory
		while (false !== ($directory = readdir($dp))) {
			if (is_dir($this->server_root.$this->current_location.$directory) && $this->isAllowedFolder($directory.'/') && $directory!='.' && $directory!='..') {
				// $directory is a directory...
				$dir_list[] = $directory;
			}
		}

		// Close top level directory
		closedir($dp);

		$dir_lowercase = array_map('strtolower', $dir_list);
		array_multisort($dir_lowercase, SORT_ASC, SORT_STRING, $dir_list);

		foreach ($dir_list as $dir){

			$i = $this->countFiles($dir);

			if($i==0 && $this->isAllowedDeleteFolder($dir.'/')){
				$this->folder_list[] = array('name'=>$dir, 'location'=>$this->location.$dir.'/', 'del'=>'Yes', 'files'=>$i);
			} else {
				$this->folder_list[] = array('name'=>$dir, 'location'=>$this->location.$dir.'/', 'del'=>'No', 'files'=>$i);
			}

		}

	}


	// get list of pages in the current Directory
	function getFiles(){

		// open current directory
		$dp = opendir($this->server_root.$this->current_location);

		// loop through the directory
		$file_list = Array();
		while (false !== ($entry = readdir($dp))) {
			if (is_file($this->server_root.$this->current_location.$entry) && eregi('.html?$', $entry)) {
				// $entry is a htm or html page...
				$file_list[] = $entry;
			}
		}
		// Close top level directory
		closedir($dp);

		$file_lowercase = array_map('strtolower', $file_list);
		array_multisort($file_lowercase, SORT_ASC, SORT_STRING, $file_list);

		foreach ($file_list as $file){

			$filesize = number_format(filesize($this->server_root.$this->current_location.$file)/1024, 2);
			$filedate = date ("j/n/y H:i:s", filemtime($this->server_root.$this->current_location.$file));

			$this->file_list[] = array('name'=>$file, 'allowed'=>$this->isAllowedFile($file), 'delete'=>$this->isAllowedDeleteFile($file), 'size'=>$filesize,'date'=>$filedate);

		}
	}


	// get status report
	function getStatus(){

		if($_GET['status']!=''){

			switch ($_GET['status']){
				case 'editfilefail':
					$status = "<p>Cannot edit this page.</p>";
					break;
				case 'editsuccess':
					$status = "<p>Page saved successfully.</p>";
					break;
				case 'rebuildsuccess':
					$status = "<p>Pages rebuilt successfully.</p>";
					break;
				case 'rebuildfail':
					$status =  "<p>Rebuild failed - Invalid template.</p>";
					break;
				case 'newfolder':
					$status =  "<p>New directory created successfully.</p>";
					break;
				case 'newfolderexists':
					$status = "<p>Cannot create new directory - a directory with this name already exists.</p>";
					break;
				case 'newfolderfail':
					$status = "<p>Cannot create a directory with this name.</p>";
					break;
				case 'newfile':
					$status = "<p>New page created successfully.</p>";
					break;
				case 'newfileexists':
					$status = "<p>Cannot create new page - a page with this name already exists.</p>";
					break;
				case 'newfilefail':
					$status = "<p>Cannot create a page with this name.</p>";
					break;
				case 'deleted':
					$status = "<p>Page deleted!</p>";
					break;
				case 'deletefilefail':
					$status = "<p>Cannot delete this page!</p>";
					break;
				case 'deletefolderfail':
					$status = "<p>Cannot delete this directory!</p>";
					break;
				case 'deletefoldersuccess':
					$status = "<p>Directory deleted!</p>";
					break;
			}

			$this->status_div = <<<EOD
	<div id="user-notice">
	$status
	</div>
	<script type="text/javascript">
	<!--
	$(document).ready(function() {
		$("#user-notice").hide();
		if ($("#user-notice").is(":hidden")) {
			$("#user-notice").slideDown("slow");
			$("#user-notice").animate( { backgroundColor: "#DC9E26" }, 600 );
			$("#user-notice").animate( { backgroundColor: "#2683DC" }, 2000 );
		}
	});
	//-->
</script>
EOD;

		}
	}


	// get new items div
	function getNewItems(){
		$tree = traverseDirTree($this->tree_start, '', 'files/index.php', null,'displayPath');
		if($this->tree_start==''){
			$tree_start_name = 'Root';
		} else {
			$tree_start_name = $this->tree_start;
		}
		$this->new_items = <<<EOD

<div id="newitems">

	<p><img src="presentation/folder_open.gif" alt="Browse Tree" width="16" height="14" title="Browse Tree" class="minicon" /> <strong><a href="javascript:;" onclick="expand('treeDiv')">Browse Page Tree</a></strong></p>

	<div id="treeDiv" class="hide">
		<div class="newitem">
			<ul>
				<li class=""><a href="files/index.php">$tree_start_name</a></li>
				$tree
			</ul>
		</div>
	</div>
	<p><img src="presentation/folder_open.gif" alt="New Directory" width="16" height="14" title="New Directory" class="minicon" /> <strong><a href="javascript:;" onclick="expand('newfolderDiv')">Create a new Directory</a></strong></p>

	<div id="newfolderDiv" class="hide">
		<div class="newitem">
			<form action="files/index.php?action=newfolder&amp;location=$this->location" method="post">
				<p><strong>New directory name:</strong> <input type="text" name="newFolder" id="newFolder" value="" /><input type="submit" id="createNewFolder" name="createNewFolder" value="Create New Directory" onclick="this.form.submit();this.disabled=true;this.value='Creating';" /></p>
			</form>
		</div>
	</div>
	<p><img src="presentation/file_normal.gif" alt="New Page" width="16" height="14" title="New Page" class="minicon" /> <strong><a href="javascript:;" onclick="expand('newfileDiv')">Create new page</a></strong></p>

	<div id="newfileDiv" class="hide">
		<div class="newitem">
				<form action="files/index.php?action=newfile&amp;location=$this->location" method="post">
				<p><strong>New filename:</strong> <input type="text" name="newFile" id="newFile" value="" />.htm
				<input type="submit" id="createNewFile" name="createNewFile" value="Create New Page" onclick="this.form.submit();this.disabled=true;this.value='Creating';" /></p>
				</form>
		</div>
	</div>
	<p><img src="presentation/rebuild.gif" alt="Rebuild" width="16" height="14" title="Rebuild" class="minicon" /> <strong><a href="files/index.php?action=rebuild&amp;location=$this->location">Apply Changes to Includes</a></strong></p>
	<p><img src="presentation/rebuild.gif" alt="Switch" width="16" height="14" title="Switch" class="minicon" /> <strong><a href="files/includes.php?location=$this->location">Switch to Include View</a></strong></p>
</div>
<p>Now browsing: $this->breadcrumbs</p>
EOD;

	}


	// render items for display
	// $up, $folders, $files
	function renderItems(){
		$this->getParents();
		$this->getBreadcrumbs();
		$this->getFolders();
		$this->getFiles();
		$this->getNewItems();

		// up
		if($this->up!='-'){
			$file_output .= <<<EOD
		<li><img src="presentation/folder_closed.gif" alt="directory" width="16" height="13" />
			<a class="folder" href="files/index.php?location=$this->up">[UP]</a></li>
EOD;

		}

		// folders
		foreach ($this->folder_list as $folder){
			if($folder['del']=='Yes'){
				$delete = "<a onclick=\"return confirm('Are you sure you want to delete the folder {$folder['name']}?');\" href=\"files/index.php?action=deletefolder&amp;location=$this->location&amp;folder={$folder['name']}/\"><img src=\"presentation/delete.gif\" onmouseover=\"this.src='presentation/delete_hover.gif'\" onmouseout=\"this.src='presentation/delete.gif'\" title=\"Delete\" alt=\"Delete\" class=\"actionimg\" width=\"59\" height=\"14\" /></a>";
			} else {
				$delete = "<img src=\"presentation/nodelete.gif\" title=\"Delete not allowed\" alt=\"Delete not allowed\" class=\"actionimg\" width=\"59\" height=\"14\" />";
			}

			if ($folder['files'] == 1){
				$num_files = $folder['files']." page"; 
			} else {
				$num_files = $folder['files']." pages";
			}
			$file_output .= <<<EOD
		<li>
			$delete
			<img src="presentation/folder_closed.gif" alt="directory" width="16" height="13" />
			<a class="folder" href="files/index.php?location={$folder['location']}">{$folder['name']} ($num_files)</a></li>
EOD;


		}

		// files

		foreach ($this->file_list as $file){
			if($file['allowed'] != TRUE && HIDE_DISALLOWED_FILES) continue;
			$urlencode_file = urlencode($file['name']);
			if($file['allowed']==TRUE){
				$edit = "<a href=\"files/index.php?action=editfile&amp;location=$this->location&amp;file=$urlencode_file\"><img src=\"presentation/edit.gif\" onmouseover=\"this.src='presentation/edit_hover.gif'\" onmouseout=\"this.src='presentation/edit.gif'\" title=\"Edit\" alt=\"Edit\" class=\"actionimg\" width=\"41\" height=\"14\" /></a>";
				$edit_2 = "<a href=\"files/index.php?action=editfile&amp;location=$this->location&amp;file=$urlencode_file\">{$file['name']}</a>";
			} else {
				// edit not allowed
				$edit = "<img src=\"presentation/noeditIcon.gif\" title=\"Edit not allowed\" alt=\"Edit not allowed\" class=\"actionimg\" width=\"41\" height=\"14\" />";
				$edit_2 = $file['name'];
			}

			if($file['allowed']==TRUE && $file['delete']==TRUE){
				$delete = "<a onclick=\"return confirm('Are you sure you want to delete $this->location{$file['name']}?');\" href=\"files/index.php?action=deletefile&amp;location=$this->location&amp;file=$urlencode_file\"><img src=\"presentation/delete.gif\" onmouseover=\"this.src='presentation/delete_hover.gif'\" onmouseout=\"this.src='presentation/delete.gif'\" title=\"Delete\" alt=\"Delete\" class=\"actionimg\" width=\"59\" height=\"14\" /></a>";
			} else {
				// delete not allowed
				$delete = "<img src=\"presentation/nodelete.gif\" title=\"Delete not allowed\" alt=\"Delete not allowed\" class=\"actionimg\" width=\"59\" height=\"14\" />";
			}

			$file_output .= <<<EOD
	<li>$delete
	<a href='$this->web_root$this->current_location{$file['name']}' target='_blank'><img src='presentation/preview.gif' onmouseover="this.src='presentation/preview_hover.gif'" onmouseout="this.src='presentation/preview.gif'" title='Preview' alt='Preview' class='actionimg' width='67' height='14' /></a>
	$edit
	<div class="size">{$file['size']} kB</div>
	<div class="size"> Last modified: {$file['date']}</div> <img src="presentation/file_normal.gif" alt="file" width="16" height="14" />
	$edit_2</li>
EOD;
		}


		$this->content .= <<<EOD
		$this->status_div
		$this->new_items
		<ul class="filelist">
		$file_output
		</ul>

EOD;
	}


	// return content
	function getContent(){
		if($this->content==''){
			$this->renderItems();
		}
		return $this->content;
	}

}

?>