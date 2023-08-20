<?php

function getFileExtensionFromContentType($content_type){

	$extensions = array(
		
		"text/plain" => "txt",
		"text/html" => "html",
		
		"image/jpeg" => "jpg",
		"image/png" => "png",
		"image/gif" => "gif"
	);
	
	return $extensions[$content_type];
}
