<?php

class Html_Element extends Html_Node{
	
	private $name;
	private $attributes = array();
	private $children = array();
	
	function __construct($name){
		$this->name = $name;
	}
	
	function getTagName(){
		return $this->name;
	}
	
	function insertBefore($inserted_node, $adjacent_node){
		
		if(!$this->isOwnChild($adjacent_node)){
			return false;
		}
		
		$insert_index = $adjacent_node->getChildIndex();
		
		array_splice($this->children, $insert_index, 0, array($inserted_node));
		
		$child_count = count($this->children);
		
		for($index = $insert_index; $index < $child_count; $index++){
			$node = $this->children[$index];
			$node->setParentNode($this, $index);
		}
		
		return true;
	}
	
	function appendChild(Html_Node $node){
		
		$node->detach();
		$this->children[] = $node;
		$index = count($this->children) - 1;
		$node->setParentNode($this, $index);
		
		return true;
	}
	
	function appendText($text){
		$this->appendChild(new Html_TextNode($text));
	}
	
	function appendHtml($html){
		$this->appendChild(new Html_PreRendered($html));
	}
	
	function removeChild(Html_Node $node){
		
		if(!$this->isOwnChild($node)){
			return false;
		}
		
		$this->children = array_splice($this->children, $node->getChildIndex(), 1);
		
		$node->setParentNode(null, null);
		
		return true;
	}
	
	function clear(){
		
		foreach($this->children as $child_node){
			$child_node->setParentNode(null, null);
		}
		
		$this->children = array();
	}
	
	private function isOwnChild($node){
		$parent = $node->getParentNode();
		return $parent === $this;
	}
	
	function getFirstChild(){
		if(count($this->children)){
			return $this->children[0];
		}
	}
	
	function getChild($index){
		
		$last_index = count($this->children) - 1;
		
		if($index < 0 || $index > $last_index){
			return;
		}
		
		return $this->children[$index];
	}
	
	function setAttribute($name, $value = true){
		$this->attributes[$name] = $value;
	}
	
	function unsetAttribute($name){
		unset($this->attributes[$name]);
	}
	
	function removeAttribute($name){
		unset($this->attributes[$name]);
	}
	
	function getAttribute($name){
		return $this->attributes[$name];
	}
	
	function addClass($class){
		
		$attributes = $this->attributes;
		
		if(isset($attributes["class"])){
			$classes = explode(" ", $attributes["class"]);
		}
		else{
			$classes = array();
		}
		
		if(!in_array($class, $classes)){
			$classes[] = $class;
		}
		
		$this->attributes["class"] = implode(" ", $classes);
	}
	
	private static function isVoidElement($tagName){
		return preg_match("/^area|base|br|col|command|embed|hr|img|input|keygen|link|meta|param|source|track|wbr$/S", $tagName);
	}
	
	private static function renderAttribute($name, $value, $encoding){
		
		if(is_bool($value) && $value){
			return $name;
		}
		
		$value_html = htmlentities($value, ENT_COMPAT | ENT_HTML5, $encoding);
		return "$name=\"$value_html\"";
	}
	
	function render($encoding = "UTF-8"){
		
		$is_void_element = self::isVoidElement($this->name);
		$has_children = count($this->children);
		
		$rendered_attrs = array("");
		foreach($this->attributes as $name => $value){
			$rendered_attrs[] = self::renderAttribute($name, $value, $encoding);
		}
		
		$name = $this->name;
		$attrs_html = implode(" ", $rendered_attrs);
		
		if($is_void_element){
			$end_opening_tag = "/>";
			$closing_tag = "";
		}
		else{
			$end_opening_tag = ">";
			$closing_tag = "</{$name}>";
		}
		
		$html = "<{$name}{$attrs_html}{$end_opening_tag}";
		
		if($has_children && !$is_void_element){
			foreach($this->children as $child_node){
				$html .= $child_node->render($encoding);
			}
		}
		
		$html .= $closing_tag;
		
		return $html;
	}
}
