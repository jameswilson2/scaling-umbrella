<?php

class Html_SelectElement extends Html_Element{
	
	function __construct(){
		parent::__construct("select");
	}
	
	function addOption($value, $text){
		$option = new Html_Element("option");
		$option->setAttribute("value", $value);
		$option->appendChild(new Html_TextNode($text));
		$this->appendChild($option);
		return $option;
	}
}
