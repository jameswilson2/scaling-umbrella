<?php

class Form_SelectField extends Form_ChoiceField{
	
	function createWidgetElement(){
		
		$select = new Html_SelectElement;
		$this->setWidgetAttributes($select);
		
		if($this->isMultiple()){
			$select->setAttribute("multiple", "multiple");
		}
		
		$selected = $this->getSelectedOptions();
		$selected_is_array = is_array($selected);
		
		$values = $this->getOptionValues();
		foreach($values as $value){
			
			$option = $select->addOption($value[0], $value[1]);
			
			if(($selected_is_array && in_array($value[0], $selected)) || $value[0] == $selected){
				$option->setAttribute("selected", "selected");
			}
		}
			
		return $select;
	}	
}
