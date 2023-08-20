<?php 

class Html_TextNode extends Html_Node{
	
	private $text;
	
	function __construct($text){
		$this->text = $text;
	}
	
	function getText(){
		return $this->text;
	}
	
	function render($encoding){
		return htmlentities($this->text, ENT_NOQUOTES | ENT_HTML5, $encoding);
	}
	
	static function wrap($tagName, $text){
		$wrapper_element = new Html_Element($tagName);
		$wrapper_element->appendChild(new Html_TextNode($text));
		return $wrapper_element;
	}
}
