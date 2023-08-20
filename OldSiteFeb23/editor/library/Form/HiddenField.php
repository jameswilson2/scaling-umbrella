<?php

class Form_HiddenField extends Form_ScalarField{
    
    function createLabelElement(){
        return;
    }
    
    function createStatusElement(){
        return;
    }
    
    function createWidgetElement(){
        $element = new Html_InputElement("hidden");
        $this->setWidgetAttributes($element);
        $element->setAttribute("value", $this->getValue());
        return $element;
    }
}
