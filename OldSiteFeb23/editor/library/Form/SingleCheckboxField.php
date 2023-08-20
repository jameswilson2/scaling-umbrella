<?php

class Form_SingleCheckboxField extends Form_BooleanField{
    
    protected $container_classes = array("form-singlecheckbox");
    
    function createWidgetElement(){
        
        $element = new Html_InputElement("checkbox");
        $this->setWidgetAttributes($element);
        
        $true_value = $this->getTrueValue();
        
        $element->setAttribute("value", $true_value);
        
        if($this->getValue() == $true_value){
            $element->setAttribute("checked");
        }
        
        return $element;
    }
}
