<?php

class Html_TableColumn{
	
	private $key;
	private $label;
	private $filters = array();
	private $create_header_element;
	private $create_value_element;
	
	function __construct(){
		$this->create_header_element = array($this, "createHeaderElementDefault");
		$this->create_value_element = array($this, "createValueElementDefault");
	}
	
	function setKey($key){
		$this->key = $key;
	}
	
	function setLabel($label){
		$this->label = $label;
	}
	
	function setFilters($filters){
		$this->filters = $filters;
	}
	
	function setCreateHeaderElement($callback){
		$this->create_header_element = $callback;
	}
	
	function setCreateValueElement($callback){
		$this->create_value_element = $callback;
	}
	
	function createHeaderElement(){
		return call_user_func($this->create_header_element);
	}
	
	function createValueElement($value){
		return call_user_func($this->create_value_element, $value);
	}
	
	function getKey(){
		return $this->key;
	}
	
	function getLabel(){
		return $this->label;
	}
	
	function filterValue($value){
		$presentation_value = $value;
		foreach($this->filters as $filter){
			$presentation_value = call_user_func($filter, $presentation_value);
		}
		return $presentation_value;
	}
	
	private function createHeaderElementDefault(){
		return new Html_TextNode($this->getLabel());
	}
	
	private function createValueElementDefault($value){
		return new Html_TextNode($this->filterValue($value));
	}
}
