<?php

class Form_DecoratorField implements Form_Field{
    
    private $decorated;
    
    function __construct($decorated){
        $this->decorated = $decorated;
    }
    
    function getName(){
        return $this->decorated->getName();
    }
    
    function getLabel(){
        return $this->decorated->getLabel();
    }
    
    function getErrorMessage(){
        return $this->decorated->getErrorMessage();
    }
    
    function getStorageKeys(){
        return $this->decorated->getStorageKeys();
    }
    
    function getStorageValues(){
        return $this->decorated->getStorageValues();
    }
    
    function loadFromSubmit($assoc_array){
        return $this->decorated->loadFromSubmit($assoc_array);
    }
    
    function loadFromStorage($assoc_array){
        return $this->decorated->loadFromStorage($assoc_array);
    }
    
    function isLargeData(){
        return $this->decorated->isLargeData();
    }
    
    function createFieldElement(){
        return $this->decorated->createFieldElement();
    }
    
    function createContainerElement(){
        return $this->decorated->createContainerElement();
    }
    
    function createStatusElement(){
        return $this->decorated->createStatusElement();
    }
    
    function createLabelElement(){
        return $this->decorated->createLabelElement();
    }
    
    function createInfoElement(){
        return $this->decorated->createInfoElement();
    }
    
    function createWidgetElement(){
        return $this->decorated->createWidgetElement();
    }
}
