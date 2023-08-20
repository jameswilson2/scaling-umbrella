<?php
// build list of images for the file editor to choose from

function buildImageList(){

	$location = IMAGE_PATH;
	// open current directory
	$dp = opendir($location);
	// loop through the directory
	$file_list = Array();
	while (false !== ($entry = readdir($dp))) {
		if (is_file($location.$entry) && eregi('.jpe?g$', $entry)) {
			// $entry is a jpg or jpeg file...
			$match = 0;
			if(isset($disallowed_images) && count($disallowed_images) > 0) {
				foreach ($disallowed_images as $dis_images) {
					if (strtolower($dis_images) == strtolower($entry) ){
						$match++;
					}
				}
			}
			if ($match==0) {
				$file_list[] = $entry;
			}
		}
	}
	// Close directory
	closedir($dp);

	$file_lowercase = array_map('strtolower', $file_list);
	array_multisort($file_lowercase, SORT_ASC, SORT_STRING, $file_list);

	$folder = IMAGE_FOLDER;

	$images = array();
	foreach ($file_list as $image_name){
		$images[] =	"[\"$image_name\", \"$folder$image_name\"]";

	}

	$content = implode(',', $images);

	$content = <<<EOD
	var tinyMCEImageList = new Array(
		$content
	);
EOD;

	// save to file

	$filename = EDITABLE_ROOT.'editor/files/lists/image_list.js';
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
