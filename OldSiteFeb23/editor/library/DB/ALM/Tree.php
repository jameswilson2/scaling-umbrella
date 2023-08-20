<?php

class DB_ALM_Tree{
    
    private $pdo;
    private $table;
    private $id_column;
    private $parent_id_column;
    private $load_query;
    
    private $node_by_id;
    private $roots;
    
    function __construct(PDO $pdo, $db_meta = array()){
        
        $this->pdo = $pdo;
        
        $db_meta = array_merge(array(
            "id_column" => "id",
            "parent_id_column" => "parent_id"
        ), $db_meta);
        
        $this->table = $db_meta["table"];
        $this->id_column = $db_meta["id_column"];
        $this->parent_id_column = $db_meta["parent_id_column"];
        
        $this->load_query = new Sql_Select($this->table);
    }
    
    function getLoadQuery(){
        return $this->load_query;
    }
    
    function load(){
        
        $node_by_id = array();
        $roots = array();
        
        $select = $this->pdo->prepare($this->load_query->render());
        
        if($select->execute()){
            
            $id_column = $this->id_column;
            $parent_id_column = $this->parent_id_column;
            
            while($row = $select->fetch(PDO::FETCH_ASSOC)){
                
                $node = new DB_ALM_Node($row);
                
                $node_by_id[$row[$id_column]] = $node; 
                
                if($row[$this->parent_id_column] === null){
                    $roots[] = $node;
                }
            }
            
            foreach($node_by_id as $node){
                $attributes = $node->attributes();
                $parent_node = $node_by_id[$attributes[$parent_id_column]];
                if($parent_node){
                    $parent_node->appendChild($node);
                }
            }
        }
        
        $this->node_by_id = $node_by_id;
        $this->roots = $roots;
    }
    
    function node($id){
        return $this->node_by_id[$id];
    }
    
    function roots(){
        return $this->roots;
    }
}
