<?php

class Sql_Select{
	
	private $columns = array();
	private $table = null;
	private $table_alias = null;
	private $joins = array();
	private $conditions = array();
	private $group_by = array();
	private $order_by = array();
	private $limit = null;
	private $offset = null;
	
	function __construct($table, $table_alias = null){
		$this->table = $table;
		$this->table_alias = $table_alias;
	}
	
	function addColumns($columns){
       if(!is_array($columns)){
           return;
       }
	   $this->columns = array_merge($this->columns, $columns);
	}
	
	function clearColumns(){
		$this->columns = array();
	}
	
	function addJoin($join){
		$this->joins[] = $join;
	}
	
	function clearJoins(){
		$this->joins = array();
	}
	
	function addCondition($condition, $conjunction = "AND"){
        if(count($this->conditions) == 0){
            $conjunction = null;
        }
        $this->conditions[] = $conjunction." ".$condition;
	}
	
	function clearConditions(){
		$this->conditions = array();
	}
	
	function addGroupBy($group_by){
		$this->group_by[] = $group_by;
	}
	
	function clearGroupBy(){
		$this->group_by = array();
	}
	
	function addOrderBy($order_by){
		$this->order_by[] = $order_by;
	}
	
	function clearOrderBy(){
		$this->order_by = array();
	}

	function setLimit($limit){
		$this->limit = $limit;
	}
	
	function setOffset($offset){
		$this->offset = $offset;
	}
	
	function render(){
		
		if(count($this->columns)){
            $columns = implode(",", $this->columns);
        }
        else{
            $columns = "*";
        }
        
        $table = $this->table;
        
        if($table instanceof Sql_Select){
            $table = "(" . $table->render() . ") AS " . $this->table_alias;
        }
        
        $sql = "SELECT $columns FROM $table";
        
        if(count($this->joins)){
            $sql .= " " . implode(" ", $this->joins);
        }
        
        if(count($this->conditions)){
            $sql .= " WHERE " . implode(" ", $this->conditions);
        }
        
        if(count($this->group_by)){
            $sql .= " GROUP BY " . implode(",", $this->group_by);
        }
        
        if(count($this->order_by)){
            $sql .= " ORDER BY " . implode(",", $this->order_by);
        }
        
        if($this->limit !== null){
            $limit = $this->limit;
            $sql .= " LIMIT $limit";
            if($this->offset){
                $offset = $this->offset;
                $sql .= " OFFSET $offset";
            }
        }
        
        return $sql;
	}

    public static function createCountRowsStatement($statement, $count_column_name = "count"){
        
        $statement = clone $statement;
        $statement->clearOrderBy();
        $statement->setLimit(null);
        
        if(count($statement->group_by) == 0){
            $count_statement = $statement;
            $count_statement->clearColumns();
        }
        else{
            $count_statement = new Sql_Select($statement, "sub_query");

        }
        
        $count_statement->addColumns(array("COUNT(*) AS $count_column_name"));
        
        return $count_statement;
    }
}

