<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';

// decide what type is required
// if necessary pass through the height/width variables and original image to RAD uplaoder (located on another server)

// image type
$type = $_GET['type'];

// process url which will process upload, save to temp location etc
$process = PROCESS_RAD;

// return url which will process user
$return = REDIRECT_RAD;

switch ($type){
	case 'gallery':
		// use gallery parameters from xml file

		$extra = $_GET['extra'];
		$location = GALLERY_PATH.$extra.'/';
		$gallery_xml = $location.'content.xml';
		$xml_handle = fopen($gallery_xml, "r");

		while (!feof($xml_handle)){
			$line = fgets($xml_handle, 1024);
			if (ereg('<gallery [^>]+>', $line)){
				// if line starts <gallery then check is on page then extract size
				$width = ereg_replace('(.+)(width=")([^"]+)(")(.+)', '\\3', $line);
				$height = ereg_replace('(.+)(height=")([^"]+)(")(.+)', '\\3', $line);
			}
		}
		// close xml file
		fclose ($xml_handle);

		header('location:'.RAD_LOCATION.'?type='.$type.'&process='.$process.'&return='.$return.'&height='.$height.'&width='.$width.'&extra='.$extra);
		exit;

		break;

	case 'header':
		// use header parameters from config
		$extra = $_GET['extra'];
		$location = HEADER_PATH.$extra.'/';
		$gallery_xml = $location.'content.xml';
		$xml_handle = fopen($gallery_xml, "r");

		while (!feof($xml_handle)){
			$line = fgets($xml_handle, 1024);
			if (ereg('<gallery [^>]+>', $line)){
				// if line starts <gallery then check is on page then extract size
				$width = ereg_replace('(.+)(width=")([^"]+)(")(.+)', '\\3', $line);
				$height = ereg_replace('(.+)(height=")([^"]+)(")(.+)', '\\3', $line);
			}
		}
		// close xml file
		fclose ($xml_handle);

		header('location:'.RAD_LOCATION.'?type='.$type.'&process='.$process.'&return='.$return.'&height='.$height.'&width='.$width.'&extra='.$extra);
		exit;

		break;

	case 'replace':
		// use existing image height and width
		$extra = $_GET['image'];
		list($width, $height, $_type, $attr) = getimagesize(IMAGE_PATH.$extra);

		header('location:'.RAD_LOCATION.'?type='.$type.'&process='.$process.'&return='.$return.'&height='.$height.'&width='.$width.'&extra='.$extra);
		exit;

		break;

	case 'upload':
		// no height and width to pass

		header('location:'.RAD_LOCATION.'?type='.$type.'&process='.$process.'&return='.$return);
		exit;

		break;

}





?>