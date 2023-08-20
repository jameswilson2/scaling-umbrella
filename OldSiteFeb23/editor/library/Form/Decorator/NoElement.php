<?php

class Form_Decorator_NoElement extends Form_DecoratorField{
    
    function __construct($decorated){
        parent::__construct($decorated);
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
