<?php
require_once 'library/_table.class.php';

// class to produce a list of comments to a given set of filters/sorts

class GalleryTable extends Table{

	function GalleryTable(){
		parent::Table();
	}


	function setQuery(){
		$this->sql = "SELECT gallery_id, gallery_url, gallery_priority, gallery_name, gallery_status, collection_name
					FROM tbl_gallery LEFT JOIN tbl_collection ON collection_id=gallery_collection_id WHERE gallery_id".$this->sql_filter.$this->sql_sort;
	}


	function getOrder(){
		$order = $_GET['order'];
		switch ($order){

			case 'priorityasc':
				$this->sql_sort = " ORDER BY gallery_priority ASC";
				$this->query_sort = "order=priorityasc";
				$this->form_sort = "priorityasc";
				break;

			case 'prioritydesc':
				$this->sql_sort = " ORDER BY gallery_priority DESC";
				$this->query_sort = "order=prioritydesc";
				$this->form_sort = "priorityesc";
				break;

			default:
				$this->sql_sort = " ORDER BY gallery_priority DESC";
				$this->query_sort = "order=priorityasc";
				$this->form_sort = "priorityasc";
				break;
		}
	}


	function getFilters(){

		$this->form_filters = "";

		$form = '';

		// filter by status
		if (isset($_GET['gallery_collection_id']) && $_GET['gallery_collection_id'] != ""){
			$this->setFilter('gallery_collection_id', $_GET['gallery_collection_id']);
		}

		$sql = "SELECT collection_id, collection_name FROM tbl_collection ORDER BY collection_name ASC";
		$result = getQuery($sql);

		while($row = mysql_fetch_array($result)){
			$collection_id = $row['collection_id'];
			$collection_name = htmlspecialchars($row['collection_name']);
			if ($collection_id == $_GET['gallery_collection_id']){
				$options_list = "<option value=\"$collection_id\" selected=\"selected\">$collection_name</option>";
			} else {
				$options_list = "<option value=\"$collection_id\">$collection_name</option>";
			}
			$form .= $options_list;
		}
		$this->form_filters .= <<<EOD
		Collection: <select name="gallery_collection_id">
		<option value=''>Any Collection</option>
		$form
		</select>
EOD;


		$form = '';

		// filter by status
		if (isset($_GET['gallery_status']) && $_GET['gallery_status'] != ""){
			$this->setFilter('gallery_status', $_GET['gallery_status']);
		}

		$options = array('Active'=>'Active','Inactive'=>'Inactive');
		foreach($options AS $key=>$option){
			if ($key == $_GET['gallery_status']){
				$options_list = "<option value=\"$key\" selected=\"selected\">$option</option>";
			} else {
				$options_list = "<option value=\"$key\">$option</option>";
			}
			$form .= $options_list;
		}
		$this->form_filters .= <<<EOD
		Status: <select name="gallery_status">
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
			   <td>Collection</td>
			   <td>Name</td>
			   <td><a href="$link_name">Position</a></td>
			   <td>Status</td>
			   <td>&nbsp;</td>
			   <td>&nbsp;</td>
			</tr>
EOD;

	}


	function renderRow($row){
		$gallery_id = $row["gallery_id"];
		$gallery_name = htmlspecialchars($row['gallery_name']);
		$gallery_url = htmlspecialchars($row['gallery_url']);
		$gallery_priority = htmlspecialchars($row['gallery_priority']);
		$gallery_status = htmlspecialchars($row['gallery_status']);
		$collection_name = htmlspecialchars($row['collection_name']);

		$url = GALLERY_PATH.'thumbs/'.$gallery_url;

		$content_row = <<<EOD
		<tr class="row">
		   <td><img src="$url" /></td>
		   <td>$collection_name</td>
		   <td>$gallery_name</td>
		   <td>$gallery_priority</td>
		   <td>$gallery_status</td>
		   <td><a href="gallery/edit.php?gallery_id=$gallery_id">Edit</a></td>
		   <td><a href="gallery/delete.php?gallery_id=$gallery_id" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a></td>
		</tr>
EOD;

		return $content_row;

	}

}

?>