<?php

class Form_BooleanField extends Form_ScalarField{
    
    private $true_value = 1;
    private $false_value = 0;
    
    function __construct(){
        $this->setOptional(true);
    }
    
    function setTrueValue($value){
        $this->true_value = $value;
    }
    
    function setFalseValue($value){
        $this->false_value = $value;
    }
    
    function getTrueValue(){
        return $this->true_value;
    }
    
    function loadFromSubmit($data){
        
        $name = $this->getName();
        $copy = $data;
        
        if(!isset($data[$name]) || empty($data[$name])){
            $copy[$name] = $this->false_value;
        }
        else{
            $copy[$name] = $this->true_value;    
        }
        
        return parent::loadFromSubmit($copy);
    }
}
