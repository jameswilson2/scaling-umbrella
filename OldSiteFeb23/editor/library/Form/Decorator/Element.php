<?php

class Form_Decorator_Element extends Form_DecoratorField{
    
    private $create_field_element;
    private $create_container_element;
    private $create_label_element;
    private $create_status_element;
    private $create_info_element;
    private $create_widget_element;
    
    function __construct($decorated){
        parent::__construct($decorated);

    }
    
    function setCreateFieldElement($function){
        $this->create_field_element = $function;
    }
    
    function setCreateContainerElement($function){
        $this->create_container_element = $function;
    }
    
    function setCreateLabelElement($function){
        $this->create_label_function = $function;
    }
    
    function setCreateStatusElement($function){
        $this->createStatusElement = $function;
    }
    
    function setCreateInfoElement($function){
        $this->create_info_function = $function;
    }
    
    function setCreateWidgetElement($function){
        $this->create_widget_element = $function;
    }
    
    function createFieldElement(){
        
        if(!$this->create_field_element){
            return parent::createFieldElement();
        }
        
        return call_user_func($this->create_field_element);
    }
    
    function createContainerElement(){
        
        if(!$this->create_container_element){
            return parent::createContainerElement();
        }
        
        return call_user_func($this->create_container_element);
    }
    
    function createStatusElement(){
        
        if(!$this->create_status_element){
            return parent::createStatusElement();
        }
        
        return call_user_func($this->create_status_element);
    }
    
    function createLabelElement(){
        
        if(!$this->create_label_element){
            return parent::createLabelElement();
        }
        
        return call_user_func($this->create_label_element);
    }
    
    function createWidgetElement(){
        
        if(!$this->create_widget_element){
            return parent::createWidgetElement();
        }
        
        return call_user_func($this->create_widget_element);
    }
}
