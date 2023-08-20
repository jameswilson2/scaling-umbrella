<?php

class Html_InputElement extends Html_Element{
	
	function __construct($type){
		parent::__construct("input");
		$this->setAttribute("type", $type);
		$this->addClass("input-$type");
	}	
}
