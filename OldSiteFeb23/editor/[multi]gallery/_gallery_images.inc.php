<?php

function uploadImage($inputName, $kind=''){

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

			resizeImage($filename, $kind);
		} else {
			$filename = '';
		}
	} else {
		$filename = '';
	}
	return ($filename);
}


function deleteImage($image_name){

	@unlink(GALLERY_LOCATION.$image_name);
}



function resizeImage($img, $kind){

	if($kind=='collection'){
		$dir = GALLERY_LOCATION;
		$newdir = GALLERY_LOCATION;

		$max_w = COLLECTION_WIDTH;
		$max_h = COLLECTION_HEIGHT;

		// get original images width and height
		list($or_w, $or_h) = getimagesize($dir.$img);

		$source_ratio = ($or_h / $or_w);
		$target_ratio = ($max_h / $max_w);

		$resize_width = $max_w;
		$resize_height = $max_h;

		if ($target_ratio < $source_ratio){
			$resize_height = 0;
		} else {
			// height, then crop to width
			$resize_width = 0;
		}

		$phMagick = &new phMagick($dir.$img);
		$phMagick->setDestination($dir.'thumbs/'.$img)
				->resize($resize_width,$resize_height)
				->crop($max_w, $max_h);

	} else {

		// set destination directory
		$dir = GALLERY_LOCATION;
		$newdir = GALLERY_LOCATION;

		$max_w = THUMB_WIDTH;
		$max_h = THUMB_HEIGHT;

		$phMagick = &new phMagick($dir.$img);
		$phMagick->setDestination($dir.'thumbs/'.$img)
		->resize($max_w,$max_h);

		$max_w = MAX_WIDTH;
		$max_h = MAX_HEIGHT;

		$phMagick = &new phMagick($dir.$img);
		$phMagick->setDestination($dir.'sample/'.$img)
		->resize($max_w,$max_h);

	}

	return true;

}

?>