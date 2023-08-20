<?php

class Crust_View_Collection{

    private $view_state;
    private $select;
    private $db;
    private $filters = array();
    
    function __construct(View_State $view_state = null){
        
        if($view_state === null){
            $view_state = new Crust_View_State;
            $view_state->definePublic("page", 1);
            $view_state->definePublic("order", "asc");
            $view_state->definePublic("order_by", null);
        }
        
        $this->view_state = $view_state;
    }
    
    function db(){
        
        $args = func_get_args();
        $args_count = count($args);
        
        if($args_count > 0){
            
            if(!$args[0] instanceof Crust_DB_Adapter){
                $args[0] = Crust_DB_Adapter_Factory::convert($args[0]);
            }
            
            $this->db = $args[0];
        }
        
        return $this->db;
    }
    
    function query(){
        
        $args = func_get_args();
        $args_count = count($args);
        
        if($args_count == 0){
            return $this->select;
        }
        
        $select = $args[0];
        
        if(is_string($select)){
            $table_name = $select;
            $select = new Crust_Sql_Select;
            $select->from($table_name);
        }
        
        if($args_count > 1){
            $this->db($args[1]);
        }
        
        $this->select = $select;
        
        return $this->select;
    }
    
    function filter($function_name, $var_name, $column_name = null){
        
        if($column_name === null){
            $column_name = $var_name;
        }
        
        $static_method_name = "Crust_View_Filter::$function_name";
        
        if(is_callable($static_method_name)){
            $function_name = $static_method_name;
        }
        
        $this->filters[] = array($function_name, $var_name, $column_name);
        
        $this->view_state->definePublic($var_name);
    }
    
    function importPublicVars($values){
        return $this->view_state->importPublic($values);
    }
    
    function getViewState(){
        return $this->view_state;
    }
    
    function setPageSize($value){    
        $this->view_state["max_page_size"] = $value;
    }
    
    function setOrderable($orderable){
        $this->view_state["orderable"] = $orderable;
    }
    
    function setPreferredOrder($preferred_order){
        
        if(!is_array($preferred_order)){
            $preferred_order = array($preferred_order);
        }
        
        $this->view_state["preferred_order"] = $preferred_order;
    }
    
    function export(){
        
        $view_state = $this->view_state;
        $select = clone $this->select;
        $db = $this->db;
        
        foreach($this->filters as $bind_args){
            call_user_func($bind_args[0], $bind_args[1], $view_state, $bind_args[2], $select);
        }
        
        Crust_View_Order::apply($view_state, $select);
        Crust_View_Paginate::apply($view_state, $select, $db);
        
        if($select->execute($db)){
            $collection = $db->fetchAll();
            $view_state["collection"] = $collection;
        }
        else{
            return array();
        }
        
        return $view_state->export();
    }
}
