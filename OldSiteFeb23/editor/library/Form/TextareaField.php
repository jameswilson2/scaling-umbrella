<?php

class Form_TextareaField extends Form_TextField{
	
	function createWidgetElement(){
		$textarea = new Html_Element("textarea");
        $this->setWidgetAttributes($textarea);
		$textarea->appendChild(new Html_TextNode($this->getValue()));
		return $textarea;
	}
}
