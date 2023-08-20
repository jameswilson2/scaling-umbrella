<?php

class datePicker {

	var $field_name;
	var $start_year;
	var $end_year;
	var $cur_date;


	function datePicker(){
		$start_year = date("Y");
		$end_year = date("Y");
		$cur_date = date('Y-m-d');
	}


	function setStartYear($years){
		$this->start_year = date("Y")-$years;
	}


	function setEndYear($years){
		$this->end_year = date("Y")+$years;
	}


	function setCurrentDate($date){
		$this->cur_date = $this->validDate($date);
	}


	function setFieldName($field_name){
		$this->field_name = $field_name;
	}


	function buildPicker(){

		$date_part = explode("-", $this->cur_date);

		$picker =  "<select name='$this->field_name-dd' id='$this->field_name-dd' class='date_day'>";
		for ($i = 1; $i < 32; $i++){
			if ($i==$date_part[2]){
				$picker .= "<option value='$i' selected='selected'>$i</option>";
			} else {
				$picker .= "<option value='$i'>$i</option>";
			}
		}
		$picker .= "</select>";


		$months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

		$picker .= "<select name='$this->field_name-mm' id='$this->field_name-mm' class='date_month'>";
		for ($i=1; $i<13; $i++){
			$j=$i-1;
			if ($i==$date_part[1]){
				$picker .= "<option value='$i' selected='selected'>$months[$j]</option>";
			} else {
				$picker .= "<option value='$i'>$months[$j]</option>";
			}
		}
		$picker .= "</select>";


		$picker .= "<select name='$this->field_name' id='$this->field_name' class='date_year highlight-days-67 split-date no-transparency'>";

		for ($i=$this->start_year; $i<=$this->end_year; $i++){
			if ($i==$date_part[0]){
				$picker .= "<option value='$i' selected='selected'>$i</option>";
			} else {
				$picker .= "<option value='$i'>$i</option>";
			}
		}

		$picker .= "</select>";

		$this->picker = $picker;

	}

	function validDate($date){

		$datePart = explode("-", $date);

		$mydate = date ("Y-m-d", mktime(0,0,0,$datePart[1],$datePart[2],$datePart[0]));

		return($mydate);
	}


	function getDateSelector(){
		$this->buildPicker();
		return $this->picker;
	}

}



class dateValidator {

	function dateValidator(){

	}


	function validDate($date){

		$datePart = explode("-", $date);

		$mydate = date ("Y-m-d", mktime(0,0,0,$datePart[1],$datePart[2],$datePart[0]));

		return($mydate);
	}


	function getPOSTDate($field_name){



		$date_day = ($_POST[$field_name.'-dd']);
		$date_month = ($_POST[$field_name.'-mm']);
		$date_year = ($_POST[$field_name]);
		$date = $date_year."-".$date_month."-".$date_day;

		$date = $this->validDate($date);

		return $date;
	}


	function getGETDate($field_name){
		$date_day = ($_GET[$field_name.'-dd']);
		$date_month = ($_GET[$field_name.'-mm']);
		$date_year = ($_GET[$field_name]);
		$date = $date_year."-".$date_month."-".$date_day;

		$date = $this->validDate($date);

		return $date;
	}

}


?>