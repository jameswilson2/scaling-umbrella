<?php
require_once 'library/security/_access.inc.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'library/verification/_random_text.inc.php';
require_once 'library/_paging.class.php';
require_once 'library/captcha.class.php';
require_once 'files/_php_builder.class.php';
ob_start();

if (!isset($_SESSION['randomString'])){
	$_SESSION['randomString'] = createRandString();
}

$sql = "SELECT comment_name, comment_text, comment_datetime
		FROM tbl_comments
		WHERE comment_status='approved'
		ORDER BY comment_datetime DESC";

if (isset($_GET['page']) && $_GET['page']!=""){
	$page=$_GET['page'];
} else {
	$page=1;
}

$self = "comments/index.php";
$pager = new Pager($sql, '5', $self, '', $page);

$sql = $pager->getPagedQuery();
$paging_links = $pager->getPagingLinks();

$comments = getQuery($sql, 'Could not get comments: ');

?>
<h2><?php echo SITE_NAME; ?> Customer Feedback</h2>
<?php
while ($comment = mysql_fetch_array($comments)){
	$comment_name = htmlspecialchars($comment['comment_name']);
	$comment_text = htmlspecialchars($comment['comment_text']);
	$comment_datetime = $comment['comment_datetime'];

	$comment_text = ereg_replace("\r\n", "\n", $comment_text);
	$comment_text = ereg_replace("\r", "\n", $comment_text);
	$comment_text = ereg_replace("\n\n", '</p><p>', $comment_text);
	$comment_text = ereg_replace("\n", '<br />', $comment_text);

	$comment_datetime = date('l dS \of F Y \a\t h:ia' ,strtotime($comment_datetime));

	echo "<p><strong>$comment_name</strong> - <em>Posted on: $comment_datetime</em></p>
				<p>$comment_text</p>
		<hr />";
}

echo $paging_links;

?>
<p>To submit a comment, please fill in your details in the form below.<br />
All fields are required to be filled in.</p>
<?php if($_GET['error']==1): ?>
	<div class="sck-error-box">
		<p><strong>There were errors in submitting your details - please check and complete.</strong><br />
		If you are getting "Considered as SPAM", please remove any HTML formatting from the relevant field. This includes the character &lt; unfortunately.</p>
	</div>
<?php endif ?>
<form id="sck-form" method="post" action="comments/process.php">

	<?php if($_GET['name']==1): ?>
	<div class="sck-error-p">Your name is considered as SPAM - please review.</div>
	<?php elseif($_GET['name']==2): ?>
	<div class="sck-error-p">Please enter your name.</div>
	<?php endif ?>

	<p>
		<label for="formName"><strong>Your Name:</strong></label>
		<input type="text" name="formName" id="formName" class="field" value="<?php echo $_SESSION['name']; ?>" />
	</p>

	<?php if($_GET['email']==1): ?>
	<div class="sck-error-p">Please enter a valid email address.</div>
	<?php elseif($_GET['email']==2): ?>
	<div class="sck-error-p">Your email address is considered as SPAM - please review.</div>
	<?php elseif($_GET['email']==3): ?>
	<div class="sck-error-p">Please enter your email address.</div>
	<?php endif ?>

	<div class="sck-note-p">Your email address will not be published.</div>
	<p>
		<label for="formEmail"><strong>Email Address:</strong></label>
		<input type="text" name="formEmail" id="formEmail" class="field" value="<?php echo $_SESSION['email']; ?>" />
	</p>

	<?php if($_GET['mes']==1): ?>
	<div class="sck-error-p">Your message is considered as SPAM - please review.</div>
	<?php elseif($_GET['mes']==2): ?>
	<div class="sck-error-p">Please enter your message.</div>
	<?php endif ?>

	<p>
		<label for="formMessage"><strong>Your Comments:</strong></label>
		<textarea rows="6" cols="20" id="formMessage" name="formMessage" class="field"><?php echo $_SESSION['message']; ?></textarea>
	</p>

	<?php if(!$_SESSION['captcha_done']):

		$human_test = Captcha::generateArithmeticProblem();
		$human_test_problem = $human_test->getPresentation();
		$human_test_key = $human_test->getKey();
		$human_test->saveToSession();
	?>
		<?php if($_GET['human_test']==1): ?>
		<div class="sck-error-p">Please solve the problem as it appears below.</div>
		<?php endif ?>

		<p style="padding-top:5px">
			<label for="human_test">Spam filter <img src="presentation/question.gif" id="spam_filter_info" title="This is a protection feature against automated form submission."/>:</label>
			<span class="other">Solve this problem: <?php echo $human_test_problem;?> <input type="text" name="human_test" id="human_test" size="3" />
			</span>
			<input type="hidden" name="human_test_key" value="<?php echo $human_test_key; ?>" />
		</p>
	<?php endif ?>

	<p>
		<input name="SignUpNow" type="submit" id="SendEmail" value="Submit Comment Now!" />
	</p>
	<div class="clear"></div>
</form>

<?php
$content = ob_get_contents();
ob_end_clean();


$title = "Customer Feedback - ".SITE_NAME;
$description = "Customer Feedback - ".SITE_NAME;
$keywords = "Customer Feedback - ".SITE_NAME;

$page = new PHPBuilder();

$page->setTitle($title);
$page->setDescription($description);
$page->setKeywords($keywords);
$page->setContent($content);

$page_content = $page->buildPage();

echo $page_content;

?>