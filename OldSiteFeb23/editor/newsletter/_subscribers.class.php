<?php
require_once 'library/_table.class.php';

// class to produce a list of comments to a given set of filters/sorts

class SubscriberTable extends Table{

	function SubscriberTable(){
		parent::Table();
	}


	function setQuery(){
		$this->sql = "SELECT member_id, member_email, member_status
						FROM tbl_member
						WHERE member_id".$this->sql_filter.$this->sql_sort;
	}


	function getOrder(){
		if (isset($_GET['order']) && $_GET['order']!=''){
			$order = $_GET['order'];
			switch ($order){
				case 'idasc':
					$this->sql_sort = " ORDER BY member_id ASC";
					$this->query_sort = "order=idasc";
					$this->form_sort = "idasc";
					break;

				case 'iddesc':
					$this->sql_sort = " ORDER BY member_id DESC";
					$this->query_sort = "order=iddesc";
					$this->form_sort = "iddesc";
					break;

				default:
					$this->sql_sort = " ORDER BY member_id ASC";
					$this->query_sort = "order=idasc";
					$this->form_sort = "idasc";
					break;
			}
		}
	}


	function getFilters(){

		$this->form_filters = "";

	}


	function getTableHeader(){

		$query_string = implode('&amp;',$this->query_filter);
		if ($this->form_sort == 'idasc'){
			$link_id = $this->self.'?'.$query_string.'&amp;order=iddesc';
		} else {
			$link_id = $this->self.'?'.$query_string.'&amp;order=idasc';
		}

		$this->header = <<<EOD
		<table width="100%" border="0" cellpadding="3" cellspacing="0" class="table">
			<tr class="rowstrong">
				<td><a href="$link_id">ID</a></td>
				<td>Email</td>
				<td>Status</td>
				<td>&nbsp;</td>
			</tr>
EOD;

	}


	function renderRow($row){
		$member_id = $row['member_id'];
		$member_email = htmlspecialchars($row['member_email']);
		$member_status = htmlspecialchars($row['member_status']);

		$content_row = <<<EOD
		<tr class="row">
			<td>$member_id</td>
			<td>$member_email</td>
			<td>$member_status</td>
			<td><a href="newsletter/subscribers_delete.php?member_id=$member_id">Delete</a></td>
		</tr>
EOD;

		return $content_row;

	}

}

?>