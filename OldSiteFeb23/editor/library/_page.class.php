<?php

class Page {

	var $header;
	var $footer;

	var $menu;

	var $title;

	function Page ($menus){
		$this->title= SITE_NAME.' - Web Site Management';
		$this->menu = '<ul><li><a href="index.php">Home</a></li></ul>';

		if(isset($_SESSION['authorized'])){
			if($_SESSION['user_admin']=='Yes'){
				$items = array('access');
				$items = array_merge($items, $menus);
			} else {
				$items = $_SESSION['user_allowed_modules'];
			}
			foreach ($items as $item){
				$this->addMenuItem($item);
			}
		}

		$this->menu .= "<ul><li><a href=\"index.php?logout=1\">Logout</a></li></ul>";
	}

	function setTitle($title){
		$this->title = $title;
	}

	function addMenuItem($item){
		// passed single folders to look in for menu items
		// each one is simple html file

		$location = EDITABLE_ROOT.'editor/'.$item.'/menu.inc.php';
		$filecontent = file_get_contents($location);
		$this->menu .= $filecontent;
	}

	function addScript($script){
		$this->script = $script;
	}

	function buildHeader(){
		$base_href= WEB_ROOT.'editor/';
		$site_name = SITE_NAME;
		$this->header = <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>

	<title>$this->title</title>
	<meta name="author" content="SCK Web Works - www.sck-webworks.co.uk" />
	<meta name="copyright" content="Copyright SCK Web Works" />
	<meta name="robots" content="all" />

	<meta http-equiv="content-language" content="en-GB" />
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

	<base href="$base_href" />

	<link href="presentation/screen.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="presentation/sck.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="presentation/print.css" rel="stylesheet" type="text/css" media="print" />
	<!--[if IE]><link href="presentation/ie.css" rel="stylesheet" type="text/css" media="all"><![endif]-->
	<!--[if IE 7]><link href="presentation/ie7.css" rel="stylesheet" type="text/css" media="all"><![endif]-->
	<!--[if IE 6]><link href="presentation/ie6.css" rel="stylesheet" type="text/css" media="all"><![endif]-->
	<!--[if lt IE 6]>
		<link href="presentation/ie5.css" rel="stylesheet" type="text/css" media="all">
		<script src="behaviour/ie5.js" type="text/javascript"></script>
	<![endif]-->

	<script src="behaviour/swfobject.js" type="text/javascript"></script>
	<script src="behaviour/ajax.js" type="text/javascript"></script>
	<script src="behaviour/jquery-1.5.1.min.js" type="text/javascript"></script>
	<script src="behaviour/jquery.color.js" type="text/javascript"></script>
	
	<link href="../fancybox/jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" media="screen" />
	<script src="../fancybox/jquery.fancybox-1.3.4.js" type="text/javascript"></script>
	
	<script src="zeroclipboard/ZeroClipboard.js" type="text/javascript"></script>
	<script src="behaviour/behaviours.js" type="text/javascript"></script>
	
	<link href="calendar/datepicker.css" rel="stylesheet" type="text/css" media="screen" />
	<script type="text/javascript" src="calendar/datepicker.js"></script>

	$this->script

	<link rel="icon" href="http://www.sck-editor.co.uk/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="http://www.sck-editor.co.uk/favicon.ico" type="image/x-icon" />

</head>

<body>
<div id="container">

  <div id="header">
		<div id="header_padding">
			<h1><a href="/" title="SCK Web Editor"><span>SCK Web Editor</span></a></h1>
			<div class="article-left"><strong>You are editing:</strong> $site_name</div>
			<div class="article-rightalt">The SCK Web Editor is provided by <strong><a href="http://www.sck-webworks.co.uk" target="_blank">SCK Web Works</a></strong></div>
			<div class="clear"></div>
		</div>
  </div>

  <div id="article">
  	<div id="article_padding">
EOD;

	}

