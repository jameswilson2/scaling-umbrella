<?php

class Form_RadioField extends Form_ChoiceField{
	
	protected $container_classes = array("radio-field");
	
	function createLabelElement(){
		return;
	}
	
	function createWidgetElement(){
		
		$fieldset = new Html_Element("fieldset");
		$fieldset->appendChild(Html_TextNode::wrap("legend", $this->getLabel()));
		
		$selected = $this->getSelectedOptions();
		$selected_is_array = is_array($selected);
		
		$form_name = $this->getFormName();
		
		$option_index = 0;
		
		$values = $this->getOptionValues();
		foreach($values as $value){
			
			$id = "_" . $this->getName() . "_" . $option_index++;
			
			$radio = new Html_InputElement("radio");
			$radio->setAttribute("name", $form_name);
			$radio->setAttribute("id", $id);
			$radio->setAttribute("value", $value[0]);
			
			$label = new Html_Element("label");
			$label->setAttribute("for", $id);
			$label->appendText($value[1]);
			
			if(($selected_is_array && in_array($value[0], $selected)) || $value[0] == $selected){
				$radio->setAttribute("checked", "checked");	
			}
			
			$fieldset->appendChild($radio);
			$fieldset->appendChild($label);
		}
		
		return $fieldset;
	}
	
}
