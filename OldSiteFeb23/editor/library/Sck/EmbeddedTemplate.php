<?php
/*
    A class to manipulate embedded templates. An embedded template is a PHP 
    file with editable content.
    
    Author:         Graham Daws
    Created:        July 2011
*/

class Sck_EmbeddedTemplate extends Sck_Template{

	private function interpolation($matches){
		
		$all = $matches[0];
		$start_variable = $matches[1];
		$content = $matches[2];
		$end_variable = $matches[3];
		
		if($start_variable != $end_variable){
			return $all;
		}
		
		$new_content = $this->getVariableContent($start_variable);
		
		if($new_content === null){
			return $all;
		}
		
		return "<!-- $start_variable START -->$new_content<!-- $end_variable END -->";
	}

	public function render(){
		$output = file_get_contents($this->getSourceFilename());
		$output = preg_replace_callback("/<!-- ([^ ]+) START -->(.*)<!-- ([^ ]+) END -->/sU", array(&$this, "interpolation"), $output);
		return $output;
	}
}
