<?php
require_once 'library/_table.class.php';

// class to produce a list of comments to a given set of filters/sorts

class GalleryTable extends Table{

	function GalleryTable(){
		parent::Table();
	}


	function setQuery(){
		$this->sql = "SELECT gallery_id, gallery_url, gallery_priority
					FROM tbl_gallery WHERE gallery_id".$this->sql_filter.$this->sql_sort;
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
			   <td><a href="$link_name">Position</a></td>
			   <td>&nbsp;</td>
			   <td>&nbsp;</td>
			</tr>
EOD;

	}


	function renderRow($row){
		$gallery_id = $row["gallery_id"];
		$gallery_url = htmlspecialchars($row['gallery_url']);
		$gallery_priority = htmlspecialchars($row['gallery_priority']);

		$url = GALLERY_PATH.'thumbs/'.$gallery_url;

		$content_row = <<<EOD
		<tr class="row">
		   <td><img src="$url" /></td>
		   <td>$gallery_priority</td>
		   <td><a href="gallery/edit.php?gallery_id=$gallery_id">Edit</a></td>
		   <td><a href="gallery/delete.php?gallery_id=$gallery_id" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a></td>
		</tr>
EOD;

		return $content_row;

	}

}

?>