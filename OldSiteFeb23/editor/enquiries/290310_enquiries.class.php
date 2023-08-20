<?php
require_once 'library/_table.class.php';

// class to produce a list of comments to a given set of filters/sorts

class EnquiryTable extends Table{

	function EnquiryTable(){
		parent::Table();
	}


	function setQuery(){
		$this->sql = "SELECT contact_id, contact_name, contact_email, contact_phone, contact_message, contact_status, contact_date
					FROM tbl_contact WHERE contact_id".$this->sql_filter.$this->sql_sort;
	}


	function getOrder(){
		$order = $_GET['order'];
		switch ($order){

			case 'nameasc':
				$this->sql_sort = " ORDER BY contact_name ASC";
				$this->query_sort = "order=nameasc";
				$this->form_sort = "nameasc";
				break;

			case 'namedesc':
				$this->sql_sort = " ORDER BY contact_name DESC";
				$this->query_sort = "order=namedesc";
				$this->form_sort = "namedesc";
				break;

			case 'dateasc':
				$this->sql_sort = " ORDER BY contact_date ASC";
				$this->query_sort = "order=dateasc";
				$this->form_sort = "dateasc";
				break;

			case 'datedesc':
				$this->sql_sort = " ORDER BY contact_date DESC";
				$this->query_sort = "order=datedesc";
				$this->form_sort = "datedesc";
				break;

			default:
				$this->sql_sort = " ORDER BY contact_date DESC";
				$this->query_sort = "order=datedesc";
				$this->form_sort = "datedesc";
				break;
		}
	}


	function getFilters(){

		$this->form_filters = "";

		/*
		// filter by status
		if (isset($_GET['contact_status']) && $_GET['contact_status'] != ""){
			$this->setFilter('contact_status', $_GET['contact_status']);
		}

		$options = array('New','Responded');
		foreach($options AS $option){
			if ($option == $_GET['contact_status']){
				$options_list = "<option value=\"$option\" selected=\"selected\">$option</option>";
			} else {
				$options_list = "<option value=\"$option\">$option</option>";
			}
			$form .= $options_list;
		}
		$this->form_filters .= <<<EOD
		<select name="contact_status">
		<option value=''>Any Status</option>
		$form
		</select>
EOD;

		*/
	}


	function getTableHeader(){

		$query_string = implode('&amp;',$this->query_filter);
		if ($this->form_sort == 'nameasc'){
			$link_name = $this->self.'?'.$query_string.'&amp;order=namedesc';
		} else {
			$link_name = $this->self.'?'.$query_string.'&amp;order=nameasc';
		}

		if ($this->form_sort == 'datedesc'){
			$link_date = $this->self.'?'.$query_string.'&amp;order=dateasc';
		} else {
			$link_date = $this->self.'?'.$query_string.'&amp;order=datedesc';
		}



		$this->header = <<<EOD
		<table width="100%" border="0" cellpadding="3" cellspacing="0" class="table">
			<tr class="rowstrong">
			   <td><a href="$link_name">Name</a> / Email / Phone</td>
			   <td><a href="$link_date">Date</a></td>
			   <td>Message</td>
			</tr>
EOD;

	}


	function renderRow($row){
		$contact_id = $row["contact_id"];
		$contact_name = htmlspecialchars($row['contact_name']);
		$contact_email = htmlspecialchars($row['contact_email']);
		$contact_phone = htmlspecialchars($row['contact_phone']);
		$contact_date = htmlspecialchars($row['contact_date']);
		$contact_message = htmlspecialchars($row['contact_message']);

		$contact_message = ereg_replace("\r\n", "\n", $contact_message);
		$contact_message = ereg_replace("\r", "\n", $contact_message);
		$contact_message = ereg_replace("\n\n", '</p><p>', $contact_message);
		$contact_message = ereg_replace("\n", '<br />', $contact_message);

		$contact_date = date('l dS \of F Y \a\t h:ia' ,strtotime($contact_date));

		$content_row = <<<EOD
		<tr class="row">
		   <td width="30%"><strong>$contact_name</strong><br />$contact_email<br />$contact_phone</td>
		   <td width="13%">$contact_date</td>
		   <td><p>$contact_message</p></td>
		</tr>
EOD;

		return $content_row;

	}

}

?>