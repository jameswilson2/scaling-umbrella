<head>
<link rel="stylesheet" href="https://npmcdn.com/leaflet@1.3.4/dist/leaflet.css" />
<script src="https://npmcdn.com/leaflet@1.3.4/dist/leaflet.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js" type="text/javascript"></script>

</head>
<?php
require_once 'library/security/_access.inc.php';
require_once 'library/captcha.class.php';
require_once 'files/_php_builder.class.php';

ob_start();



?>

<div class="map_left">
	<?php
		echo file_get_contents('_content.inc');
	?>
	<div id="dirctions">&nbsp;<div class="directionsDetails"><h2 class="nopad colour">Get Directions</h2><label>Your Postcode</label> <input class="postcode autoText defaulted" data-default="enter your postcode" id="postcode"> <input type="button" value="> Get Route" class="directionsSearch" onclick="javascript:getDirections();"></div></div>
</div>

<div id="map2ed"></div>
<div class="clearspace"></div>
<div id="dirctionsresults"></div>
<div class="break_line"></div>
<h2 class="colour">Contact Us</h2>
<p>Enter your details and we will contact you as soon as possible.</p>
<div class="trds contactform cs">

<?php if($_GET['error']==1): ?>
<div class="sck-error-box">
	<p><strong>There were errors in submitting your details - please check and complete.</strong><br />
	If you are getting "Considered as SPAM", please remove any HTML formatting from the relevant field. This includes the character &lt; unfortunately.</p>
</div>
<?php endif ?>

<form id="sck-form" method="post" action="enquiries/sendmail.php">

<div class="trd">
<?php if($_GET['name']==1): ?>
<div class="sck-error-p">Your name is considered as SPAM - please review.</div>

<?php elseif($_GET['name']==2): ?>
<div class="sck-error-p">Please enter your name.</div>
<?php endif ?>

<label for="formName"><strong>Your Name:</strong></label>
<input type="text" name="formName" id="formName" class="field" value="<?php echo $_SESSION['name']; ?>" />
</div>

<div class="trd">
<?php if($_GET['email']==1): ?>
<div class="sck-error-p">Please enter a valid email address.</div>
<?php elseif($_GET['email']==2): ?>
<div class="sck-error-p">Your email address is considered as SPAM - please review.</div>
<?php elseif($_GET['email']==3): ?>
<div class="sck-error-p">Please enter your email address.</div>
<?php endif ?>

<label for="formEmail"><strong>Email Address:</strong></label>
<input type="text" name="formEmail" id="formEmail" class="field" value="<?php echo $_SESSION['email']; ?>" />
</div>

<div class="trd">
<?php if($_GET['tel']==1): ?>
<div class="sck-error-p">Your telephone number is considered as SPAM - please review.</div>

<?php elseif($_GET['tel']==2): ?>
<div class="sck-error-p">Please enter your telephone number.</div>
<?php endif ?>
<label for="formTele"><strong>Telephone Number:</strong></label>
<input type="text" name="formTele" id="formTele" class="field" value="<?php echo $_SESSION['telephone']; ?>" />
</div>

<div class="trd">
<label for="formService"><strong>Type of Service Required:</strong></label>
<input type="text" name="formService" id="formService" class="field" value="<?php echo $_SESSION['servicerequired']; ?>" />
</div>

<div class="trd">
<label for="formAddress"><strong>Address:</strong></label>
<input type="text" name="formAddress" id="formAddress" class="field" value="<?php echo $_SESSION['address']; ?>" />
</div>

<div class="trd">
<label for="formPostcode"><strong>Post Code:</strong></label>
<input type="text" name="formPostcode" id="formPostcode" class="field" value="<?php echo $_SESSION['postcode']; ?>" />
</div>

<div class="twotrd">
<?php if($_GET['mes']==1): ?>
<div class="sck-error-p">Your message is considered as SPAM - please review.</div>
<?php elseif($_GET['mes']==2): ?>
<div class="sck-error-p">Please enter your message.</div>
<?php endif ?>

<label for="formMessage"><strong>Your Message:</strong></label>
<textarea cols="20" id="formMessage" name="formMessage" class="field"><?php echo $_SESSION['message']; ?></textarea>
</div>

<div class="clear"></div>
<?php if(!$_SESSION['captcha_done']):

	$human_test = Captcha::generateArithmeticProblem();
	$human_test_problem = $human_test->getPresentation();
	$human_test_key = $human_test->getKey();
	$human_test->saveToSession();
?>
	<div class="trd">
	<?php if($_GET['human_test']==1): ?>
	<div class="sck-error-p">Please solve the problem as it appears below.</div>
	<?php endif ?>
	<br />
	<label for="human_test"><strong>Spam filter <img src="presentation/question.gif" id="spam_filter_info" title="This is a protection feature against automated form submission." alt="This is a protection feature against automated form submission."/>:</strong></label>
		<span class="other">Solve this problem: <?php echo $human_test_problem;?> <input type="text" name="human_test" id="human_test" size="3" />
		</span>
		<input type="hidden" name="human_test_key" value="<?php echo $human_test_key; ?>" />
	</div>
