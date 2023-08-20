<?php

class Form_Decorator_NoStorage extends Form_DecoratorField{

    function __construct($decorated){
        parent::__construct($decorated);
    }
    
    function getStorageKeys(){
        return array();
    }
    
    function getStorageValues(){
        return array();
    }
    
    function loadFromStorage($assoc_array){
        return true;
    }
}
