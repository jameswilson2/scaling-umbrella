<?php

$sql = "SELECT count(comment_id) AS num_comments FROM tbl_comments WHERE comment_status='pending'";

$result = getSimpleQuery($sql,'Could not get comments: ');

$row = mysql_fetch_array($result);

$num_comments = $row['num_comments'];

$content = <<<EOD
<h2>Customer Comments</h2>
<ul>
EOD;

if($num_comments==1){
	$content .= "<li><strong>You have $num_comments new comment. <a href=\"comments/?comment_status=pending\">Moderate</a></strong></li>";
} elseif($num_comments!=0) {
	$content .= "<li><strong>You have $num_comments new comments. <a href=\"comments/?comment_status=pending\">Moderate them</a></strong></li>";
} else {
	$content .= "";
}

$sql = "SELECT count(comment_id) AS num_comments FROM tbl_comments WHERE comment_status='approved'";

$result = getSimpleQuery($sql,'Could not get comments: ');

$row = mysql_fetch_array($result);

$num_comments = $row['num_comments'];

if($num_comments==1){
	$content .= "<li>You have $num_comments active comment. <a href=\"comments/\">View All</a></li>";
} elseif($num_comments!=0) {
	$content .= "<li>You have $num_comments active comments. <a href=\"comments/\">View All</a></li>";
} else {
	$content .= "";
}

$content .= "</ul>";

echo $content;

?>