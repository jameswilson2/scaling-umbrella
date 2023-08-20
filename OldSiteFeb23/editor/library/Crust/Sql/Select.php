<?php

class Crust_Sql_Select{
    
    private $select_values = array();
    private $from;
    private $alias;
    private $join_values = array();
    private $where_values = array();
    private $bind_values = array();
    private $order_values = array();
    private $group_values = array();
    private $having_values = array();
    private $limit;
    private $offset;
    
    function select($columns){
        
        if(is_scalar($columns)){
            $columns = array($columns);
        }
        
        $this->select_values = array_merge($this->select_values, $columns);
        
        return $this;
    }
    
    function clearSelect(){
        $this->select_values = array();
        return $this;
    }
    
    function from($from){
        $this->from = $from;
        return $this;
    }
    
    function alias($name){
        $this->alias = $name;
        return $this;
    }
    
    function join($value){
        $this->join_values[] = $value;
        return $this;
    }
    
    function clearJoin(){
        $this->join_values = array();
        return $this;
    }
    
    function where($condition, $bind_values = null){
        
        $this->where_values[] = $condition;
    
        if(is_array($bind_values) || is_scalar($bind_values)){
            $this->bind($bind_values);
        }
        
        return $this;
    }
    
    function bind(){
        
        $args = func_get_args();
        $args_count = count($args);
        
        if($args_count == 1){
            if(is_array($args[0])){
                $this->bind_values = array_merge($this->bind_values, $args[0]);
            }
            else{
                $this->bind_values[] = $args[0];
            }        
        }
        else if($args_count == 2){
            $this->bind_values[$args[0]] = $args[1];
        }
        
        return $this;
    }
    
    function group($value){
        $this->group_values[] = $value;
        return $this;
    }
    
    function clearGroup(){
        $this->group_values = array();
        return $this;
    }
    
    function having($value){
        $this->having_values[] = $value;
        return $this;
    }
    
    function order($value){
        $this->order_values[] = $value;
        return $this;
    }
    
    function reorder($value = null){
        
        $this->order_values = array();
        
        if($value !== null){
            $this->order_values[] = $value;
        }
        
        return $this;
    }
    
    function limit($value){
        $this->limit = intval($value);
        return $this;
    }
    
    function offset($value){
        $this->offset = intval($value);
        return $this;
    }
    
    function render(){
        
        $sql_values = $this->bind_values;
        $sql = "SELECT ";
        
        if(count($this->select_values)){
            $sql .= implode(",", $this->select_values);
        }
        else{
            $sql .= "*";
        }
        
        $sql .= "\nFROM ";
        
        if($this->from instanceof Crust_Sql_Select){
            
            list($sub_query_sql, $sub_query_values) = $this->from->render();
            
            $sql_values = array_merge($sql_values, $sub_query_values);
            
            $sql .= "(" . $sub_query_sql . ")";
            
            $sub_query_alias = $this->from->alias;
            if(is_string($sub_query_alias)){
                $sql .= " AS `$sub_query_alias`";
            }
        }
        else{
            $sql .= "`" . $this->from . "`";
        }
        
        if(count($this->join_values)){
            $sql .= implode("\n", $this->join_values);    
        }
        
        if(count($this->where_values)){
            $sql .= "\nWHERE " . implode(" AND ", $this->where_values);
        }
        
        if(count($this->group_values)){
            $sql .= "\nGROUP BY " . implode(",", $this->group_values);
        }
        
        if(count($this->having_values)){
            $sql .= "\nHAVING " . implode(" AND ", $this->having_values);
        }
        
        if(count($this->order_values)){
            $sql .= "\nORDER BY " . implode(",", $this->order_values);
        }
        
        if($this->limit !== null){
            $sql .= "\nLIMIT " . $this->limit;
            if($this->offset !== null){
                $sql .= " OFFSET " . $this->offset;
            }
        }
        
        return array($sql, $sql_values);
    }
     
    function execute($db){
        list($sql, $values) = $this->render();
        return $db->execute($sql, $values);
    }
    
    function createCountQuery($column_name = null){
        
        $clone = clone $this;
        $clone->reorder();
        $clone->limit(null);
        $clone->alias("unnamed");
        
        $column = "COUNT(*)";
        
        if($column_name !== null){
            $column .= " AS \"$column_name\"";
        }
        
        $query = new Crust_Sql_Select;
        $query->select(array($column));
        $query->from($clone);
        
        return $query;
    }
}
