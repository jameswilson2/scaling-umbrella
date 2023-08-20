<?php

class DB_ALM_Node{
    
    private $parent;
    private $children;
    private $attributes;
    
    function __construct($attributes){
        
        $this->tree = $tree;
        $this->parent = $parent;
        $this->attributes = $attributes;
        $this->children = array();
    }
    
    function attributes(){
        return $this->attributes;
    }
    
    function parent(){
        return $this->parent;
    }
    
    function hasChildren(){
        return count($this->children) > 0;
    }
    
    function children(){
        return $this->children;
    }
    
    function appendChild($child){
        $this->children[] = $child;
        $child->parent = $this;
    }
    
    function isDescendantOf($target_parent){
        
        if($this === $target_parent){
            return true;
        }
        
        $parent = $this->parent;
        
        while($parent && $parent !== $target_parent){
            $parent = $parent->parent; 
        }
        
        return $parent !== null;
    }
}
