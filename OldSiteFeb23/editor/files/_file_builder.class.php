<?php
// page builder class
// build page from required components
// pick out template and includes as required
// save page and sort backups



class FileBuilder{

	// current location
	var $current_location;

	// server root
	var $server_root;

	// filename
	var $filename;

	// template name
	var $template_name = '_template.sck.tpl';

	// template location (once found)
	var $template_location;

	// template contents
	var $template_content;

	// title
	var $meta_title;

	// description
	var $meta_description;

	// keywords
	var $meta_keywords;

	// page content
	var $content;

	// array of includes
	var $includes = array();
	
	var $memory_includes = array();
	
	// set up initial variable - pick from config file
	function FileBuilder(){
		$this->server_root = EDITABLE_ROOT;
	}


	// set location of the file
	function setLocation($current_location){
		$this->current_location = $current_location;
	}


	// set filename of the file to be built
	function setFilename($filename){
		$this->filename=$filename;
	}


	// set page title
	function setTitle($title){
		$this->meta_title = $title;
	}


	// set page description
	function setDescription($description){
		$this->meta_description = $description;

	}


	// set page keywords
	function setKeywords($keywords){
		$this->meta_keywords = $keywords;
	}


	// set page content
	function setContent($content){
		$content = trim($content);

		$content = ereg_replace('checked="0" ', '', $content);
		$content = ereg_replace('disabled="0" ', '', $content);
		$content = ereg_replace('readonly="0" ', '', $content);
		$content = ereg_replace('disabled="true" ', '', $content);
		$content = ereg_replace('readonly="true" ', '', $content);

		$this->content =<<<EOD
<!-- CONTENT START -->
$content
					<!-- CONTENT END -->
EOD;
	}


	// find closest template and store content
	function findTemplate(){
		$this->template_location = $this->fileFinder($this->server_root, $this->template_name);

		$this->template_content = file_get_contents($this->template_location.$this->template_name);

	}


	// search for given file, starting at current location and ending at $top
	// return location of the file
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
		}
		
		trigger_error("File not found '$search_filename'", E_USER_ERROR);
		exit;
	}


	// pick includes out of template
	function pickIncludes(){
		$templatecontents = $this->template_content;

		while (ereg('<!-- INCLUDE MODULE "[^"]+" -->', $templatecontents)){

			preg_match('/(<!-- INCLUDE MODULE ")([^"]+)(" -->)/', $templatecontents, $match);

			$include = $match[2];

			$templatecontents = ereg_replace('<!-- INCLUDE MODULE "'.$include.'" -->', 'SORTED', $templatecontents );
			$this->includes[] = $include;

		}

	}


	// find closest include and add to template
	function findInclude($include_name){
		//$this->include_location = $this->fileFinder($this->template_location, $include_name);

		$this->include_location = $this->fileFinder($this->server_root, $include_name);
		
		if(isset($this->memory_includes[$include_name])){
			$includecontents = $this->memory_includes[$include_name];
		}
		else{
			$includecontents = file_get_contents($this->include_location.$include_name);
		}
		
		$this->template_content = ereg_replace('<!-- INCLUDE MODULE "'.$include_name.'" -->', $includecontents, $this->template_content );
	}
	
	public function setIncludeModuleContent($include_name, $content){
		$this->memory_includes[$include_name] = $content;
	}

	// replace page specific variables
	function combinePage(){

		$this->template_content = ereg_replace('### TITLE ###', $this->meta_title, $this->template_content );
		$this->template_content = ereg_replace('### META DESCRIPTION ###', $this->meta_description, $this->template_content );
		$this->template_content = ereg_replace('### META KEYWORDS ###', $this->meta_keywords, $this->template_content );
		$this->template_content = ereg_replace('<!-- ### CONTENT AREA ### -->', $this->content, $this->template_content );
		$this->template_content = ereg_replace('### SELF ###', $this->current_location.$this->filename, $this->template_content );

	}


	// save page and sort backups
	function savePage(){
		$tempfilename = "tempfile.htm";
		$filename = $this->server_root.$this->current_location.$this->filename;
		$backup = $this->server_root.$this->current_location.$this->filename.".BCK";


		// save file to temporary file
		$tempfile = fopen($tempfilename, 'w');
		if (!$tempfile) {
			exit('<p>Unable to open temporary file for writing!</p>');
		}
		fwrite($tempfile, $this->template_content);
		fclose($tempfile);

		// delete old backup
		@unlink ($backup);

		// copy old file to backup
		$ok = @copy($filename, $backup);

		// copy temporary file to correct location
		$ok = copy($tempfilename, $filename);

		// delete temporary file
		@unlink($tempfilename);
	}


	// public - build completed page and save
	function buildPage(){
		$this->findTemplate();
		$this->pickIncludes();

		foreach($this->includes as $include_name){
			$this->findInclude($include_name);
		}

		$this->combinePage();

		$this->savePage();

	}

}


?>