<?php
require_once 'library/_table.class.php';

// class to produce a list of comments to a given set of filters/sorts

class NewsTable extends Table{

	function NewsTable(){
		parent::Table();
	}


	function setQuery(){
		$this->sql = "SELECT news_id, news_title, news_summary, news_date, news_status
					FROM tbl_news WHERE news_id".$this->sql_filter.$this->sql_sort;
	}


	function getOrder(){
		$order = $_GET['order'];
		switch ($order){

			case 'datedesc':
				$this->sql_sort = " ORDER BY news_date DESC";
				$this->query_sort = "order=datedesc";
				$this->form_sort = "datedesc";
				break;

			case 'dateasc':
				$this->sql_sort = " ORDER BY news_date ASC";
				$this->query_sort = "order=dateasc";
				$this->form_sort = "dateasc";
				break;

			case 'titleasc':
				$this->sql_sort = " ORDER BY news_title ASC";
				$this->query_sort = "order=titleasc";
				$this->form_sort = "titleasc";
				break;

			case 'titledesc':
				$this->sql_sort = " ORDER BY news_title DESC";
				$this->query_sort = "order=titledesc";
				$this->form_sort = "titledesc";
				break;

			default:
				$this->sql_sort = " ORDER BY news_date DESC";
				$this->query_sort = "order=datedesc";
				$this->form_sort = "datedesc";
				break;
			}
	}


	function getFilters(){

		$this->form_filters = "";

	}


	function getTableHeader(){

		$query_string = implode('&amp;',$this->query_filter);
		if ($this->form_sort == 'titleasc'){
			$link_title = $this->self.'?'.$query_string.'&amp;order=titleasc';
		} else {
			$link_title = $this->self.'?'.$query_string.'&amp;order=titleasc';
		}

		if ($this->form_sort == 'datedesc'){
			$link_date = $this->self.'?'.$query_string.'&amp;order=dateasc';
		} else {
			$link_date = $this->self.'?'.$query_string.'&amp;order=datedesc';
		}



		$this->header = <<<EOD
		<table width="100%" border="0" cellpadding="3" cellspacing="0" class="table">
			<tr class="rowstrong">
				<td><a href="$link_title">Title</a></td>
				<td><a href="$link_date">Posted</a></td>
				<td>Summary</td>
				<td>Status</td>
				<td>&nbsp;</td>
			</tr>
EOD;

	}


	function renderRow($row){

		$news_id = $row['news_id'];
		$news_title = htmlspecialchars($row['news_title']);
		$news_summary = htmlspecialchars($row['news_summary']);
		$news_status = htmlspecialchars($row['news_status']);
		$news_date = $row['news_date'];

		$content_row = <<<EOD
		<tr class="row">
			<td>$news_title</td>
			<td>$news_date</td>
			<td>$news_summary</td>
			<td>$news_status</td>
			<td><a href="news/edit.php?nid=$news_id">Edit</a></td>
		</tr>
EOD;

		return $content_row;

	}

}

?>