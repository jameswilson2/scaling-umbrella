<?php

function uploadImage($inputName){
	if (trim($_FILES[$inputName]['tmp_name']) != null){
		echo "File. " . $_FILES[$inputName]['type'];
		if (eregi('^image/p?jpeg(;.*)?$', $_FILES[$inputName]['type'])
			|| eregi('^image/gif(;.*)?$', $_FILES[$inputName]['type'])
			|| eregi('^image/png(;.*)?$', $_FILES[$inputName]['type'])){

			$tmp_name = $_FILES[$inputName]['tmp_name'];
			$type = $_FILES[$inputName]['type'];

			if (eregi('^image/p?jpeg(;.*)?$', $type)){
				$extension = '.jpg';
			} else if(eregi('^image/png(;.*)?$', $type)) {
				$extension = '.png';
			} else {
				$extension = '.gif';
			}
			// complete path/filename
			$path = IMAGE_PATH;
			$filename = md5(rand() . time() . $_SERVER['REMOTE_ADDR']) . $extension;

			// copy file  - if deemed safe
			if (is_uploaded_file($tmp_name) && copy($tmp_name, $path.$filename)){

			} else {
				$filename = '';
			}
			resizeImage($filename);
		} else {
			$filename = '';
		}
	} else {
		$filename = '';
	}
	return ($filename);
}


function deleteImage($image_name){

	@unlink(IMAGE_PATH.$image_name);
}



function resizeImage($img, $status=''){

	// set destination directory
	$dir = ICON_FOLDER;
	$newdir = ICON_FOLDER;

	// get original images width and height
	list($or_w, $or_h, $or_t) = getimagesize($dir.$img);

	$max_w = ICON_MAX_WIDTH;
	$max_h = ICON_MAX_HEIGHT;

	// make sure image is a jpeg
	if ($or_t == 2) {

		// obtain the image''s ratio
		$ratio = ($or_h / $or_w);

		// original image
		$or_image = imagecreatefromjpeg($dir.$img);

		// resize image?
		if ($or_w > $max_w) {

			// resize by width, then height (width dominant)
			$rs_w = $max_w;
			$rs_h = $ratio * $rs_w;

			// copy old image to new image
			$rs_image = imagecreatetruecolor($rs_w, $rs_h);
			imagecopyresampled($rs_image, $or_image, 0, 0, 0, 0, $rs_w, $rs_h, $or_w, $or_h);
		} else {
			// image requires no resizing
			$rs_w = $or_w;
			$rs_h = $or_h;

			$rs_image = $or_image;
		}

		// generate resized image
		imagejpeg($rs_image, $newdir.$img, '90');

		return true;

	} else {
		// Image type was not jpeg!
		return false;
	}
}

?>