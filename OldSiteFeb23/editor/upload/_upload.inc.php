<?php
require_once 'upload/_upload.config.php';

function uploadFile($inputName){

	if (trim($_FILES[$inputName]['tmp_name']) != null){

		$tmp_name = $_FILES[$inputName]['tmp_name'];
		$filename = $_FILES[$inputName]['name'];

		$filename = str_replace(" ", "-", $filename);

		// complete path/filename
		$path = UPLOAD_PATH;

		// copy file  - if deemed safe
		if (is_uploaded_file($tmp_name) && copy($tmp_name, $path.$filename)){

		} else {
			$filename = '';
		}
	} else {
		$filename = '';
	}
	return ($filename);
}

function deleteFile($name){
	@unlink(UPLOAD_PATH . $name);
}

?>