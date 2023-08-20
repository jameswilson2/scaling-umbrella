<?php

class Html_PreRendered extends Html_Node{
	
	private $html;
	
	public function __construct($html){
		$this->html = $html;
	}
	
	function render($encoding){
		return $this->html;
	}
}
