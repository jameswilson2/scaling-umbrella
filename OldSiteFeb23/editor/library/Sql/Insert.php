<?php

class Sql_Insert{
	
	private $table;
	private $columns = array();
	private $values = array();
	
	function __construct($table){
		$this->table = $table;
	}
	
	function setColumns($columns){
		$this->columns = $columns;
	}
	
	function addValues($values){
		$this->values[] = $values;
	}
	
	function render(){
		
		$table = $this->table;
		
		$sql = "INSERT INTO $table";
		
		$columns = $this->columns;
		if(count($columns)){
			$sql .= " (" . implode(",", $columns) . ")";
		}
		
		$values = $this->values;
		if(count($values)){
			
			$rendered_values = array();
			
			foreach($values as $value){
				$rendered_values[] = "(" . implode(",", $value) . ")";
			}
			
			$sql .= " VALUES " . implode(",", $rendered_values);
		}
		
		return $sql;
	}
}
