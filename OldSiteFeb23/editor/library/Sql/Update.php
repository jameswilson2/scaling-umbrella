<?php

class Sql_Update{
	
	private $table = null;
	private $row = array();
	private $conditions = array();
	
	function __construct($table){
		$this->table = $table;
	}
	
	function setRow($row){
		$this->row = $row;	
	}
	
	function clearRow(){
		$this->row = array();
	}
	
	function addCondition($condition){
		if(count($this->conditions) == 0){
            $conjunction = null;
        }
        $this->conditions[] = $conjunction." ".$condition;
	}
	
	function clearConditions(){
		$this->conditions = array();
	}
	
	function render(){

		$table = $this->table;
		
		$sql = "UPDATE $table SET ";
		
		$assignments = array();
		
		foreach($this->row as $name => $value){
			$assignments[] = "$name = $value";
		}
		
		$sql .= implode(",", $assignments);
		
		if(count($this->conditions)){
            $sql .= " WHERE " . implode(" ", $this->conditions);
        }
		
		return $sql;
	}
}
