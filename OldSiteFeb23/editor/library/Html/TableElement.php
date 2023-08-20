<?php

class Html_TableElement extends Html_Element{
	
	private $thead;
	private $tbody;
	
	private $column_models = array();
	
	function __construct(){
		
		parent::__construct("table");
		
		$this->setAttribute("border", 0);
		$this->setAttribute("cellpadding", 0);
		$this->setAttribute("cellspacing", 0);
		
		$thead = new Html_Element("thead");
		$tbody = new Html_Element("tbody");
		
		$this->appendChild($thead);
		$this->appendChild($tbody);
		
		$this->thead = $thead;
		$this->tbody = $tbody;
	}
	
	function setColumns($column_models){
		
		$this->column_models = array();
		
		foreach($column_models as $column){
			
			if(is_array($column)){
				$column = SettersArray::set(new Html_TableColumn, $column);
			}
			
			$this->column_models[] = $column;
		}
		
		$this->createHeaderElements();
		$this->tbody->clear();
	}
	
	function setData($data){
		
		$this->tbody->clear();
		
		foreach($data as $row){
			$this->appendData($row);
		}
	}
	
	function appendData($row){
		
		$tr = new Html_Element("tr");
		
		foreach($this->column_models as $column){
			$td = new Html_Element("td");
			$td->appendChild($column->createValueElement($row[$column->getKey()]));
			$tr->appendChild($td);
		}
		
		$this->tbody->appendChild($tr);
	}
	
	function getTHead(){
		return $this->thead;
	}
	
	function getTBody(){
		return $this->tbody;
	}
	
	private function createHeaderElements(){
		
		$this->thead->clear();
		
		$tr = new Html_Element("tr");
		
		foreach($this->column_models as $column){
			$th = new Html_Element("th");
			$th->appendChild($column->createHeaderElement());
			$tr->appendChild($th);
		}
		
		$this->thead->appendChild($tr);
	}
	
}
