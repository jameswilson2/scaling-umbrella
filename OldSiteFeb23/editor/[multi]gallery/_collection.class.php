<?php
require_once 'library/_table.class.php';

// class to produce a list of comments to a given set of filters/sorts

class CollectionTable extends Table{

	function CollectionTable(){
		parent::Table();
	}


	function setQuery(){
		$this->sql = "SELECT collection_id, collection_url, collection_name, collection_date, collection_status
					FROM tbl_collection WHERE collection_id".$this->sql_filter.$this->sql_sort;
	}


	function getOrder(){
		$order = $_GET['order'];
		switch ($order){

			case 'priorityasc':
				$this->sql_sort = " ORDER BY collection_date ASC";
				$this->query_sort = "order=priorityasc";
				$this->form_sort = "priorityasc";
				break;

			case 'prioritydesc':
				$this->sql_sort = " ORDER BY collection_date DESC";
				$this->query_sort = "order=prioritydesc";
				$this->form_sort = "prioritydesc";
				break;

			default:
				$this->sql_sort = " ORDER BY collection_date DESC";
				$this->query_sort = "order=prioritydesc";
				$this->form_sort = "prioritydesc";
				break;
		}
	}


	function getFilters(){

		$this->form_filters = "";

		$form = '';

		// filter by status
		if (isset($_GET['collection_status']) && $_GET['collection_status'] != ""){
			$this->setFilter('collection_status', $_GET['collection_status']);
		}

		$options = array('Active'=>'Active','Inactive'=>'Inactive');
		foreach($options AS $key=>$option){
			if ($key == $_GET['collection_status']){
				$options_list = "<option value=\"$key\" selected=\"selected\">$option</option>";
			} else {
				$options_list = "<option value=\"$key\">$option</option>";
			}
			$form .= $options_list;
		}
		$this->form_filters .= <<<EOD
		Status: <select name="collection_status">
		<option value=''>Any Status</option>
		$form
		</select>
EOD;

	}


	function getTableHeader(){

		$query_string = implode('&amp;',$this->query_filter);
		if ($this->form_sort == 'priorityasc'){
			$link_name = $this->self.'?'.$query_string.'&amp;order=prioritydesc';
		} else {
			$link_name = $this->self.'?'.$query_string.'&amp;order=priorityasc';
		}


		$this->header = <<<EOD
		<table width="100%" border="0" cellpadding="3" cellspacing="0" class="table">
			<tr class="rowstrong">
			   <td>Image</td>
			   <td>Name</td>
			   <td><a href="$link_name">Date</a></td>
			   <td>Status</td>
			   <td>&nbsp;</td>
			</tr>
EOD;

	}


	function renderRow($row){
		$collection_id = $row["collection_id"];
		$collection_name = htmlspecialchars($row['collection_name']);
		$collection_url = htmlspecialchars($row['collection_url']);
		$collection_date = htmlspecialchars($row['collection_date']);
		$collection_status = htmlspecialchars($row['collection_status']);

		$url = GALLERY_PATH.'thumbs/'.$collection_url;

		$content_row = <<<EOD
		<tr class="row">
		   <td><img src="$url" /></td>
		   <td>$collection_name</td>
		   <td>$collection_date</td>
		   <td>$collection_status</td>
		   <td><a href="gallery/collection_edit.php?collection_id=$collection_id">Edit</a></td>		</tr>
EOD;

		return $content_row;

	}

}

?>