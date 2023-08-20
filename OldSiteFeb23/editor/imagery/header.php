<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';
require_once 'library/_page.class.php';

$page = new Page($menus);

$header = $page->getHeader();
$footer = $page->getFooter();

echo $header; ?>
<h2><?php echo SITE_NAME; ?> - Edit Flash Headers</h2>
<ul class="filelist">
<?php
$location = HEADER_PATH;

// open current directory
$dp = opendir($location);
$dir_list = array();
// loop through the directory
while (false !== ($directory = readdir($dp))) {
	$match = 0;
	$disallowed = array('.', '..');
	foreach ($disallowed as $dis) {
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

$i=1;

foreach ($dir_list as $dir){
	$sublocation = $location.$dir.'/';
	?>
	<li>
		<div class="actionimg"><a href="imagery/header_sort.php?folder=<?php echo $dir; ?>">Sort the photos in this gallery</a></div>
		<img src="presentation/folder_closed.gif" id="image<?php echo $dir; ?>" width="16" height="13" alt="directory" />
	<a class="folder" href="imagery/header_detail.php?folder=<?php echo $dir; ?>"><?php echo $dir; ?></a>
	</li>
<?php
}
?>
</ul>
<?php echo $footer; ?>
