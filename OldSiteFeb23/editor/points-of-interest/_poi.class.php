<?php
require_once 'library/_table.class.php';

// class to produce a list of comments to a given set of filters/sorts

class POITable extends Table{

	function POITable(){
		parent::Table();
	}


	function setQuery(){
		$this->sql = "SELECT * FROM tbl_poi INNER JOIN tbl_poi_icons on tbl_poi.poi_icon = tbl_poi_icons.icon_id ".$this->sql_filter.$this->sql_sort;
	}


	function getOrder(){
		$order = $_GET['order'];
		switch ($order){

			case 'nameasc':
				$this->sql_sort = " ORDER BY poi_title ASC";
				$this->query_sort = "order=nameasc";
				$this->form_sort = "nameasc";
				break;

			case 'namedesc':
				$this->sql_sort = " ORDER BY poi_title DESC";
				$this->query_sort = "order=namedesc";
				$this->form_sort = "namedesc";
				break;

			default:
				$this->sql_sort = " ORDER BY poi_icon DESC";
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
			   <td>Lat/Lng</td>
			   <td>Content</td>
			   <td>&nbsp;</td>
			</tr>
EOD;

	}


	function renderRow($row){
		$poi_id = $row['poi_id'];
		$poi_title = htmlspecialchars($row["poi_title"]);
		$poi_latitude = $row['poi_latitude'];
		$poi_longitude = $row['poi_longitude'];
		$poi_content = $row['poi_content'];
		$icon_image = IMAGE_WEB_PATH.htmlspecialchars($row['icon_image']);

		$contact_date = date('l dS \of F Y \a\t h:ia' ,strtotime($contact_date));

		$content_row = <<<EOD
		<tr class="row">
		   <td><img src="$icon_image" alt="" /></td>
		   <td><a href="points-of-interest/edit.php?poi_id=$poi_id"><strong>$poi_title</strong></a></td>
		   <td>$poi_latitude/$poi_longitude</td>
		   <td>$poi_content</td>
		   <td><a href="points-of-interest/delete.php?poi_id=$poi_id" onclick="return confirm('Are you sure you want to delete this enquiry?');">Delete</a></td>
		</tr>
EOD;

		return $content_row;

	}

}

?>