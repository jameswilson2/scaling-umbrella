<?php

class Form_ChoiceField extends Form_FieldBase{
	
	private $options;
	private $multiple = false;
	
	private $selected;
	
	function setValue($value){
		$this->selected = $value;
	}

	function setOptions($options){
		$this->options = $options;	
	}
	
	function getOptionValues(){
		$values = call_user_func($this->options);
		return $values;
	}
	
	function setMultiple($multiple){
		$this->multiple = $multiple;
	}
	
	function isMultiple(){
		return $this->multiple;
	}
	
	protected function getFormName(){
		$name = $this->getName();
		if($this->multiple){
			$name .= "[]";
		}
		return $name;
	}
	
	function loadFromSubmit($array){
		
		$field_name = $this->getName();
		
		if(!isset($array[$field_name])){
			if($this->isOptional()){
				return true;
			}
			else{
				$this->setErrorMessage("required field");
				return false;
			}
		}
		
		$values = $array[$field_name];
		
		if(!$this->multiple || !is_array($values)){
			$values = array($values);
		}
		
		$options = $this->getOptionValues();
		
		foreach($values as $value){
			
			$found = false;
			
			foreach($options as $option){
				if($value == $option[0]){
					$found = true;
					break;
				}
			}
			
			if(!$found){
				$this->setErrorMessage("unknown value");
				return false;
			}
		}
		
		if($this->multiple){
			$this->selected = $values;
		}
		else{
			$this->selected = $values[0];
		}
		
		return true;
	}
		
	function getStorageKeys(){
		return array($this->getName());
	}
	
	function getStorageValues(){
		return array($this->getName() => $this->selected);
	}
	
	function loadFromStorage($array){
		$this->selected = @$array[$this->getName()];	
	}
	
	protected function getSelectedOptions(){
		return $this->selected;
	}
}
