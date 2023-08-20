<?php

class Form_NumberField extends Form_TextField{
    
    private $min;
    private $max;
    private $step = 1;
    
    function setMin($min){
        $this->min = $min;
    }
    
    function setMax($max){
        $this->max = $max;
    }
    
    function setStep($step){
        $this->step = $step;
    }
    
    protected function validate($value){
        
        if(!is_numeric($value)){
            $this->setErrorMessage("require a number");
            return false;
        }
        
        $value = floatval($value);
        
        if($this->min !== null && $value < $this->min){
            $this->setErrorMessage("require a value above $this->min");
            return false;
        }
        
        if($this->max !== null && $value > $this->max){
            $this->setErrorMessage("require a value below $this->max");
            return false;
        }
        
        $quotient = round($value/$this->step, 8);
        
        if($quotient - floor($quotient) != 0){
            $this->setErrorMessage("require a value that is a multiple of $this->step");
            return false;
        }
        
        return true;
    }
    
    function createWidgetElement(){
        
        $input = new Html_InputElement("number");
        $this->setWidgetAttributes($input);
        $input->setAttribute("value", $this->getValue());
        
        if($this->min !== null){
            $input->setAttribute("min", $this->min);
        }
        
        if($this->max !== null){
            $this->setAttribute("max", $this->max);
        }
        
        if($this->step != 1){
            $input->setAttribute("step", $this->step);
        }
        
        return $input;
    }
    
}
