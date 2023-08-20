<?php

class Form_Fieldset extends Form_FieldBase{
	
	private $fields = array();
	
	function __clone(){
		$fields = array();
		foreach($this->fields as $field){
			$fields[] = clone $field;
		}
		$this->fields = $fields;
	}
	
	function addField($field){
		
		if(is_array($field)){
			
			$decorators = array();
			
			if(isset($field["decorator"])){
				$decorators[] = $field["decorator"];
				unset($field["decorator"]);
			}
			
			if(isset($field["decorators"])){
				foreach($field["decorators"] as $decorator){
					$decorators[] = $decorator;
				}
				unset($field["decorators"]);
			}
			
			$setters = $field;
			$type = ucfirst($setters["type"]);
			
			$class_name = "Form_{$type}Field";
			
			if(!class_exists($class_name)){
				$class_name = $type;
			}
			
			$field = new $class_name;
			SettersArray::set($field, $setters);
			
			if(count($decorators)){
				foreach($decorators as $decorator){
					
					$decorator_class_name = "Form_Decorator_$decorator";
					
					if(!class_exists($decorator_class_name)){
						$decorator_class_name = $decorator;
					}
					
					$field = new $decorator_class_name($field);
				}
			}
		}
		
		$this->fields[] = $field;
		
		return $field;
	}
	
	function removeField($target_field){
		
		foreach($this->fields as $index => $field){
			if($target_field === $field){
				array_splice($this->fields, $index, 1);
				return true;
			}
		}
		
		return false;
	}
	
	function getFields(){
		return $this->fields;
	}
	
	function loadFromSubmit($data){
		
		$load_status = true;
		
		foreach($this->fields as $field){
			$load_status = min($load_status, $field->loadFromSubmit($data));
		}
		
		return $load_status;
	}
	
	function loadFromStorage($data){
		
		$load_status = true;
		
		foreach($this->fields as $field){
			$load_status = min($load_status, $field->loadFromStorage($data));
		}
		
		return $load_status;
	}
	
	function getStorageKeys(){
		
		$keys = array();
		
		foreach($this->fields as $field){
			$keys = array_merge($keys, $field->getStorageKeys());
		}
		
		return $keys;
	}
	
	function getStorageValues(){
		
		$values = array();
		
		foreach($this->fields as $field){
			$values = array_merge($values, $field->getStorageValues());
		}
		
		return $values;
	}
	
	function createContainerElement(){
		
		if(count($this->fields) == 0){
			return;
		}
		
		return new Html_Element("fieldset");
	}
	
	function createLabelElement(){
		
		if(count($this->fields) == 0){
			return;
		}
		
		return Html_TextNode::wrap("legend", $this->getLabel());
	}
	
	function createWidgetElement(){
		
		if(count($this->fields) == 0){
			return;
		}
		
		$elements = array();
		
		foreach($this->fields as $field){
			$elements[] = $field->createFieldElement();	
		}
		
		return $elements;
	}
}
