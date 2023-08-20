<?php

class Form_SubmitField extends Form_FieldBase{
	
	protected $container_classes = array("submit-field");

	function getStorageKeys(){
		return array();
	}
	
	function getStorageValues(){
		return array();
	}
	
	function loadFromSubmit($data){
		return true;
	}
	
	function loadFromStorage($data){
		return true;
	}
	
	function createLabelElement(){
		
	}
	
	function createWidgetElement(){
		
		$input = new Html_InputElement("submit");
		$input->setAttribute("value", $this->getLabel());
		
		$name = $this->getName();
		if($name){
			$input->setAttribute("name", $name);
		}
		
		return $input;
	}
}
