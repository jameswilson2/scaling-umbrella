<?php

class Crust_View_State implements ArrayAccess{
    
    private $public_accessible = array();
    private $state = array();
    
    function define($name, $default_value = null, $params = array()){
        
        if($this->isDefined($name)){
            return;
        }
        
        $this->state[$name] = $default_value;
        
        if(isset($params["public"]) && $params["public"] === true){
            $this->public_accessible[] = $name;
        }
    }
    
    function definePublic($name, $default_value = null, $params = array()){
        return $this->define($name, $default_value, array_merge($params, array("public" => true)));
    }
    
    
    function get($name, $default_value = null){
        return ($this->isDefined($name) ? $this->state[$name] : $default_value);
    }
    
    function isDefined($name){
        return array_key_exists($name, $this->state);
    }
    
    function import($values){
        $this->state = array_merge($this->state, $values);
    }
    
    function importPublic($values){
        foreach($values as $key => $value){
            if(in_array($key, $this->public_accessible)){
                $this->state[$key] = $value;
            }
        }
    }
    
    function offsetExists($offset){
        return array_key_exists($offset, $this->state);
    }
    
    function offsetGet($offset){
        return ($this->offsetExists($offset) ? $this->state[$offset] : null);
    }
    
    function offsetSet($offset, $value){
        if(!is_null($offset)){
            $this->state[$offset] = $value;
        }
        else{
            $this->state[] = $value;
        }
    }
    
    function offsetUnset($offset){
        unset($this->state[$offset]);
    }
    
    function export(){
        return $this->state;
    }
}
