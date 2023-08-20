<?php
require_once 'library/security/_secure.inc.php';
require_once 'imagery/_images.config.php';
require_once 'library/_page.class.php';


if (!isset($_GET['filename'])){
	header('location:'.WEB_ROOT.'editor/imagery/index.php');
	exit;
}

// load flash editor with appropriate parameters (if required)

$filename = $_GET['filename'];
$width = $_GET['width'];
$height = $_GET['height'];
$extra = $_GET['extra'];

$source = '../temp/'.$filename;

$type = $_GET['type'];
switch ($type){
	case 'gallery':
		// redirect to save page
		header('location:'.WEB_ROOT.'editor/imagery/save_gallery.php?type='.$type.'&filename='.$filename.'&extra='.$extra);
		exit;

		break;

	case 'header':
		// load flash with target parameters and image sizes + location
		// $filename
		// $height
		// $width
		// $type
		// $extra

		$content = <<<EOD

	<div id="flash"></div>

	<script type="text/javascript">
	<!--
	var swfo = new SWFObject("imagery/default.swf?rand="+Math.random(), "flash_swf", "938", "550", "8", "#CCCCCC", true);
	swfo.addParam("base", "imagery/");
	swfo.addParam("allowFullScreen", "true");
	// Variables
	swfo.addVariable("source","$source");
	swfo.addVariable("fl_filename","$filename");
	swfo.addVariable("fl_type","$type");
	swfo.addVariable("fl_extras","$extra");
	swfo.addVariable("maskSetWidth",$width);
	swfo.addVariable("maskSetHeight",$height);
	// /Variables
	swfo.write("flash");
	// -->
	</script>

EOD;

		break;

	case 'replace':
		// load flash with target parameters and image sizes + original image name
		// $filename
		// $height
		// $width
		// $type
		// $extra

		$content = <<<EOD

	<div id="flash"></div>

	<script type="text/javascript">
	<!--
	var swfo = new SWFObject("imagery/default.swf?rand="+Math.random(), "flash_swf", "938", "550", "8", "#CCCCCC", true);
	swfo.addParam("base", "imagery/");
	swfo.addParam("allowFullScreen", "true");
	// Variables
	swfo.addVariable("source","$source");
	swfo.addVariable("fl_filename","$filename");
	swfo.addVariable("fl_type","$type");
	swfo.addVariable("fl_extras","$extra");
	swfo.addVariable("maskSetWidth",$width);
	swfo.addVariable("maskSetHeight",$height);
	// /Variables
	swfo.write("flash");
	// -->
	</script>

EOD;

		break;

	case 'upload':
		// load flash with image sizes - flash decides image sizes
		// $filename
		// $type

		$content = <<<EOD

	<div id="flash"></div>

	<script type="text/javascript">
	<!--
	var swfo = new SWFObject("imagery/default.swf?rand="+Math.random(), "flash_swf", "938", "550", "8", "#CCCCCC", true);
	swfo.addParam("base", "imagery/");
	swfo.addParam("allowFullScreen", "true");
	// Variables
	swfo.addVariable("source","$source");
	swfo.addVariable("fl_filename","$filename");
	swfo.addVariable("fl_type","$type");
	swfo.addVariable("fl_extras","$extra");
	swfo.addVariable("maskSetWidth",0);
	swfo.addVariable("maskSetHeight",0);
	// /Variables
	swfo.write("flash");
	// -->
	</script>

EOD;

		break;

}

$_page = new PageSimple();

$header = $_page->getHeader();
$footer = $_page->getFooter();

echo $header;

$instructions = <<<EOD
<div id="panelOne"><p><strong class="blue">Instructions:</strong> Do you need some simple instructions on how to use this, if so <a href="javascript:showInstructions();">click here</a>!</p></div>
<div id="panelTwo">
<p><strong class="blue">Simple Instructions:</strong> <a href="javascript:hideInstructions();">Hide these instructions!</a></p>
<p><strong class="blue">Select Box Size:</strong> Use this to select the size of photo you want (it will not work if you are adding a header or replacing a photo)<br />
<strong class="blue">Fix to Box:</strong> Will take the photo and scale it to the size of the ligher area automatically.<br />
<strong class="blue">Enlarge / Reduce:</strong> This will make your photo bigger or smaller.<br />
<strong class="blue">Finish &amp; Crop:</strong> Will only appear if your photo is the same size or larger than the target size of the photo you want!</p>
<p><strong>If the lighter area below is larger than the size of the editor below, click the <img src="presentation/fullscreen-icon.gif" width="18" height="16" alt="Fullscreen Button" class="minicon" /> button. Once you have edited, click "Finish &amp; Crop"!</strong> Clicking the <img src="presentation/fullscreen-icon.gif" width="18" height="16" alt="Fullscreen Button" class="minicon" /> button when fullscreen, will only mean you have to resize the photo again!</p>
<hr />
</div>
<script type="text/javascript">
<!--
$(document).ready(function() {	
	$("#panelTwo").hide();
});
function showInstructions() {
	$("#panelOne").slideUp("slow");
	$("#panelTwo").slideDown("slow");
}
function hideInstructions() {
	$("#panelOne").slideDown("slow");
	$("#panelTwo").slideUp("slow");
}
//-->
</script>
EOD;
echo $instructions;

echo $content;

echo $footer;
?>
















