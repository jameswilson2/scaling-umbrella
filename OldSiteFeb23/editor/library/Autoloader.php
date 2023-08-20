<?php

class Autoloader{
	
	private $base_dir;
	private $prefix;
	
	function __construct($base_dir, $prefix = null){
		$this->base_dir = $base_dir;
		$this->prefix = $prefix;
	}
	
	function loadClassFile($class_name){
		
		if($this->prefix && strpos($class_name, $this->prefix) !== 0){
			return;
		}
		
		$filename = $this->base_dir . "/" . str_replace("_", "/", $class_name) . ".php";
		
		if(is_file($filename)){
			require($filename);
		}
	}
	
	static function register($base_dir, $prefix = null){
		$loader = new self($base_dir, $prefix);
		spl_autoload_register(array($loader, "loadClassFile"));
	}
}
