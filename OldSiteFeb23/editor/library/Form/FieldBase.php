<?php

/*
	The base class of all the form field classes. This class implements the 
	getter and setter methods for the field's common meta and status 
	properties, define's a validation interface and implements the create 
	element methods (all except createWidgetElement). Form_FieldBase is an 
	abstract base class; not all methods of the Form_Field interface are 
	implemented. The data load and data store methods have been left 
	unimplemented: getStorageKeys, getStorageValues, loadFromStorage and 
	loadFromSubmit. The createWidgetElement method is not implemented in this 
	class.
*/ 
class Form_FieldBase implements Form_Field{

	private $name;
	private $widget_element_id;
	private $label;
	private $error_message;
	
	private $validators = array();
	private $optional = false;
	
	private $pre_filters = array();
	private $post_filters = array();
	
	protected $container_classes = array();
	
	function __construct(){
		
	}
	
	function getName(){
		return $this->name;
	}
		
	/*
		Set the field name. The name given must be unique among the field 
		names used in the form. The field name identifies the field in the 
		storage and submit value arrays. 
		
		The naming convention is all lowercase with underscores used where 
		spaces would be.
	*/
	function setName($name){
		
		if($this->widget_element_id === null){
			$this->widget_element_id = "_" . $name;
		}
		
		return $this->name = $name;
	}
	
	function setWidgetElementId($widget_element_id){
		$this->widget_element_id = $widget_element_id;
	}
	
	function getWidgetElementId(){
		return $this->widget_element_id;
	}
	
	/*
		Prettify a field name to be used as label text
	*/
	static function labelize($name){
		return ucwords(str_replace(array("_", "-"), " ", $name));
	}
	
	/*
		Return a string of the label property. If the label property is not 
		set (i.e. the setLabel method was not called) then the method will 
		return the name property value in a prettified form as the label.
	*/
	function getLabel(){
		return ($this->label !== null ? $this->label : self::labelize($this->name));
	}
	
	function setLabel($label){
		$this->label = $label;
	}
	
	/*
		Returns true if an empty or undefined value is allowed to pass validation.
	*/
	function isOptional(){
		return $this->optional;
	}
	
	/*
		Set the field to be optional or not. The first argument is a boolean 
		value, pass true to set the field to be optional or pass false to set 
		the field to require a value.
		
		As default, the field is set to be required.
	*/
	function setOptional($optional){
		$this->optional = $optional;
	}
	
	/*
		Set an array of validation functions. The loadFromSubmit method should return
		false immediately after one of these validation functions returns false
		or returns an error array.
	*/
	function setValidators($validators){
		$this->validators = $validators;
	}
	
	/*
		Append a validation function to the current array of validation functions set
	*/
	function addValidator($validator){
		$this->validators[] = $validator;
	}
	
	/*
		Get the array of validation functions
	*/
	function getValidators(){
		return $this->validators;	
	}
	
	/*
		Set an array of pre-filter functions. A pre-filter function transforms
		the submit value before validation.
	*/
	function setPreFilters($filters){
		$this->pre_filters = $filters;
	}
	
	function getPreFilters(){
		return $this->pre_filters;
	}
	
	/*
		Set an array of post-filter functions. A post-filter function transforms
		the submit value after validation.
	*/
	function setPostFilters($filters){
		$this->post_filters = $filters;
	}
	
	function getPostFilters(){
		return $this->post_filters;
	}
	
	function getErrorMessage(){
		return $this->error_message;
	}
	
	/*
		Set the information text property, that will be used in the 
		createFieldElement method to emit an information element. The first 
		argument is a string value for passing the information text.
	*/
	function setInfoText($info_text){
		$this->info_text = $info_text;	
	}
	
	/*
		When the loaded submit value is detected as invalid the derived class 
		should call this method to set the error message property. The first
		argument is a string value for passing the error message text.
	*/
	protected function setErrorMessage($error_message){
		$this->error_message = $error_message;
	}
	
	function getStorageKeys(){
		throw new Form_Exception_NotImplemented;
	}
	
	function getStorageValues(){
		throw new Form_Exception_NotImplemented;
	}
	
	function loadFromSubmit($array){
		throw new Form_Exception_NotImplemented;
	}
	
	function loadFromStorage($array){
		throw new Form_Exception_NotImplemented;
	}
	
	function createFieldElement(){
		
		$field_element = $this->createContainerElement();
		
		$status = $this->createStatusElement();
		if($status){
			$field_element->appendChild($status);
		}
		
		$label = $this->createLabelElement();
		if($label){
			$field_element->appendChild($label);
		}
		
		$widget = $this->createWidgetElement();
		if($widget){
			if(is_array($widget)){
				foreach($widget as $widget_x){
					if($widget_x){
						$field_element->appendChild($widget_x);
					}
				}
			}
			else{
				$field_element->appendChild($widget);
			}
		}
		
		$info = $this->createInfoElement();
		if($info){
			$field_element->appendChild($info);
		}

		return $field_element;
	}
	
	function createContainerElement(){
		
		$container = new Html_Element("div");
		$container->addClass("field");
		
		if(function_exists("get_called_class")){
			$container->addClass(get_called_class());
		}
		
		foreach($this->container_classes as $class_name){
			$container->addClass($class_name);
		}
		
		if($this->name){
			$container->setAttribute("id", "_" . $this->name . "_field");
		}
		
		if($this->isOptional()){
			$container->addClass("optional");
		}
		
		return $container;
	}
	
	function createStatusElement(){
		
		if($this->error_message !== null){
			$error_container = Html_TextNode::wrap("div", $this->error_message);
			$error_container->addClass("error");
			return $error_container;
		}
	}

	function createInfoElement(){
		
		if($this->info_text){
			$info_text_span = Html_TextNode::wrap("span", $this->info_text);
			$info_text_span->addClass("info");
			return $info_text_span;
		}
	}
	
	function createLabelElement(){
		$label = Html_TextNode::wrap("label", $this->getLabel());
		$label->setAttribute("for", $this->widget_element_id);
		return $label;
	}
	
	function createWidgetElement(){
		throw new Form_Exception_NotImplemented;
	}
	
	/*
		Helper method for derived classes to set common attributes on the 
		widget element.
	*/
	protected function setWidgetAttributes($widget){
		
		$name = $this->getName();
		
		$widget->setAttribute("name", $name);
		$widget->setAttribute("id", $this->widget_element_id);
		
		if(!$this->optional){
			$widget->setAttribute("required");
		}
	}
	
	function isLargeData(){
		return false;
	}
}
