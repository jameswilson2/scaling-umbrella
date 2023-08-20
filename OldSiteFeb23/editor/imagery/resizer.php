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
$rand = rand();
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

	<div id="controls"></div>
	<div class="cropper_cont"><div class="cropper"><div class="fullImage"><img src="temp/$filename?rand=$rand" alt="" /></div></div></div>

	<script type="text/javascript">
	<!--
	$('.fullImage').imageCropper({
		filename:'$filename',
		extra:'$extra',
		type:'$type',
		width:$width,
		height:$height,
		resizable:false,
		showSizeSelect:false,
		xmlurl:'imagery/configuration.xml'
	});
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

	<div id="controls"></div>
	<div class="cropper_cont"><div class="cropper"><div class="fullImage"><img src="temp/$filename?rand=$rand" alt="" /></div></div></div>

	<script type="text/javascript">
	<!--
	$('.fullImage').imageCropper({
		filename:'$filename',
		extra:'$extra',
		type:'$type',
		width:$width,
		height:$height,
		resizable:false,
		showSizeSelect:false,
		xmlurl:'imagery/configuration.xml'
	});
	// -->
	</script>

EOD;

		break;

	case 'upload':
		// load flash with image sizes - flash decides image sizes
		// $filename
		// $type

		$content = <<<EOD

	<div id="controls"></div>
	<div class="cropper_cont"><div class="cropper"><div class="fullImage"><img src="temp/$filename?rand=$rand" alt="" /></div></div></div>

	<script type="text/javascript">
	<!--
	$('.fullImage').imageCropper({
		filename:'$filename',
		extra:'$extra',
		type:'$type',
		xmlurl:'imagery/configuration.xml'
	});
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
<style type="text/css">
.cropper { position:relative; float:left;  }
.cropper img { display:block; }
.cover { position:absolute; background:#000; }
.resizer { border:#000 1px dashed; position:absolute; background-attachment:fixed; _background-attachment:scroll; z-index:2; overflow:hidden; cursor:move; }
.ui-resizable-handle { position:absolute; width:10px; height:10px; background:#fff; border:#000 1px solid; overflow:hidden; }
.ui-resizable-n, .ui-resizable-ne, .ui-resizable-nw { top:-1px; }
.ui-resizable-nw, .ui-resizable-w, .ui-resizable-sw { left:-1px; }
.ui-resizable-ne, .ui-resizable-e, .ui-resizable-se { margin-left:-5px; }
.ui-resizable-s, .ui-resizable-se, .ui-resizable-sw { margin-top:-5px; }
.ui-resizable-n, .ui-resizable-s { cursor:n-resize; }
.ui-resizable-e, .ui-resizable-w { cursor:e-resize; }
.ui-resizable-nw, .ui-resizable-se { cursor:nw-resize; }
.ui-resizable-ne, .ui-resizable-sw { cursor:ne-resize; }
.cropper_cont { width:100%; overflow:auto; }
.clear { clear:both; height:0px; overflow:hidden; }
.ui-draggable-dragging img { display:none; }
#cropwidth, #cropheight { width:50px; }
.ui-selectable-helper { z-index:30; border:#000 1px dashed; position:absolute; }
.inlineForm { display:inline; }
#controls { padding-bottom:5px; }
input[type="button"], input[type="submit"] { 
background-image: -webkit-gradient(linear,left top,left bottom,color-stop(0, rgb(168,189,226)),color-stop(1, rgb(43,112,173)));
background-image: -ms-linear-gradient(top,#a8bde2,#2b70ad);
background-image: -moz-linear-gradient(center bottom,rgb(168,189,226) 0%,rgb(43,112,173) 100%);
filter: progid:DXImageTransform.Microsoft.Gradient(GradientType=0,StartColorStr=#FFa8bde2,EndColorStr=#FF2b70ad);
-ms-filter: "progid:DXImageTransform.Microsoft.Gradient(GradientType=0,StartColorStr=#FFa8bde2,EndColorStr=#FF2b70ad)";
border-radius:10px; font-weight:bold; color:#fff; width:auto; overflow:visible; padding:6px 10px; border:0;
}
</style>
<script type="text/javascript" src="behaviour/jquery-ui.js"></script>
<script type="text/javascript" src="behaviour/jquery.ui.touch-punch.min.js"></script>
<script type="text/javascript" src="behaviour/jquery.imageCropper.js"></script>

<div id="panelOne"><p><strong class="blue">Instructions:</strong> Do you need some simple instructions on how to use this, if so <a href="javascript:showInstructions();">click here</a>!</p></div>
<div id="panelTwo">
<p><strong class="blue">Simple Instructions:</strong> <a href="javascript:hideInstructions();">Hide these instructions!</a></p>
<p><strong class="blue">Select Box Size:</strong> Use this to select the size of photo you want (it will not work if you are adding a header or replacing a photo)<br />
<strong class="blue">Fix to Box:</strong> Will take the photo and scale it to the size of the ligher area automatically.<br />
<strong class="blue">Enlarge / Reduce:</strong> This will make your photo bigger or smaller.<br />
<strong class="blue">Finish &amp; Crop:</strong> Will only appear if your photo is the same size or larger than the target size of the photo you want!</p>
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
















