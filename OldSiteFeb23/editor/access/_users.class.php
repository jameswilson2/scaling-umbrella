<?php
require_once 'library/_table.class.php';

// class to produce a list of comments to a given set of filters/sorts

class UserTable extends Table{

	function UserTable(){
		parent::Table();
	}


	function setQuery(){
		$this->sql = "SELECT user_id, user_name, user_email, user_admin
					FROM tbl_user WHERE user_id".$this->sql_filter.$this->sql_sort;
	}


	function getOrder(){
		$order = $_GET['order'];
		switch ($order){

			case 'emailasc':
				$this->sql_sort = " ORDER BY user_email ASC";
				$this->query_sort = "order=emailasc";
				$this->form_sort = "emailasc";
				break;

			case 'emaildesc':
				$this->sql_sort = " ORDER BY user_email DESC";
				$this->query_sort = "order=emaildesc";
				$this->form_sort = "emaildesc";
				break;

			default:
				$this->sql_sort = " ORDER BY user_email ASC";
				$this->query_sort = "order=emailasc";
				$this->form_sort = "emailasc";
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
		if ($this->form_sort == 'emailasc'){
			$link_email = $this->self.'?'.$query_string.'&amp;order=emaildesc';
		} else {
			$link_email = $this->self.'?'.$query_string.'&amp;order=emailasc';
		}




		$this->header = <<<EOD
		<table width="100%" border="0" cellpadding="3" cellspacing="0" class="table">
			<tr class="rowstrong">
			   <td>Name</td>
			   <td><a href="$link_email">Email</a></td>
			   <td>Admin</td>
			   <td>&nbsp;</td>
			</tr>
EOD;

	}


	function renderRow($row){
		$user_id = $row["user_id"];
		$user_name = htmlspecialchars($row['user_name']);
		$user_email = htmlspecialchars($row['user_email']);
		$user_admin = $row['user_admin'];
		$contact_date = htmlspecialchars($row['contact_date']);
		$contact_message = htmlspecialchars($row['contact_message']);

		$content_row = <<<EOD
		<tr class="row">
		   <td>$user_name</td>
		   <td>$user_email</td>
		   <td>$user_admin</td>
		   <td><a href="access/edit.php?user_id=$user_id">Edit</a> | <a href="access/access.php?user_id=$user_id">Access</a></td>
		</tr>
EOD;

		return $content_row;

	}

}

?>