<?php
require_once 'library/security/_access.inc.php';
require_once 'library/_extras_dbconn.inc.php';
require_once 'library/verification/_random_text.inc.php';
require_once 'library/_paging.class.php';

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

$page_title = SITE_NAME.' - Customer Feedback';

include '../_header.inc.php'; ?>
<h2><?php echo SITE_NAME; ?> Customer Comments</h2>
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

	<?php if($_GET['image']==1): ?>
	<div class="sck-error-p">Please enter the text as it appears below.</div>
	<?php endif ?>

	<p>
		<label for="image_text">Enter the text as it appears in the image:</label>
		<span class="other"><img src="editor/library/verification/_random_image.php" alt="" />
		<input type="text" name="image_text" id="image_text" class="field" /></span>
	</p>

	<p>
		<input name="SignUpNow" type="submit" id="SendEmail" value="Submit Comment Now!" />
		<input name="ClearForm" type="reset" id="ClearForm" value="Clear Form" />
	</p>
	<div class="clear"></div>
</form>

<?php include '../_footer.inc.php'; ?>