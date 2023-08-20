<?php

class Html_Node{
	
	private $parent_node;
	private $child_index;
	
	function getParentNode(){
		return $this->parent_node;
	}
	
	function getChildIndex(){
		return $this->child_index;
	}
	
	protected function setParentNode($parent_node, $child_index){
		$this->parent_node = $parent_node;
		$this->child_index = $child_index;
	}
	
	function detach(){
		if($this->parent_node !== null){
			$this->parent_node->removeChild($this);
		}
	}
	
	function render($encoding){
		return "";
	}
	
	function getNextSibling(){
			
		$index = $this->getChildIndex();
		$parent = $this->getParentNode();
		
		if($index !== null && $parent !== null){
			return $parent->getChild($index + 1);
		}
	}
}
