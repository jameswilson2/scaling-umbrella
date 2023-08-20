<?php

class Crust_View_Paginate{
    
    static function apply(Crust_View_State $view_state, Crust_Sql_Select $select, $db = null){
        
        $view_state->define("max_page_size", 20);
        $view_state->define("page", 1);
        
        if($view_state["max_page_size"] === null){
            $view_state->import(array(
                "max_page" => 1,
                "page" => 1
            ));
            return;
        }
        
        if(isset($view_state["collection_size"])){
            $collection_size = $view_state["collection_size"];
        }
        else{
            if($db !== null){
                $count_query = $select->createCountQuery("count");
                $count_query->execute($db);
                $count_query_result = $db->fetch();
                if($count_query_result){
                    $collection_size = $count_query_result["count"];
                }
            }
        }
        
        if(!isset($collection_size)){
            $collection_size = $view_state["page"] * $view_state["max_page_size"];
        }
        
        $max_page_size = $view_state["max_page_size"];
        $max_page = max(1, ceil($collection_size / $max_page_size));
        
        $page = min(max(1, $view_state["page"]), $max_page);
        $offset = ($page - 1) * $max_page_size;
        
        $page_size = ($page < $max_page ? $max_page_size : $collection_size % $max_page_size);
        
        $select->limit($max_page_size);
        $select->offset($offset);
        
        $view_state->import(array(
            "max_page_size" => $max_page_size,
            "max_page" => $max_page,
            "page" => $page,
            "page_size" => $page_size,
            "offset" => $offset,
            "collection_size" => $collection_size
        ));
    }
    
    static function remove(Crust_Sql_Select $select){
        $select->limit(null);
        $select->offset(null);
    }
}
