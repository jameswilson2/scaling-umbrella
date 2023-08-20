<?php

class Form_TextField extends Form_ScalarField{
	
	function createWidgetElement(){
		$input = new Html_InputElement("text");
		$this->setWidgetAttributes($input);
		$input->setAttribute("value", $this->getValue());
		return $input;
	}
}
