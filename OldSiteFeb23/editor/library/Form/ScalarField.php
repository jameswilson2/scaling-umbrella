<?php

class Form_ScalarField extends Form_FieldBase{
	
	private $value;
	
	function setValue($value){
		$this->value = $value;
	}
	
	function loadFromSubmit($array){
		
		$name = $this->getName();
		if(isset($array[$name]) && $array[$name] !== null && $array[$name] !== ""){
			
			$value = $array[$name];
			
			$value = $this->prefilter($value);
			
			$filters = $this->getPreFilters();
			foreach($filters as $filter){
				$value = call_user_func($filter, $value);	
			}
			
			$validators = $this->getValidators();
			foreach($validators as $validator){
				
				if(!is_callable($validator)){
					throw new Exception("$name field: validator is not callable");
				}
				
				$validation_status = call_user_func($validator, $value, $array);
				if(is_array($validation_status)){
					if($validation_status[0] === false){
						$this->setErrorMessage($validation_status[1]);
						return false;
					}
				}
				else{
					if($validation_status === false){
						$this->setErrorMessage("Invalid value");
						return false;
					}
				}
			}
			
			if(!$this->validate($value)){
				return false;
			}
			
			$value = $this->postfilter($value);
			
			$filters = $this->getPostFilters();
			foreach($filters as $filter){
				$value = call_user_func($filter, $value);	
			}
			
			$this->value = $value;
			return true;
		}
		else{
			$optional = $this->isOptional();
			if(!$optional){
				$this->setErrorMessage("required field");
			}
			return $optional;
		}
	}
	
	function loadFromStorage($array){	
		$name = $this->getName();
		if(isset($array[$name])){
			$this->value = $array[$name];
			return true;
		}
		else{
			return false;
		}
	}
	
	function getStorageKeys(){
		return array($this->getName());
	}
	
	function getStorageValues(){
		return array($this->getName() => $this->value);
	}
	
	protected function getValue(){
		return $this->value;
	}
	
	protected function prefilter($value){
		return $value;
	}
	
	protected function postfilter($value){
		return $value;
	}
	
	protected function validate($value){
		return true;
	}
}
