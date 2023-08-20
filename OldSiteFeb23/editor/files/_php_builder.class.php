<?php
require_once 'files/_file_builder.class.php';

// php file builder class - build php page from required templates

// extends _file_builder.class

class PHPBuilder extends FileBuilder{

	private $template = null;
	
	function PHPBuilder($template = null){
		parent::FileBuilder();
		
		$this->template = $template;
		if($this->template && !is_readable($template)){
			throw new Exception("Cannot read template file '$template'");
		}
		
		$this->web_root = WEB_ROOT;
		// split up server variables into required parts

		$currentFile = $_SERVER["SCRIPT_NAME"];
		$parts = Explode('/', $currentFile);
    	$currentFile = $parts[count($parts) - 1];

		$full_path = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

		list($junk, $local_path) = explode($this->web_root, $full_path);

		$local_paths = explode('/', $local_path);

		$num_folders = count($local_paths);

		$this->filename = $local_paths[$num_folders - 1];

		if($this->filename!=''){
			list($this->current_location, $junk) = explode($this->filename, $local_path);
		} else {
			$this->current_location = $local_path;
		}

	}

	function findTemplate(){
		if($this->template){
			$filename_components = pathinfo($this->template);
			$this->template_location = $filename_components['dirname'];
			$this->template_name = $filename_components['basename'];
			$this->template_content = file_get_contents($this->template);
		}
		else return parent::findTemplate();
	}

	// public - build completed page and save
	function buildPage(){
		$this->findTemplate();
		$this->pickIncludes();

		foreach($this->includes as $include_name){
			$this->findInclude($include_name);
		}

		$this->combinePage();

		return $this->template_content;

	}

}


?>