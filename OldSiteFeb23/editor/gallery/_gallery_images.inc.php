<?php

function uploadImage($inputName, $small){

	if (trim($_FILES[$inputName]['tmp_name']) != null){

		if (eregi('^image/p?jpeg(;.*)?$', $_FILES[$inputName]['type'])
			|| eregi('^image/gif(;.*)?$', $_FILES[$inputName]['type'])){

			$tmp_name = $_FILES[$inputName]['tmp_name'];
			$type = $_FILES[$inputName]['type'];

			if (eregi('^image/p?jpeg(;.*)?$', $type)){
				$extension = '.jpg';
			} else {
				$extension = '.gif';
			}

			// complete path/filename
			$path = GALLERY_LOCATION;
			$filename = md5(rand() . time() . $_SERVER['REMOTE_ADDR']) . $extension;

			// copy file  - if deemed safe
			if (is_uploaded_file($tmp_name) && copy($tmp_name, $path.$filename)){

			} else {
				$filename = '';
			}

			resizeImage($filename, $small);
		} else {
			$filename = '';
		}
	} else {
		$filename = '';
	}
	return ($filename);
}


function deleteImage($image_name){

	@unlink(EAT_PATH.$image_name);
}



function resizeImage($img, $type){
	// set destination directory
	$dir = GALLERY_LOCATION;
	$newdir = GALLERY_LOCATION;


	$max_w = THUMB_WIDTH;
	$max_h = THUMB_HEIGHT;

	$phMagick = &new phMagick($dir.$img);
	$phMagick->debug = true;
	$phMagick->setDestination($dir.'thumbs/'.$img)
	->resize($max_w,$max_h);

	$max_w = MAX_WIDTH;
	$max_h = MAX_HEIGHT;

	$phMagick = &new phMagick($dir.$img);
	$phMagick->debug = true;
	$phMagick->setDestination($dir.$img)
	->resize($max_w,$max_h);

	return true;

}

?>