<?php

class Pager{
	
	private $select_query;
	private $row_count;
	private $pdo;
	private $page = 1;
	private $max_page = 1;
	private $limit;
	
	function __construct($select_query, $pdo){
		
		$this->select_query = $select_query;
		$this->pdo = $pdo;
		$this->row_count = $this->getTotalRowCount();
	}
	
	function getTotalRowCount(){
		
		$count_query = Sql_Select::createCountRowsStatement($this->select_query);
		
		$select = $this->pdo->prepare($count_query->render());
		$select->execute();
		
		$result = $select->fetch(PDO::FETCH_ASSOC);
		return $result["count"];
	}
	
	function setPage($page){
	
		assert($this->limit);
		
		$this->page = $page;
		$this->max_page = ceil(max($this->row_count, 1) / max($this->limit, 1));
	}
	
	function setLimit($limit){
		$this->limit = $limit;
	}
	
	function render($url, $page_var = "page"){
		return Html_PageControl::render($this->page, $this->max_page, $url, $page_var);
	}
	
	function createQuery(){
		
		assert($this->limit);
		
		$offset = $this->limit * max(min($this->page, $this->max_page) - 1, 0);
		$select_query = clone $this->select_query;
		
		$select_query->setLimit($this->limit);
		$select_query->setOffset($offset);
		
		return $select_query;
	}
}
