<?php

$sql = "SELECT count(contact_id) AS num_contacts FROM tbl_contact WHERE (contact_date + INTERVAL 7 DAY)>=NOW()";

$result = getSimpleQuery($sql,'Could not get contacts');

$row = mysql_fetch_array($result);

$num_contacts = $row['num_contacts'];

$content =<<<EOD
<h2>Enquiries</h2>
<ul>
	<li><a href="enquiries/">Manage Enquiries</a></li>
EOD;

if($num_contacts==1){
	$content .= "<li>You have $num_contacts enquiry in the last 7 days. <a href=\"enquiries/\">View</a></li>";
} elseif($num_contacts!=0) {
	$content .= "<li>You have $num_contacts enquiries in the last 7 days. <a href=\"enquiries/\">View</a></li>";
} else {
	$content .= "";
}

$content .= "</ul>";
echo $content;

?>