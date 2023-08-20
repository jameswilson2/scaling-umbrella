<?php

class Form_NullField extends Form_FieldBase{
    
    function getStorageKeys(){
        return array();
    }
    
    function getStorageValues(){
        return array();
    }
    
    function loadFromSubmit($array){
        return true;
    }
    
    function loadFromStorage($array){
        return true;
    }
    
    function createFieldElement(){
        return;
    }
    
    function createContainerElement(){
        return;        
    }
    
    function createStatusElement(){
        return;
    }
    
    function createLabelElement(){
        return;
    }
    
    function createWidgetElement(){
        return;
    }
}
