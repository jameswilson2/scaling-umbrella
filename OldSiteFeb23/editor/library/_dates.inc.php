<?php
function buildDateOptions($field_name, $date){

	// validate date first

	// split date up into d/m/y then build select boxes based on this

	$date_part = explode("-", $date);

	$output =  "<select name='$field_name-dd' id='$field_name-dd' class='date_day'>";
	for ($i = 1; $i < 32; $i++){
		if ($i==$date_part[2]){
			$output .= "<option value='$i' selected='selected'>$i</option>";
		} else {
			$output .= "<option value='$i'>$i</option>";
		}
	}
	$output .= "</select>";


	$months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

	$output .= "<select name='$field_name-mm' id='$field_name-mm' class='date_month'>";
	for ($i=1; $i<13; $i++){
		$j=$i-1;
		if ($i==$date_part[1]){
			$output .= "<option value='$i' selected='selected'>$months[$j]</option>";
		} else {
			$output .= "<option value='$i'>$months[$j]</option>";
		}
	}
	$output .= "</select>";


	$output .= "<select name='$field_name' id='$field_name' class='date_year highlight-days-67 split-date no-transparency'>";

	$j = date ("Y");

	for ($i=($j-2); $i<($j+2); $i++){
		if ($i==$date_part[0]){
			$output .= "<option value='$i' selected='selected'>$i</option>";
		} else {
			$output .= "<option value='$i'>$i</option>";
		}
	}

	$output .= "</select>";

	return $output;

}

function validDate($mydate)
{
	$datePart = explode("-", $mydate);
	$today = getdate();
	$year = $today['year'];
			/* check year (only allow this year or next) */
	if (($datePart[0] < ($year-2)) | ($datePart[0] > ($year + 2)))
	{
		return("");
	}
	if (($datePart[1] < 1) | ($datePart[1] > 12)) /* check the month */
	{
		return("");
	}
	if (($datePart[2] < 1) | ($datePart[2] > 31)) /* check day */
	{
		return("");
	}
	$mydate = date ("Y-n-j", mktime(0,0,0,$datePart[1],$datePart[2],$datePart[0]));
	return($mydate);
}


?>