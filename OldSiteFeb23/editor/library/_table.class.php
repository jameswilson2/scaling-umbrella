<?php
require_once 'library/_paging.class.php';

// class to table with sort and filter selections

class Table {

	// current page
	// access private
	var $page;

	// filter form - select boxes to filter form by
	// access private
	var $form_filters;

	// hidden field for filter form - to carry through sort data
	// access private
	var $form_sort;

	// filter form full form to filter items
	// access private
	var $filter_form;

	// filter query string
	// access private
	var $query_filter;

	// sort query string
	// access private
	var $query_sort;

	// filter sql
	// access private
	var $sql_filter;

	// sort sql
	// access private
	var $sql_sort;

	// mysql query
	// access private
	var $sql;

	// mysql resource
	// access private
	var $resource;

	// rows per page
	// access private
	var $rows_per_page = 15;

	// paging links
	// access private
	var $paging_links;

	// current page
	// access private
	var $self;

	// table headers
	// access private
	var $header;


	function Table(){
		if (isset($_GET['page']) && $_GET['page']!=""){
			$this->page=$_GET['page'];
		} else {
			$this->page=1;
		}

		$this->self = $_SERVER['PHP_SELF'];

		$this->getOrder();

		$this->query_filter = array();

		$this->getFilters();
	}


	function getQuery(){

		$this->setQuery();

		$query_string = implode('&amp;',$this->query_filter);
		$query_string .= "&amp;".$this->query_sort;

		$pager = new Pager($this->sql, $this->rows_per_page, $this->self, $query_string, $this->page);
		$this->sql = $pager->getPagedQuery();

		$bookings = getQuery($this->sql, $error_message='Could not get booking data: ');

		$this->paging_links = $pager->getPagingLinks();
		$this->resource = $bookings;
	}


	function getFilterForm(){
		if ($this->form_filters!=''){
			$this->filter_form = <<<EOD
	<form action="$this->self" method="get" id="calendar">
	$this->form_filters
	<input type="hidden" name="order" value="$this->form_sort" />
	<input type="submit" value="Filter Results" />
	</form>
EOD;
		} else {
			$this->filter_form="";
		}

	}


	function setFilter($filter, $value){
		$_value = safeaddslashes($value);
		$this->sql_filter .= " AND $filter = '$value'";
		$this->query_filter[] = "$filter=$value";
	}


	function renderTable(){
		$this->getQuery();

		while($row = mysql_fetch_array($this->resource)){

			$content .= $this->renderRow($row);

		}

		$this->getFilterForm();

		$this->getTableHeader();

		$this->content = <<<EOD
		$this->filter_form
		$this->header
		$content
	</table>
		$this->paging_links
EOD;
	}


	function getTable(){
		$this->renderTable();
		return $this->content;
	}


	// default functions - overridden by child class
	function setQuery(){

	}

	function getOrder(){

	}


	function getFilters(){

	}


	function getTableHeader(){

	}


	function renderRow($row){

	}

}

?>