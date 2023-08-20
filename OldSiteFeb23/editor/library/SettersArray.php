<?php

class SettersArray{
	static function set($object, $setters, $prefix = "set"){
		foreach($setters as $name => $value){
			$method_name = $prefix . ucfirst($name);
			if(method_exists($object, $method_name)){
				$object->{$method_name}($value);
			}
		}
		return $object;
	}
}
