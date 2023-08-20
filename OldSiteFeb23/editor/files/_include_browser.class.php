<?php
require_once 'files/tree.inc.php';
require_once 'files/_include_builder.class.php';
require_once 'files/_file_rebuilder.class.php';

/* FileBrowser class
build list of files
build directory tree
each file - check if allowed to edit/delete
each folder - check if allowed to edit/delete

combine config files (new class?)

perform actions (new file/folder/delete)

*/

class IncludeBrowser {

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

	// combined array of diallowed includes - no editor access to this user
	var $disallowed_includes;

	// combined array of disallowed folders - no editor access to this user
	var $disallowed_folders;

	// up a level link
	var $up;

	// current location name
	var $location_name;

	// array of subfolders
	var $folder_list = array();

	// array of files
	var $file_list = array();

	// array of include names
	var $includes = array();

	// template name
	var $template_name = '_template.sck.tpl';


	// set up the initial variables - pick up from config and combine with user specific variables
	function IncludeBrowser(){
		$this->tree_start = $_SESSION['user_web_start'];
		$this->web_root = WEB_ROOT;
		$this->server_root = EDITABLE_ROOT;
		$this->location = $_GET['location'];
		$this->current_location = $this->tree_start.$this->location;

		// combine these with user specific arrays
		$this->disallowed_folders = array_merge($GLOBALS["disallowed_folders"], $_SESSION['user_disallowed_folders']);
		$this->disallowed_includes = $GLOBALS["disallowed_includes"];

		$this->checkLocation();
		$this->findTemplate();

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
				header('location:'.WEB_ROOT.'editor/files/includes.php');
				exit;
			}

		}
		foreach ($this->disallowed_folders as $dis) {
			if (preg_match("#^$dis#", $this->current_location)){
				header('location:'.WEB_ROOT.'editor/files/includes.php');
				exit;
			}
		}
	}


	// find suitable template and extract list of includes from it
	// check if template is in the current folder
	function findTemplate(){
		$this->template_location = $this->fileFinder($this->server_root, $this->template_name);

		if($this->template_location==$this->server_root.$this->current_location){
			$this->template_status = "SAME";
		} else {
			$this->template_status = "DIFFERENT";
		}

		$templatecontents = file_get_contents($this->template_location.$this->template_name);

		// pick out include names

		while (ereg('<!-- INCLUDE MODULE "[^"]+" -->', $templatecontents)){
			$include = ereg_replace('(.+)(<!-- INCLUDE MODULE ")([^"]+)(" -->)(.+)', '\\3', $templatecontents);
			$templatecontents = ereg_replace('<!-- INCLUDE MODULE "'.$include.'" -->', 'SORTED', $templatecontents );
			if($this->isAllowedFile($include)){
				$this->includes[] = $include;
			}
		}

	}


	function fileFinder($top, $search_filename){

		// loop until web root is reached

		$levels = explode('/', $this->server_root.$this->current_location);
		$level=count($levels);

		// put back together - one less level each time

		for($i=($level-1); $i>=1; $i--){
			$file_location='';
			for($j=0; $j<=($i-1); $j++){
				$file_location .= $levels[$j].'/';

			}
			if (is_file($file_location.$search_filename)){
				return $file_location;
			}
			if ($file_location==$top){
				echo "Error: Page not found!";
				exit;
			}
		}

	}


	// get action from query string and respond accordingly
	// need method for each action
	function getAction(){
		switch($_GET['action']){
			case 'newinclude':
				$this->createInclude();
				break;

			case 'deleteinclude':
				$this->deleteInclude();
				break;

			case 'editinclude':
				$this->editInclude();
				break;

			case 'rebuild':
				$this->rebuild();
				break;

			default:
				break;
		}
	}


	// check if a folder is allowed editor access by current user
	// noedit folders
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
		foreach ($this->disallowed_includes as $dis_file) {
			if ($dis_file == $file ){
				return FALSE;
			}
		}

		return TRUE;
	}


	// check if a page is allowed to be deleted - ie not in the template root
	function isAllowedDeleteFile($file){
		if($this->template_status=="SAME"){
			return FALSE;
		} else {
			return TRUE;
		}
	}


	// create new page from POST data
	function createInclude(){
		$newfile = $_POST['newFile'];
		$location = $this->server_root.$this->current_location;


		$dp = opendir($location);
		// loop through the directory
		$file_list = Array();
		while (false !== ($entry = readdir($dp))) {
			if (is_file($location.$entry) && eregi('.inc$', $entry)) {
				// $entry is a htm or html file...
				$file_list[] = $entry;
			}
		}
		// Close directory
		closedir($dp);

		// check new filename against current list
		// must check if it''s the same with either extension
		foreach ($file_list as $file){
			if (strtolower($file)==strtolower($newfile)){
				// page already exists
				header('location:'.WEB_ROOT.'editor/files/includes.php?location='.$this->location.'&status=newfileexists');
				exit;
			}
		}

		// create new page 

		$newfile = strtolower($newfile);

		// copy page from the template root

		$ok = @copy($this->template_location.$newfile, $location.$newfile);

		header('location:'.WEB_ROOT.'editor/files/includes.php?location='.$this->location.'&status=newfile');
		exit;
	}


	// delete file from GET data
	function deleteInclude(){

		$file = $_GET['file'];
		$filename = $this->server_root.$this->current_location.$file;

		if(!is_file($filename)){
			header('location:'.WEB_ROOT.'editor/files/includes.php?location='.$this->location.'&status=deletefilefail');
			exit;
		}

		if(!$this->isAllowedFile($file) || !$this->isAllowedDeleteFile($file)){
			header('location:'.WEB_ROOT.'editor/files/includes.php?location='.$this->location.'&status=deletefilefail');
			exit;
		}

		$deleted = $filename.'.deleted';
		// copy file to 'deleted' location
		$ok = copy($filename, $deleted);

		// delete current file
		@unlink($filename);

		header('location:'.WEB_ROOT.'editor/files/includes.php?location='.$this->location.'&status=deleted');
		exit;

	}



	// edit file - also save file if edited
	function editInclude(){
		$file = $_GET['file'];
		if(!$this->isAllowedFile($file)){
			header('location:'.WEB_ROOT.'editor/files/includes.php?location='.$this->location.'&status=editfilefail');
			exit;
		}

		// if POST set - save file

		if (isset($_POST["elm1"])) {

			$content = $_POST["elm1"];

			// save include file
			$builder = new IncludeBuilder();

			$builder->setLocation($this->current_location);
			$builder->setFilename($file);
			$builder->setContent($content);

			$builder->buildPage();

		}

		// load file data

		$filecontent = file_get_contents($this->server_root.$this->current_location.$file);

		// load edit form

		$urlencode_file = urlencode($file);
		$content = htmlspecialchars($filecontent, ENT_COMPAT, "UTF-8");

		$base_href = WEB_ROOT;
		$css = CSS;

		$edit_form = <<<EOD
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
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,embed_video_link,template",
		cleanup: false,
		
		// Theme options
		theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,|,template",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,embed_video_link,cleanup,help,code",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,|,fullscreen",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",

		width : "792",
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
$this->status_div
			<div class="important">
				<div class="article-left">
					<p><strong>You are now editing page:</strong> <img src="presentation/file_normal.gif" alt="page icon" width="16" height="14" class="minicon" />$this->location$file</p>
				</div> 
				<div class="article-rightalt">
					<p><input type="button" id="btnPBack" name="btnPBack" value="Back" onclick="window.location.href='files/includes.php?location=$this->location'" /></p>
				</div>
				<div class="clear"></div>
				<p><small>Once you have made your changes save your page with the button at the bottom.</small></p>
			</div>

			<form action="files/includes.php?action=editinclude&amp;status=editsuccess&amp;location=$this->location&amp;file=$urlencode_file" method="post">
					<textarea name="elm1" id="elm1" cols="60" rows="15">$content</textarea>

				<div class="important">
					<div class="article-left">
						<input type="button" id="btnBack" name="btnBack" value="Back" onclick="window.location.href='files/includes.php?location=$this->location'" />
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
		header('location:'.WEB_ROOT.'editor/files/includes.php?location='.$this->location.'&status=rebuildsuccess');
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
			$this->breadcrumbs[] = "<a href=\"files/includes.php\">Root</a>";
		} else {
			$this->breadcrumbs[] = "<a href=\"files/includes.php\">$this->tree_start</a>";
		}

		$current = explode('/', $this->location);
		$level=count($current);

		$parent = '';
		for($i=0; $i<($level-1); $i++){
			$parent .= $current[$i].'/';
			$this->breadcrumbs[] = "<a href=\"files/includes.php?location=$parent\">{$current[$i]}</a>";
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

			if($i==0){
				$this->folder_list[] = array('name'=>$dir, 'location'=>$this->location.$dir.'/', 'files'=>$i);
			} else {
				$this->folder_list[] = array('name'=>$dir, 'location'=>$this->location.$dir.'/', 'files'=>$i);
			}

		}

	}


	// get list of files in the current folder
	function getFiles(){

		// open current directory
		$dp = opendir($this->server_root.$this->current_location);

		// loop through the directory
		$file_list = Array();
		while (false !== ($entry = readdir($dp))) {
			if (is_file($this->server_root.$this->current_location.$entry) && eregi('.inc$', $entry)) {
				// $entry is a inc file...
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
					$status = "<p>Include saved successfully.<br />Now <a href='files/includes.php?action=rebuild&location='>apply these changes</a> across the site.</p>";
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
		$tree = traverseDirTree($this->tree_start, '', 'files/includes.php', null,'displayPath');
		if($this->tree_start==''){
			$tree_start_name = 'Root';
		} else {
			$tree_start_name = $this->tree_start;
		}


		if (count($this->includes)!=0 && $this->template_status!="SAME"){
			foreach($this->includes as $include_name){
				$include_list .= "<option value=\"$include_name\">$include_name</option>";
			}

			$new_file_div = <<<EOD
	<p><img src="presentation/file_normal.gif" alt="New Page" width="16" height="14" title="New Page" class="minicon" /> <strong><a href="javascript:;" onclick="expand('newfileDiv')">Create new include</a></strong></p>

	<div id="newfileDiv" class="hide">
		<div class="newitem">
				<form action="files/includes.php?action=newinclude&amp;location=$this->location" method="post">
				<p><strong>New include:</strong> <select name="newFile" id="newFile">$include_list</select>
				<input type="submit" id="createNewFile" name="createNewFile" value="Create New Page" onclick="this.form.submit();this.disabled=true;this.value='Creating';" /></p>
				</form>
		</div>
	</div>
EOD;

		}





		$this->new_items = <<<EOD

<div id="newitems">

	<p><img src="presentation/folder_open.gif" alt="Browse Tree" width="16" height="14" title="Browse Tree" class="minicon" /> <strong><a href="javascript:;" onclick="expand('treeDiv')">Browse Page Tree</a></strong></p>

	<div id="treeDiv" class="hide">
		<div class="newitem">
			<ul>
				<li class=""><a href="files/includes.php">$tree_start_name</a></li>
				$tree
			</ul>
		</div>
	</div>
	$new_file_div
	<p><img src="presentation/rebuild.gif" alt="Rebuild" width="16" height="14" title="Rebuild" class="minicon" /> <strong><a href="files/includes.php?action=rebuild&amp;location=$this->location">Apply Changes to Includes</a></strong></p>
	<p><img src="presentation/rebuild.gif" alt="Switch" width="16" height="14" title="Switch" class="minicon" /> <strong><a href="files/index.php?location=$this->location">Switch to Page View</a></strong></p>
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
			<a class="folder" href="files/includes.php?location=$this->up">[UP]</a></li>
EOD;

		}

		// folders
		foreach ($this->folder_list as $folder){

			$file_output .= <<<EOD
		<li>
			<img src="presentation/folder_closed.gif" alt="directory" width="16" height="13" />
			<a class="folder" href="files/includes.php?location={$folder['location']}">{$folder['name']}</a></li>
EOD;


		}

		// files

		foreach ($this->file_list as $file){
			if($file['allowed'] != TRUE && HIDE_DISALLOWED_INCLUDES) continue;
			$urlencode_file = urlencode($file['name']);
			if($file['allowed']==TRUE){
				$edit = "<a href=\"files/includes.php?action=editinclude&amp;location=$this->location&amp;file=$urlencode_file\"><img src=\"presentation/edit.gif\" onmouseover=\"this.src='presentation/edit_hover.gif'\" onmouseout=\"this.src='presentation/edit.gif'\" title=\"Edit\" alt=\"Edit\" class=\"actionimg\" width=\"41\" height=\"14\" /></a>";
				$edit_2 = "<a href=\"files/includes.php?action=editinclude&amp;location=$this->location&amp;file=$urlencode_file\">{$file['name']}</a>";
			} else {
				// edit not allowed
				$edit = "<img src=\"presentation/noeditIcon.gif\" title=\"Edit not allowed\" alt=\"Edit not allowed\" class=\"actionimg\" width=\"41\" height=\"14\" />";
				$edit_2 = $file['name'];
			}

			if($file['allowed']==TRUE && $file['delete']==TRUE){
				$delete = "<a onclick=\"return confirm('Are you sure you want to delete $this->location{$file['name']}?');\" href=\"files/includes.php?action=deleteinclude&amp;location=$this->location&amp;file=$urlencode_file\"><img src=\"presentation/delete.gif\" onmouseover=\"this.src='presentation/delete_hover.gif'\" onmouseout=\"this.src='presentation/delete.gif'\" title=\"Delete\" alt=\"Delete\" class=\"actionimg\" width=\"59\" height=\"14\" /></a>";
			} else {
				// delete not allowed
				$delete = "<img src=\"presentation/nodelete.gif\" title=\"Delete not allowed\" alt=\"Delete not allowed\" class=\"actionimg\" width=\"59\" height=\"14\" />";
			}

			$file_output .= <<<EOD
	<li>$delete
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