<?php endif ?>
<br />

	<div class="trd">
		<div class="clear5px"></div><button type="submit" class="btn rbtn">&gt; Submit Form</button>
	</div>
	<div class="clear"></div>
</div>
<div class="clear"></div>
<div class="clearspace"></div>

<p>Data sent from this form is recorded in line with our <a href="privacypolicy.htm">Privacy Policy</a>.</p>

<div class="clear"></div>
</form>

<?php

$title = "Contact Us - ".SITE_NAME;
$description = "Contact Us - ".SITE_NAME;
$keywords = "Contact Us - ".SITE_NAME;

$content = ob_get_contents();
ob_end_clean();

$page = new PHPBuilder();

$page->setTitle($title);
$page->setDescription($description);
$page->setKeywords($keywords);
$page->setContent($content);

$page_content = $page->buildPage();

echo $page_content;

?>

<script type="text/javascript">
var startPoint = [54.3457717,-2.7358015];
var map = L.map('map2ed', {editable: false}).setView(startPoint, 10),
    tilelayer = L.tileLayer('http://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {maxZoom: 25, attribution: 'Data \u00a9 <a href="http://www.openstreetmap.org/copyright"> OpenStreetMap Contributors </a> Tiles \u00a9 HOT'}).addTo(map);
    L.EditControl = L.Control.extend({
        options: {
            position: 'topleft',
            callback: null,
            kind: '',
            html: ''
        },
        onAdd: function (map) {
            var container = L.DomUtil.create('div', 'leaflet-control leaflet-bar'),
                link = L.DomUtil.create('a', '', container);
            link.href = '#';
            link.title = 'Create a new ' + this.options.kind;
            link.innerHTML = this.options.html;
            L.DomEvent.on(link, 'click', L.DomEvent.stop)
                      .on(link, 'click', function () {
                        window.LAYER = this.options.callback.call(map.editTools);
                      }, this);
            return container;
        }
    });	
	var rec = L.marker(
        startPoint
    ).addTo(map);
    var poly = L.polygon([
      [
	    [
			[54.11215,-3.130524],
			[54.152784,-3.147004],
			[54.202617,-3.144257],
			[54.254796,-3.139944],
			[54.343649,-3.111298],
			[54.376858,-3.078188],
			[54.377657,-3.046517],
			[54.347602,-3.060250],
			[54.285470,-3.094539],
			[54.254394,-3.085613],
			[54.268181,-2.976307],
			[54.248277,-2.931675],
			[54.320929,-2.890885],
			[54.33174,-2.934144],
			[54.350954,-2.968476],
			[54.373359,-2.968476],
			[54.394152,-2.966416],
			[54.418131,-2.988389],
			[54.426121,-2.957489],
			[54.404544,-2.911484],
			[54.373359,-2.805054],
			[54.391353,-2.683464],
			[54.355356,-2.671791],
			[54.327937,-2.629219],
			[54.271740,-2.640409],
			[54.106515,-2.801170],
			[54.160022,-2.888889],
			[54.193177,-2.834183],
			[54.149567,-2.930775]
        ],
		[
			[54.325685744798726,-2.52493629057426],
			[54.306129814628264,-2.4714200024027377],
			[54.293638393828175,-2.39932308846619],
			[54.29906014884144,-2.3697355308104306],
			[54.31055048524376,-2.340450083138421],
			[54.320327557286845,-2.315711115952581],
			[54.33210322649086,-2.308241258142516],
			[54.34581823815681,-2.3221775761339813],
			[54.37550630844876,-2.336767923552543],
			[54.39576381979681,-2.3261086153797805],
			[54.470037612805754,-2.348499298095703],
			[54.45272605356699,-2.35231876373291],
			[54.42919954046966,-2.3421692533884197],
			[54.39247122105509,-2.3493412655079737],
			[54.36235881641611,-2.3535796167561784],
			[54.32698212928454,-2.336007496342063],
			[54.31290659277615,-2.3619090544525534],
			[54.312671010937144,-2.3733713175170124],
			[54.314083981331414,-2.389323797542602],
			[54.323048038428574,-2.4348651370382868]
		]
	]	  
    ]).addTo(map);
</script>

<script>
function getDirections() {
	if (isValidPostcode(document.getElementById("postcode").value)) {
		window.open('https://www.google.co.uk/maps/dir/' + document.getElementById("postcode").value + '/54.3457717,-2.7358015/', '_blank')
	} else {
		alert(document.getElementById("postcode").value + " seems to not be a valid UK postcode, re-enter your postcode and try again")
	}
}

function isValidPostcode(p) {
	var postcodeRegEx = /[A-Z]{1,2}[0-9]{1,2}[A-Z]{0,1} ?[0-9][A-Z]{2}/i;
	return postcodeRegEx.test(p);
}
</script>