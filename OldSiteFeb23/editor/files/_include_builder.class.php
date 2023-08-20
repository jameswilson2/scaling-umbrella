<?php
// file builder class
// build file from required components
// pick out template and includes as required
// save file and sort backups



class IncludeBuilder{

	// current location
	var $current_location;

	// server root
	var $server_root;

	// filename
	var $filename;

	// include content
	var $content;

	// set up initial variable - pick from config file
	function IncludeBuilder(){
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


	// set page content
	function setContent($content){
		$content = trim($content);

		$content = ereg_replace('checked="0" ', '', $content);
		$content = ereg_replace('disabled="0" ', '', $content);
		$content = ereg_replace('readonly="0" ', '', $content);
		$content = ereg_replace('disabled="true" ', '', $content);
		$content = ereg_replace('readonly="true" ', '', $content);

		$this->content =<<<EOD
$content
EOD;
	}


	// save page and sort backups
	function savePage(){
		$tempfilename = "tempfile.htm";
		$filename = $this->server_root.$this->current_location.$this->filename;
		$backup = $this->server_root.$this->current_location.$this->filename.".BCK";

		// save file to temporary file
		$tempfile = fopen($tempfilename, 'w');
		if (!$tempfile) {
			exit("<p>Unable to open temporary file for writing!</p>");
		}
		fwrite($tempfile, $this->content);
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

		$this->savePage();

	}

}


?>