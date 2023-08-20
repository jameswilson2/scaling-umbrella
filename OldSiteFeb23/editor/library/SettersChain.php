<?php

class SettersChain{
	
	private $object;
	private $prefix;
	
	function __construct($object, $prefix = "set"){
		$this->object = $object;
		$this->prefix = $prefix;
	}
	
	static function wrap($object){
		return new SettersChain($object);
	}
	
	function __call($name, $arguments){
		call_user_method($this->prefix . ucfirst($name), $this->object, $arguments);
		return $this;
	}
}
