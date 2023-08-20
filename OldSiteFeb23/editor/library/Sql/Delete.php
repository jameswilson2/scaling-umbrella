<?php

class Sql_Delete{
	
	private $table;
	private $conditions = array();
	
	function __construct($table){
		$this->table = $table;
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
		
		$sql = "DELETE FROM $table";
		
		if(count($this->conditions)){
            $sql .= " WHERE " . implode(" ", $this->conditions);
        }
        
        return $sql;
	}
}
