<?php

class Form_EmailField extends Form_TextField{

    function loadFromSubmit($data){
        
        $value = @$data[$this->getName()];
        
        if(!Form_Validator_Email::isValid($value)){
            $this->setErrorMessage("invalid email address");
            return false;
        }
        
        return parent::loadFromSubmit($data);
    }
    
    function createWidgetElement(){
        $input = parent::createWidgetElement();
        $input->setAttribute("type", "email");
        return $input;   
    }
}
