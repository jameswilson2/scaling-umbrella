<?php
require_once 'library/_table.class.php';

// class to produce a list of comments to a given set of filters/sorts

class FaqTable extends Table{

	function FaqTable(){
		parent::Table();
	}


	function setQuery(){
		$this->sql = "SELECT faq_id, faq_question, faq_answer, faq_status, category_name
					FROM tbl_faq LEFT JOIN tbl_faq_category ON faq_category_id=category_id
					WHERE faq_id".$this->sql_filter.$this->sql_sort;
	}


	function getOrder(){
		$order = $_GET['order'];
		switch ($order){
			case 'qasc':
				$this->sql_sort = " ORDER BY faq_question DESC";
				$this->query_sort = "order=qasc";
				$this->form_sort = "qasc";
				break;

			case 'qdesc':
				$this->sql_sort = " ORDER BY faq_question ASC";
				$this->query_sort = "order=qdesc";
				$this->form_sort = "qdesc";
				break;

			default:
				$this->sql_sort = " ORDER BY faq_question ASC";
				$this->query_sort = "order=qasc";
				$this->form_sort = "qasc";
				break;
		}
	}


	function getFilters(){

		$this->form_filters = "";

		// filter by status
		if (isset($_GET['faq_status']) && $_GET['faq_status'] != ""){
			$this->setFilter('faq_status', $_GET['faq_status']);
		}

		$options = array('active'=>'Active','inactive'=>'Inactive');
		foreach($options AS $key=>$option){
			if ($key == $_GET['faq_status']){
				$options_list = "<option value=\"$key\" selected=\"selected\">$option</option>";
			} else {
				$options_list = "<option value=\"$key\">$option</option>";
			}
			$form .= $options_list;
		}
		$this->form_filters .= <<<EOD
		<select name="faq_status">
		<option value=''>Any Status</option>
		$form
		</select>
EOD;


		// filter by category
		if (isset($_GET['category_id']) && $_GET['category_id'] != ""){
			$this->setFilter('category_id', $_GET['category_id']);
		}

		$sql = "SELECT category_id, category_name FROM tbl_faq_category";
		$cats = getQuery($sql, 'Could not get categories: ');

		$options_list='';

		while ($cat = mysql_fetch_array($cats)){
			$_category_id = $cat['category_id'];
			$_category_name = htmlspecialchars($cat['category_name']);
			if ($_category_id==$_GET['category_id']){
				$options_list .= "<option value=\"$_category_id\" selected=\"selected\">$_category_name</option>";
			} else {
				$options_list .= "<option value=\"$_category_id\">$_category_name</option>";
			}
		}

		$this->form_filters .= <<<EOD
		<select name="category_id">
		<option value=''>All Categories</option>
		$options_list
		</select>
EOD;


	}


	function getTableHeader(){

		$query_string = implode('&amp;',$this->query_filter);
		if ($this->form_sort == 'qasc'){
			$link_q = $this->self.'?'.$query_string.'&amp;order=qdesc';
		} else {
			$link_q = $this->self.'?'.$query_string.'&amp;order=qasc';
		}

		$this->header = <<<EOD
		<table width="100%" border="0" cellpadding="3" cellspacing="0" class="table">
		<tr class="rowstrong">
			<td>Category</td>
			<td><a href="$link_q">Question</a></td>
			<td>Answer</td>
			<td>Status</td>
			<td>Edit</td>
			<td>Delete</td>
		</tr>
EOD;

	}


	function renderRow($row){
		$faq_id = $row['faq_id'];
		$category_name = htmlspecialchars($row['category_name']);
		$faq_question = htmlspecialchars($row['faq_question']);
		$faq_answer = htmlspecialchars($row['faq_answer']);
		$faq_status = $row['faq_status'];
		$faq_status_ucfirst = ucfirst($faq_status);

		$faq_answer = ereg_replace("\r\n", "\n", $faq_answer);
		$faq_answer = ereg_replace("\r", "\n", $faq_answer);
		$faq_answer = ereg_replace("\n\n", '</p><p>', $faq_answer);
		$faq_answer = ereg_replace("\n", '<br />', $faq_answer);

		$content_row = <<<EOD
		<tr class="row">
			<td>$category_name</td>
			<td>$faq_question</td>
			<td><p>$faq_answer</p></td>
			<td>$faq_status_ucfirst</td>
			<td><a href="faq/edit.php?faq_id=$faq_id">Edit</a></td>
			<td><a onclick="return confirm('Are you sure you want to delete this question?');" href="faq/delete.php?faq_id=$faq_id">Delete</a></td>
		</tr>
EOD;

		return $content_row;

	}

}

?>