<?php
require_once 'files/_file_builder.class.php';

// file rebuilder class - rebuild whole website

// extends _file_builder.class

class Rebuilder extends FileBuilder{

	// array containing the full tree
	var $tree = array();


	// array containing folders already parsed
	var $old_tree = array();


	// array of templates in the tree
	var $templates = array();


	// array of html files in the tree
	var $html_files = array();


	// array of includes in the tree
	var $includes = array();

	// array of content to be added to database
	var $sql = array();

	function Rebuilder(){
		parent::FileBuilder();

		$this->navigateTree('','');

		// sort file arrays by location (name)
		// sort templates by depth (deepest first)

		sort($this->tree);

		foreach ($this->html_files as $key => $row) {
		    $location[$key]  = $row['location'];
		}

		array_multisort($location, SORT_ASC, $this->html_files);

		$location = array();
		foreach ($this->templates as $key => $row) {
		    $depth[$key] = $row['depth'];
		}

		array_multisort($depth, SORT_DESC, $this->templates);

		$location = array();
		foreach ($this->include_list as $key => $row) {
		    $location[$key]  = $row['location'];
		}

		array_multisort($location, SORT_ASC, $this->include_list);

		/*
		echo "<pre>";
		print_r($this->tree);
		print_r($this->html_files);
		print_r($this->include_list);
		print_r($this->templates);
		echo "</pre>";
		*/

		$this->rebuildPages();
		$this->buildFileList();
		$this->saveSearchData();

	}


	// navigate whole tree and pick out all allowed folders
	// save tree to array
	// this tree will be used for picking out files to rebuild
	// find all templates in the tree (allowed folders)
	// find all allowed html files in the tree (allowed files)
	function navigateTree($start){
		$disallowed_folders = $GLOBALS["disallowed_folders"];
		$disallowed_files = $GLOBALS["disallowed_files"];

		$subdirectories=opendir(EDITABLE_ROOT.$start);
		while (($entry=readdir($subdirectories))!==false){
			$path=$start.$entry.'/';
			if (is_file(EDITABLE_ROOT.$start.$entry)){
				$match=0;
				foreach ($disallowed_files as $dis_file) {
					if ($dis_file == $start.$entry ){
						$match++;
					}
				}

				if (eregi('.html?$', $entry) && $match==0){
					// html file - save to array
					// check if allowed to rebuild
					$this->html_files[] = array('location'=>$start, 'filename'=>$entry);
				} elseif (eregi('.inc$', $entry)){
					// include - save to array
					$content = file_get_contents($this->server_root.$start.$entry);
					$this->include_list[] = array('location'=>$start, 'filename'=>$entry, 'content'=>$content);
				} elseif (eregi('.sck.tpl$', $entry)){
					// template - save to array
					$depth = substr_count($start,'/');
					$this->templates[] = array('location'=>$start, 'depth'=>$depth);
				}


			} else {
				// check if allowed directory - if not don''t check for sub directories
				$match=0;

				foreach($disallowed_folders as $dis){
					if($path==$dis){
						$match++;
					}
				}


				if (($entry!='.') && ($entry!='..') && $match==0){
					$this->tree[] = $path;
				}
				if (($entry!='.') && ($entry!='..')  && $match==0){
					$this->navigateTree($path);
				}
			}
		}

	}


	// find most appropriate include and add to template
	function findInclude($include_name){

		$this->current_include_name = $include_name;

		// pick all potential includes by name and location
		$suitable_includes = array_filter($this->include_list, array($this,"compare_name_location"));

		sort($suitable_includes);

		if(count($suitable_includes)==1){
			// find content and add to template
			$this->template_content = ereg_replace('<!-- INCLUDE MODULE "'.$include_name.'" -->', $suitable_includes[0]['content'], $this->template_content );
			return;
		} else {
			// loop through folders and find most suitable include
			$levels = explode('/', $this->server_root.$this->current_location);
			$level=count($levels);

			// put back together - one less level each time

			for($i=($level-1); $i>=1; $i--){
				$file_location='';
				for($j=0; $j<=($i-1); $j++){
					$file_location .= $levels[$j].'/';

				}

				foreach($suitable_includes as $_include){
					if($file_location==$this->server_root.$_include['location']){
						// matches
						$this->template_content = ereg_replace('<!-- INCLUDE MODULE "'.$include_name.'" -->', $_include['content'], $this->template_content );
						return;
					}
				}
				//if ($file_location==$this->server_root.$this->template_location){
				if ($file_location==$this->server_root){
					trigger_error("File not found '$include_name'", E_USER_ERROR);
					exit;
				}
			}
		}
	}


	function compare_name_location($include){
		//if($include['filename'] == $this->current_include_name && ($this->template_location=='' || strpos($include['location'], $this->template_location) !== false)){

		if($include['filename'] == $this->current_include_name && ($this->template_location=='' || $include['location']=='' || strpos($include['location'], $this->template_location) !== false)){
			return TRUE;
		} else {
			return FALSE;
		}
	}


