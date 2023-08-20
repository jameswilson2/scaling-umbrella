<?php
require_once 'files/_file_builder.class.php';

// php file builder class - build php page from required templates

// extends _file_builder.class

class HomeBuilder extends FileBuilder{

	function HomeBuilder(){
		parent::FileBuilder();

		$this->web_root = WEB_ROOT;
		// split up server variables into required parts

		$this->filename = 'index.php';

		$this->current_location = 'homepage/';

		if($_SERVER['QUERY_STRING']!=''){
			$this->filename.= '?'.$_SERVER['QUERY_STRING'];
		}

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