	function buildFooter(){
		$this->footer = <<<EOD
			<div class="clear"></div>
   	</div>
  </div>

  <div id="sidebar">
		<div id="sidebar_padding">
			<div id="nav">
				$this->menu
			</div>
			<div class="sidebar-panel">
				<div class="sidebar-panel-padding">
					<p><strong>Are you stuck?</strong><br />If you are having difficulty trying to use parts of this editor, please browse the <a href="javascript:popUp('http://www.sck-editor.co.uk/help/index.php?popup=true',640,580,50,50,1);">help pages</a>.<br />If you are still having difficulty, please <a href="contact.php">contact</a> us for help.</p>
					<ul>
						<li><a href="javascript:popUp('http://www.sck-editor.co.uk/help/index.php?popup=true',640,580,50,50,1);">Help Pages</a></li>
						<li><a href="contact.php">Contact</a></li>
					</ul>
				</div>
			</div>
		</div>
  </div>

  <div class="clear"></div>

  <div id="footer"></div>

</div>

</body>
</html>

EOD;

	}

	function getHeader(){
		$this->buildHeader();
		return $this->header;
	}

	function getFooter(){
		$this->buildFooter();
		return $this->footer;
	}

}

class PageSimple {

	var $header;
	var $footer;

	var $title;

	function PageSimple (){
		$this->title= SITE_NAME.' - Web Site Management';
	}

	function setTitle($title){
		$this->title = $title;
	}

	function buildHeader(){
		$base_href= WEB_ROOT.'editor/';
		$site_name = SITE_NAME;
		$this->header = <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>

	<title>$this->title</title>
	<meta name="author" content="SCK Web Works - www.sck-webworks.co.uk" />
	<meta name="copyright" content="Copyright SCK Web Works" />
	<meta name="robots" content="all" />

	<meta http-equiv="content-language" content="en-GB" />
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />

	<base href="$base_href" />

	<link href="presentation/screen.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="presentation/sck.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="presentation/print.css" rel="stylesheet" type="text/css" media="print" />
	<!--[if IE]><link href="presentation/ie.css" rel="stylesheet" type="text/css" media="all"><![endif]-->
	<!--[if IE 7]><link href="presentation/ie7.css" rel="stylesheet" type="text/css" media="all"><![endif]-->
	<!--[if IE 6]><link href="presentation/ie6.css" rel="stylesheet" type="text/css" media="all"><![endif]-->
	<!--[if lt IE 6]>
		<link href="presentation/ie5.css" rel="stylesheet" type="text/css" media="all">
		<script src="behaviour/ie5.js" type="text/javascript"></script>
	<![endif]-->

	<script src="behaviour/jquery-1.5.1.min.js" type="text/javascript"></script>
	<link href="../fancybox/jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" media="screen" />
	<script src="../fancybox/jquery.fancybox-1.3.4.js" type="text/javascript"></script>
	<script src="behaviour/swfobject.js" type="text/javascript"></script>

	<link rel="icon" href="http://www.sck-editor.co.uk/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="http://www.sck-editor.co.uk/favicon.ico" type="image/x-icon" />

</head>

<body>
<div id="containerSimple">

  <div id="header">
		<div id="header_padding">
			<h1><a href="/" title="SCK Web Editor"><span>SCK Web Editor</span></a></h1>
			<div class="article-left"><strong>You are editing:</strong> $site_name</div>
			<div class="article-rightalt">The SCK Web Editor is provided by <strong><a href="http://www.sck-webworks.co.uk" target="_blank">SCK Web Works</a></strong></div>
			<div class="clear"></div>
		</div>
  </div>

  <div id="articleSimple">
  	<div id="article_padding">
EOD;

	}

	function buildFooter(){
		$this->footer = <<<EOD
			<div class="clear"></div>
   	</div>
  </div>

  <div class="clear"></div>

  <div id="footerSimple"></div>

</div>

</body>
</html>

EOD;

	}

	function getHeader(){
		$this->buildHeader();
		return $this->header;
	}

	function getFooter(){
		$this->buildFooter();
		return $this->footer;
	}

}

?>