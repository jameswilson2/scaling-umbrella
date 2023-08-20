<?php

class Form extends Form_Fieldset{
	
	private $action = "";
	private $method = "POST";
	private $enctype = "application/x-www-form-urlencoded";
	
	function addField($field){
		
		$field = parent::addField($field);
		
		if($field->isLargeData()){
			$this->enctype = "multipart/form-data";
		}
		
		return $field;
	}
	
	function setAction($action){
		$this->action = $action;
	}
	
	function setMethod($method){
		$this->method = $method;
	}
	
	function addSecTokenField($key){
		$security_token = new Form_SecTokenField;
		$security_token->setName("token");
		$security_token->setKey($key);
		$this->addField($security_token);
	}
	
	function addSubmitField($label, $name = null){
		$submit = new Form_SubmitField;
		$submit->setLabel($label);
		
		if($name){
			$submit->setName($name);
		}
		
		$this->addField($submit);
	}
	
	function createContainerElement(){
		
		$form = new Html_Element("form");
		$form->setAttribute("action", $this->action);
		$form->setAttribute("method", $this->method);
		$form->setAttribute("enctype", $this->enctype);
		
		return $form;
	}
	
	function createLabelElement(){
		return null;
	}
}
