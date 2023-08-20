<?php

class Crust_View_Filter{
    
    static function comparison($operator, $var_name, $view_state, $column, Crust_Sql_Select $select){
        
        if(!$view_state->isDefined($var_name) || strlen($view_state[$var_name]) === 0){
            return;
        }
        
        $bind_key = ":$var_name";
        
        $select->where("$column $operator $bind_key", array($bind_key => $view_state[$var_name]));
    }
    
    static function equal($var_name, $view_state, $column, Crust_Sql_Select $select){
        return self::comparison("=", $var_name, $view_state, $column, $select);
    }
    
    static function notEqual($var_name, $view_state, $column, Crust_Sql_Select $select){
        return self::comparison("<>", $var_name, $view_state, $column, $select);
    }
    
    static function greaterThan($var_name, $view_state, $column, Crust_Sql_Select $select){
        return self::comparison(">", $var_name, $view_state, $column, $select);
    }
    
    static function greaterThanOrEqual($var_name, $view_state, $column, Crust_Sql_Select $select){
        return self::comparison(">=", $var_name, $view_state, $column, $select);
    }
    
    static function lessThan($var_name, $view_state, $column, Crust_Sql_Select $select){
        return self::comparison(">", $var_name, $view_state, $column, $select);
    }
    
    static function lessThanOrEqual($var_name, $view_state, $column, Crust_Sql_Select $select){
        return self::comparison(">=", $var_name, $view_state, $column, $select);
    }
    
    static function contains($var_name, $view_state, $column, Crust_Sql_Select $select){
        
        if(!$view_state->isDefined($var_name) || strlen($view_state[$var_name]) === 0){
            return;
        }
        
        $bind_key = ":$var_name";
        
        $value = $view_state[$var_name];
        
        $value = str_replace("%", "\\%", $value);
        $value = str_replace("_", "\\_", $value);
        
        $value = "%$value%";
                
        $select->where("$column LIKE $bind_key", array($bind_key => $value));
    }
    
    static function interval($var_name, $view_state, $column, Crust_Sql_Select $select){
        
        if(!$view_state->isDefined($var_name) || !is_array($view_state[$var_name])){
            return;
        }
        
        $boundary_values = $view_state[$var_name];
        
        $has_min_val = isset($boundary_values[0]) && strlen($boundary_values[0]) > 0 /*&& is_numeric($boundary_values[0])*/;
        $has_max_val = isset($boundary_values[1]) && strlen($boundary_values[1]) > 0 /*&& is_numeric($boundary_values[1])*/;
        
        $match_interval = $has_min_val && $has_max_val;
        
        $min_value_key = ":{$var_name}_min";
        $max_value_key = ":{$var_name}_max";
        
        if($match_interval){
            
            if($boundary_values[0] > $boundary_values[1]){
                $tmp = $boundary_values[0];
                $boundary_values[0] = $boundary_values[1];
                $boundary_values[1] = $tmp;
            }
            
            $select->where("$column BETWEEN $min_value_key AND $max_value_key", array(
                $min_value_key => $boundary_values[0],
                $max_value_key => $boundary_values[1]
            ));            
        }
        else{
            if($has_min_val){
                $select->where("$column >= $min_value_key", array($min_value_key => $boundary_values[0]));
            }
            else if($has_max_val){
                $select->where("$column <= $max_value_key", array($max_value_key => $boundary_values[1]));
            }
        }
    }
}
