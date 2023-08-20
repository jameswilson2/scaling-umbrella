<?php
require_once 'library/_table.class.php';

// class to produce a list of comments to a given set of filters/sorts

class CoverageTable extends Table{

	function CoverageTable(){
		parent::Table();
	}


	function setQuery(){
		$this->sql = "SELECT * FROM tbl_maparea ".$this->sql_filter.$this->sql_sort;
	}


	function getOrder(){
		$order = $_GET['order'];
		switch ($order){

			case 'namedesc':
				$this->sql_sort = " ORDER BY area_name DESC";
				$this->query_sort = "order=namedesc";
				$this->form_sort = "namedesc";
				break;

			default:
				$this->sql_sort = " ORDER BY area_name DESC";
				$this->query_sort = "order=iddesc";
				$this->form_sort = "datedesc";
				break;
		}
	}

	function getTableHeader(){

		$query_string = implode('&amp;',$this->query_filter);
		if ($this->form_sort == 'nameasc'){
			$link_name = $this->self.'?'.$query_string.'&amp;order=namedesc';
		} else {
			$link_name = $this->self.'?'.$query_string.'&amp;order=nameasc';
		}

		$this->header = <<<EOD
		<table width="100%" border="0" cellpadding="3" cellspacing="0" class="table">
			<tr class="rowstrong">
			   <td>&nbsp;</td>
			   <td><a href="$link_name">Name</a></td>
			   <td>&nbsp;</td>
			</tr>
EOD;

	}


	function renderRow($row){
		$area_id = $row['area_id'];
		$area_title = htmlspecialchars($row["area_name"]);

		$content_row = <<<EOD
		<tr class="row">
		   <td><img src="$icon_image" alt="" /></td>
		   <td><a href="coverage/edit.php?area_id=$area_id"><strong>$area_title</strong></a></td>
		   <td><a href="coverage/delete.php?area_id=$area_id" onclick="return confirm('Are you sure you want to delete this enquiry?');">Delete</a></td>
		</tr>
EOD;

		return $content_row;

	}

}

?>