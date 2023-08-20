<?php

function traverseDirTree($tree_start,$start,$url,$fileFunc=null,$dirFunc=null,$afterDirFunc=null){

	$disallowed_folders = $GLOBALS["disallowed_folders"];

	$directory=opendir(EDITABLE_ROOT.$tree_start.$start);
	while (($entry=readdir($directory))!==false){
		$path=$start.$entry.'/';
		if (is_file(EDITABLE_ROOT.$tree_start.$start.$entry)){
			if ($fileFunc!==null && eregi('.html?$', $entry)){
				$content .= $fileFunc($start.$entry, $url, $tree_start);
			}
		} elseif (is_dir(EDITABLE_ROOT.$tree_start.$path)) {
			// check if allowed directory - if not don''t check for sub directories
			$match=0;

			foreach($disallowed_folders as $dis){
				if($path==$dis){
					$match++;
				}
			}


			if (($entry!='.') && ($entry!='..') && $dirFunc!==null && $match==0){
				$content .= $dirFunc($path, $url, $tree_start);
			}
			if (($entry!='.') && ($entry!='..')  && $match==0){
				$content .= traverseDirTree($tree_start,$path,$url,$fileFunc,$dirFunc,$afterDirFunc);
			}
			if ($afterDirFunc!==null){
				$afterDirFunc($path);
			}
		}
	}

	closedir($directory);

	return $content;
}

function displayPath($path, $url, $tree_start){
	$level=substr_count($path,'/');
	for ($i=1;$i<($level+1);$i++){
		$bullets .= '...';
	}

	if (file_exists(EDITABLE_ROOT.$tree_start.$path.'_template.sck.tpl')){
		$template = " [T]";
	}

	if ($path==$_GET['location']){
		$item = "<li class=\"level$level active\">$bullets<a href=\"$url?location=$path\">".basename($path)."</a>$template</li>";
	} else {
		$item = "<li class=\"level$level\">$bullets<a href=\"$url?location=$path\">".basename($path)."</a>$template</li>";
	}
	return $item;
}


function selectBox($path, $url, $tree_start){
	$level=substr_count($path,'/');
	for ($i=1;$i<($level+1);$i++){
		$bullets .= '...';
	}

	if (file_exists(EDITABLE_ROOT.$tree_start.$path.'_template.sck.tpl')){
		$template = " [T]";
	}

	if ($path==$url){
		$item = "<option value=\"$path\" selected=\"selected\">".$path."$template</option>";
	} else {
		$item = "<option value=\"$path\">".$path."$template</option>";
	}

	return $item;

}


function checkBox($path, $url, $tree_start){
	$level=substr_count($path,'/');
	for ($i=1;$i<($level+1);$i++){
		$bullets .= '...';
	}

	if (file_exists(EDITABLE_ROOT.$tree_start.$path.'_template.sck.tpl')){
		$template = " [T]";
	}

	if (!in_array($tree_start.$path, $url)){
		$item = "<p><label class=\"left\">$tree_start$path</label><span class=\"other\"><input type=\"checkbox\" checked=\"checked\" value=\"$tree_start$path\" name=\"user_allowed_folders[]\" /></span></p>";
	} else {
		$item = "<p><label class=\"left\">$tree_start$path</label><span class=\"other\"><input type=\"checkbox\" value=\"$tree_start$path\" name=\"user_allowed_folders[]\" /></span></p>";
	}

	return $item;

}


function getDisallowedFolders($path, $url, $tree_start){

	if(!in_array($tree_start.$path, $url)){
		$item = $tree_start.$path.',';
	}

	return $item;

}


function fileCheckBox($file, $url, $tree_start){
	if (!in_array($tree_start.$file, $url)){
		$item = "<p><label class=\"left\">$tree_start$file</label><span class=\"other\"><input type=\"checkbox\" checked=\"checked\" value=\"$tree_start$file\" name=\"user_allowed_files[]\" /></span></p>";
	} else {
		$item = "<p><label class=\"left\">$tree_start$file</label><span class=\"other\"><input type=\"checkbox\" value=\"$tree_start$file\" name=\"user_allowed_files[]\" /></span></p>";
	}
	return $item;
}



function getDisallowedFiles($file, $url, $tree_start){

	if(!in_array($tree_start.$file, $url)){
		$item = $tree_start.$file.',';
	}

	return $item;

}


function folderList($path, $url, $tree_start){

}


?>