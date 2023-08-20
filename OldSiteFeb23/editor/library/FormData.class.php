<?php

class FormData{
	
	private $form_name = '';
	private $data = array();
	private $no_existing = false;
	private $errors = array();
	
	public function __construct($form_name){
		
		$this->form_name = $form_name;
		
		$existing_data = @$_SESSION['form_values'][$this->form_name];
		if($existing_data){
			$this->data = $existing_data;
		}
		else{
			$this->no_existing = true;
		}
		
		$existing_errors = @$_SESSION['form_errors'][$this->form_name];
		if($existing_errors){
			$this->errors = $existing_errors;
		}
	}
	
	public function getField($name){
		return $this->data[$name];
	}
	
	public function setField($name, $value){
		$this->data[$name] = $value;
		unset($this->errors[$name]);
	}
	
	public function getFieldError($name){
		return $this->errors[$name];
	}
	
	public function setFieldError($name, $message, $default_message = ""){
		
		if(!$message){
			$message = $default_message;
		}
		
		$this->errors[$name] = $message;
	}
	
	public function hasErrors(){
		return !empty($this->errors);
	}
	
	public function hasNoData(){
		return $this->no_existing;
	}
	
	public function clear(){
		$this->data = array();
		$this->errors = array();
	}
	
	public function save(){
		$_SESSION['form_values'][$this->form_name] = $this->data;
		$_SESSION['form_errors'][$this->form_name] = $this->errors;
	}
	
	public function destroy(){
		unset($_SESSION['form_values'][$this->form_name]);
		unset($_SESSION['form_errors'][$this->form_name]);
	}
}

