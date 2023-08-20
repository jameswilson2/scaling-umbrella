<?php

class Form_FileField extends Form_ScalarField{
    
    function createWidgetElement(){
        
        $element = new Html_InputElement("file");
        $this->setWidgetAttributes($element);
        
        return $element;
    }
    
    function isLargeData(){
        return true;
    }
}
