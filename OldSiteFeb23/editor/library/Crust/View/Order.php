<?php

class Crust_View_Order{
    
    static function apply(Crust_View_State $view_state, Crust_Sql_Select $select){
        
        if($view_state->isDefined("order_by") && $view_state->isDefined("orderable")){
            
            $orderable = $view_state->get("orderable");
            
            if(is_array($orderable)){
                
                $order = $view_state->get("order", "asc");
                $order = (strcasecmp($order, "desc") == 0 ? "desc" : "asc");
                
                $order_by = $view_state["order_by"];
                
                if(in_array(strtolower($order_by), $orderable)){
                    
                    $select->reorder("$order_by " . strtoupper($order));
                    
                    $view_state->import(array(
                        "order" => $order,
                        "order_reverse" => ($order == "asc" ? "desc" : "asc"),
                        "order_by" => $order_by
                    ));
                }
                else{
                    self::noViewState($view_state);
                }
            }
            else{
                self::noViewState($view_state);
            }
        }
        else{
            self::noViewState($view_state);
        }
        
        if($view_state->isDefined("preferred_order")){
            $preferred_order = $view_state["preferred_order"];
            foreach($preferred_order as $preferred_order_element){
                $select->order($preferred_order_element);
            }
        }
    }
    
    static function noViewState(Crust_View_State $view_state){
        unset($view_state["order"]);
        unset($view_state["order_by"]);
    }
    
    static function remove(Crust_Sql_Select $select){
        $sql->reorder();
    }
}
