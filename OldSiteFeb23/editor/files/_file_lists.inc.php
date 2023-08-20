<?php
require_once 'library/security/_secure.inc.php';

// build list of images for the file editor to choose from

function buildFileList(){


	// ignore disallowed files
	// add special files (from config)
	// sort by folder then filename

	$location = EDITABLE_ROOT;

	$file_list = $GLOBALS['hidden_file_list'];

	// open current directory
	$dp = opendir($location);
	$dir_list = array();
	// loop through the directory
	while (false !== ($directory = readdir($dp))) {
		$match = 0;
		foreach ($GLOBALS['disallowed'] as $dis) {
			if ($dis == $directory ){
				$match++;
			}
		}
		if (is_dir($location . $directory) && $match == 0) {
			// $directory is a directory...
			$dir_list[] = $directory;
		}
	}

	// Close top level directory
	closedir($dp);

	$dir_lowercase = array_map('strtolower', $dir_list);
	array_multisort($dir_lowercase, SORT_ASC, SORT_STRING, $dir_list);

	foreach ($dir_list as $dir){
		$sublocation = $location.$dir.'/';
		$subdp = opendir($sublocation);
		while (false !== ($file = readdir($subdp))) {
			if (is_file($sublocation.$file) && eregi('.html?$', $file)) {
				// $file is a htm or html file...
				$match = 0;
				foreach ($GLOBALS['disallowed_files'] as $dis_files) {
					if ($dis_files == $file ){
						$match++;
					}
				}
				if ($match==0){
					$file_list[] = array($dir.'/', $file);
				}
			}
		}

		closedir($subdp);
	}


	// open current directory
	$dp = opendir($location);

	// loop through the directory
	while (false !== ($entry = readdir($dp))) {
		if (is_file($location.$entry) && eregi('.html?$', $entry)) {
			// $entry is a htm or html file...
			$match = 0;
			foreach ($GLOBALS['disallowed_files'] as $dis_files) {
				if ($dis_files == $entry ){
					$match++;
				}
			}
			if ($match==0){
				$file_list[] = array('', $entry);
			}

		}
	}
	// Close top level directory
	closedir($dp);



	foreach ($file_list as $key => $row) {
		$folder[$key]  = $row[0];
		$file[$key] = $row[1];
	}

	array_multisort($folder, SORT_ASC, $file, SORT_ASC, $file_list);

	$files = array();
	foreach ($file_list as $location){
		$folder = $location[0];
		$file = $location[1];

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
?>