	function buildFileList(){

		$files = array();
		foreach ($this->html_files as $html_file){
			$folder = $html_file['location'];
			$file = $html_file['filename'];

			$files[] =	"[\"$folder$file\", \"$folder$file\"]";
		}

		$content = implode(',', $files);

		$content = <<<EOD
var tinyMCELinkList = new Array(
	$content
);
EOD;

		// save to file

		$filename = EDITABLE_ROOT.'editor/files/lists/link_list.js';
		$tempfilename = "tempfile.htm";

		$tempfile = fopen($tempfilename, 'w');
		if (!$tempfile) {
			exit("<p>Unable to open temporary file for writing!</p>");
		}

		fwrite($tempfile, $content);
		fclose($tempfile);

		// copy temporary file to correct location
		$ok = copy($tempfilename, $filename);

		// delete temporary file
		@unlink($tempfilename);

	}


	// rebuild pages, template by template
	function rebuildPages(){
	 	foreach($this->templates as $template){
	 		$this->template_location = $template['location'];

			$this->template_content = file_get_contents($this->server_root.$this->template_location.$this->template_name);
			$this->includes = array();
			$this->pickIncludes();
			$this->template_master_content = $this->template_content;

			foreach($this->html_files as $html_file){

				// keep track of the current location - if same as last then don't need to recheck for includes again

				$this->filename = $html_file['filename'];
				$this->current_location = $html_file['location'];

				if(!in_array($this->current_location, $this->old_tree)){
					// if done this folder before then don't do it again!
					if($this->template_location=='' || strpos($this->current_location, $this->template_location) !== false ){
						//echo $this->current_location.$this->filename."<br />";

						if($old_location===$this->current_location){
							// don't check the includes again
						} else {
							if($old_location!=''){
								$this->old_tree[] = $old_location;
							}

							// check includes
							// reset template to master
							$this->template_content = $this->template_master_content;

							// find most appropriate includes and add to template

							foreach($this->includes as $include_name){
								$this->findInclude($include_name);
							}

							$this->template_folder_content = $this->template_content;

							$old_location = $this->current_location;
						}

						// get old content
						// build + save page

						$this->template_content = $this->template_folder_content;

						$filecontent = file_get_contents($this->server_root.$this->current_location.$this->filename);

						// get everything between content start and end tags
						// MUST CHECK IF CONTENT TAGS ARE PRESENT!!!
						if (!ereg('<!-- CONTENT START -->', $filecontent) || !ereg('<!-- CONTENT END -->', $filecontent)){
							echo "bad tags! : ".$this->current_location.$this->filename ;
							exit;
							header('location:'.WEB_ROOT.'editor/files/index.php?location='.$this->location.'&status=editfilefail');
							exit;
						}

						list($header, $footer) = explode("<!-- CONTENT START -->", $filecontent);
						list($content, $footer) = explode("<!-- CONTENT END -->", $footer);
						list($rubbish, $title) = explode("<title>", $header);
						list($title, $rubbish) = explode("</title>", $title);
						list($rubbish, $description) = explode('<meta name="description" content="', $header);
						list($description, $rubbish) = explode('" />', $description);
						list($rubbish, $keywords) = explode('<meta name="keywords" content="', $header);
						list($keywords, $rubbish) = explode('" />', $keywords);

						$this->setFilename($this->filename);
						$this->setTitle($title);
						$this->setDescription($description);
						$this->setKeywords($keywords);
						$this->setContent($content);

						$this->combinePage();

						$this->savePage();

						$_title = safeAddSlashes(html_entity_decode(strip_tags($title)));
						$content = ereg_replace('</p>', ' ', $content);
						$content = ereg_replace('<br />', ' ', $content);
						$content = ereg_replace('</li>', ' ', $content);
						$content = ereg_replace('<ul>', ' ', $content);
						$content = ereg_replace('</ol>', ' ', $content);
						$content = ereg_replace('</td>', ' ', $content);
						$content = ereg_replace('<hr />', ' ', $content);
						$content = ereg_replace('</div>', ' ', $content);
						$content = ereg_replace('</h1>', ' ', $content);
						$content = ereg_replace('</h2>', ' ', $content);
						$content = ereg_replace('</h3>', ' ', $content);
						$content = ereg_replace('</h4>', ' ', $content);
						$_content = safeAddSlashes(html_entity_decode(strip_tags($content)));
						$_folder = safeAddSlashes($this->current_location);
						$_filename = safeAddSlashes($this->filename);

						$this->sql[] = "('$_title', '$_content', '$_folder', '$_filename')";

					}
				}
			}
		}
	}


	function saveSearchData(){
		$this->sql = implode(',', $this->sql);

		$sql2 = "DELETE FROM tbl_search";
		$result = getQuery($sql2);

		$sql = "INSERT INTO tbl_search (search_title, search_text, search_folder, search_filename) VALUES ".$this->sql;

		$result = getQuery($sql);

	}


}


?>