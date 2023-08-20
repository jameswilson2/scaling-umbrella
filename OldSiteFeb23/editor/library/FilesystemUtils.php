<?php

/**
	Normalize a filename and use the $root argument to form an absolute
	filename.
	
	On making a change to this function, run these test cases and make sure they pass:
		assert(normalize_filename("test.jpg", "/home/site/public/images") == "/home/site/public/images/test.jpg");
		assert(normalize_filename("sub/test.jpg", "/home/site/public/images") == "/home/site/public/images/sub/test.jpg");
		assert(normalize_filename("sub/.././test.jpg", "/home/site/public/images") == "/home/site/public/images/test.jpg");
		assert(normalize_filename("/../../test.jpg", "/home/site/public/images") == "/home/site/public/images/test.jpg");
		assert(normalize_filename("../././////test.jpg", "/home/site/public/images") == "/home/site/public/images/test.jpg");
		assert(normalize_filename("..\\././/\\//test.jpg", "/home/site/public/images") == "/home/site/public/images/test.jpg");
*/
function normalize_filename($filename, $root){
	
	$filename_parts = pathinfo($filename);
	$filename_dirname = str_replace("\\", "/", $filename_parts['dirname']);
	$filename_basename = $filename_parts['basename'];
	
	$path = explode("/", $filename_dirname);
	$rebuilt_path = array();
	foreach($path as $directory){
		if($directory == '' || $directory == '.') continue;
		if($directory == '..'){
			array_pop($rebuilt_path);
		}
		else{
			$rebuilt_path[] = $directory;
		}
	}
	$rebuilt_path = implode("/", $rebuilt_path);
	
	$root_dirname = rtrim($root, "/");
	
	$output = array();
	if($root_dirname != '') $output[] = $root_dirname;
	if($rebuilt_path != '') $output[] = $rebuilt_path;
	if($filename_basename != '') $output[] = $filename_basename;
	return implode("/", $output);
}

// Search for a matching filename given an incomplete filename and an array of search paths
function find_file($filename, $locations){
	if(is_file($filename)){
		if($filename[0] != "/"){
			$filename = getcwd() . "/$filename";
		}
		return $filename;
	}
	foreach($locations as $location){
		$compound_path = "$location/$filename";
		if(is_file($compound_path)) return $compound_path;
	}
	return false;
}

// Search for given file, starting at the current working directory and ending at $topdir
function find_tree_file($filename, $topdir){
	$currentDirectory = str_replace('\\', '/', getcwd());
	$dirs = explode('/', $currentDirectory);
	$levelCount = count($dirs);
	for($i=($levelCount-1); $i>=1; $i--){
		$currentFilename = $currentDirectory . '/' . $filename;
		if(is_file($currentFilename)) return $currentFilename;
		if($currentDirectory . '/' == $topdir) return false;
		$currentDirectory = substr($currentDirectory, 0, strlen($currentDirectory) - strlen($dirs[$i]) - 1);
	}
}

// Generate an array of search paths from the parent directories between the root and the current directory.
// The paths are ordered in the returned array current to root.
function parent_search_paths($current_dir = null, $root_dir = null){
	
	if(!$current_dir){
		$current_dir = getcwd();
	}
	
	if(!$root_dir){
		$root_dir = EDITABLE_ROOT;
	}
	
	// Make paths consistent (removes redundant slash chars)
	$current_dir = dirname($current_dir . '/.');
	$root_dir = dirname($root_dir . '/.');
	
	assert(is_dir($current_dir) && is_dir($root_dir));
	assert(strpos($current_dir, $root_dir) !== false); // Check current dir is a child of root dir
	
	if($current_dir == $root_dir){
		return array($current_dir);
	}
	
	$path = $current_dir;
	$path_length = strlen($path);
	
	$segments = explode('/', substr($current_dir, strlen($root_dir) + 1));
	
	$search_paths = array();
	$search_paths[] = $current_dir;
	
	for($i = count($segments) - 1; $i >= 0; $i--){
		$path_length -= strlen($segments[$i]) + 1;
		$path = substr($path, 0, $path_length);
		$search_paths[] = $path;
	}
	
	return $search_paths;
}

function unique_filename($filename){
	$original_filename_pathinfo = pathinfo($filename);
	$original_filename_dirname = $original_filename_pathinfo['dirname'];
	$original_filename_filename = $original_filename_pathinfo['filename'];
	$original_filename_extension = $original_filename_pathinfo['extension'];
	$copy = 0;
	while(file_exists($filename)){
		$copy++;
		$filename = "$original_filename_dirname/$original_filename_filename$copy.$original_filename_extension";
	}
	return $filename;
}

function unique_basename($basename, $directories){
    
    if(!is_array($directories)){
        $directories = array($directories);
    }
    
    $basename_pathinfo = pathinfo($basename);
    $original_filename = $basename_pathinfo["filename"];
    $original_extension = $basename_pathinfo["extension"];
    
    $copy = 0;
    
    for($i = 0, $length = count($directories); $i < $length; $i++){
        
        $dir = $directories[$i];
        
        if(file_exists($dir . "/" . $basename)){
            $copy++;
            $basename = $original_filename . $copy . "." . $original_extension;
            $i = 0;
        }
    }
    
    return $basename;
}

function relative_filename($filename, $root = EDITABLE_ROOT){
	if($filename[0] != "/"){
		return $filename;
	}
    $found = strpos($filename, $root);
	if($found === false || $found != 0){
        return false;
    }
    return substr($filename, strlen($root));
}

function filename_extension($filename){
    $info = pathinfo($filename);
    return $info["extension"];
}

