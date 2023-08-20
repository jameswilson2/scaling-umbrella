<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>### TITLE ###</title>
	<meta name="description" content="### META DESCRIPTION ###" />
	<meta name="keywords" content="### META KEYWORDS ###" />
	<meta name="author" content="SCK Web Works - www.sck-webworks.co.uk" />
	<meta name="robots" content="all" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<base href="http://www.kencomp.net/" />
	
	<link href="presentation/framework.css" rel="stylesheet" type="text/css" media="all" />
	<link href="presentation/screen.css" rel="stylesheet" type="text/css" media="screen" />
	
	<!-- INCLUDE MODULE "_extra_css.inc" -->
	<link href="presentation/print.css" rel="stylesheet" type="text/css" media="print" />
	<!--[if IE 6]><link href="presentation/ie6.css" rel="stylesheet" type="text/css" media="all"><![endif]-->
	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js" type="text/javascript"></script>
	<script src="behaviour/jquery-plugins.js" type="text/javascript"></script>
	<!-- INCLUDE MODULE "_extra_jslibs.inc" -->
	<link href="fancybox/jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" />
	<script src="fancybox/jquery.fancybox-1.3.4.js" type="text/javascript"></script>
	<script src="behaviour/package.js" type="text/javascript"></script>
	<script src="behaviour/jquery.imageGallery.js" type="text/javascript"></script>
	<script src="behaviour/jquery.infiniteScroll.js" type="text/javascript"></script>
	<script src="behaviour/presentation.js" type="text/javascript"></script>
	
	<link rel="icon" href="favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<!-- Google Analytics -->
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-34638189-1', 'auto');
ga('send', 'pageview');

</script>
<!-- End Google Analytics -->
</head>
<body>
	<div class="header">
		<div class="container cs">
			<div class="left_section">
				<a href="." class="logo"><img src="presentation/logo.png" alt="Kencomp" /></a>
			</div>
			<div class="right_section">
				<!-- INCLUDE MODULE "_contact.inc" -->
			</div>
		</div>
	</div>
	<nav>
		<div class="nav">
			<div class="back">
				<div class="container cs">
					<div id="nav">
						<!-- INCLUDE MODULE "_navigation.inc" -->
					</div>
				</div>
			</div>
		</div>
	</nav>
	<!-- INCLUDE MODULE "_gallery.inc" -->
	<div class="container">
		<div class="content">
			<div class="<!-- INCLUDE MODULE "_maincontent_class.inc" -->">
				<!-- ### CONTENT AREA ### -->
			</div>
			<div class="<!-- INCLUDE MODULE "_subcontent_class.inc" -->">
				<!-- INCLUDE MODULE "_subcontent.inc" -->
			</div>
			<div class="clear"></div>
			<!-- INCLUDE MODULE "_bottom_content.inc" -->
		</div>
	</div>
	<div class="undercontent">
		<div class="container">
			<!-- INCLUDE MODULE "_undercontent.inc" -->
		</div>
	</div>
	<div class="footer">
		<div class="container cs">
			<div class="left_section">
				<!-- INCLUDE MODULE "_footer_contact.inc" -->
			</div>
			<div class="right_section">
				<!-- INCLUDE MODULE "_footer_right.inc" -->
				<div class="footerline"&nbsp;></div>
			</div>
		</div>
	</div>
</body>
</html>