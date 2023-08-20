<?php

class ArrayUtils{
	
	static function implode($glue, $array, $left_wrap = "", $right_wrap = ""){
		
		$str = "";
		$first = true;
		
		foreach($array as $element){
			
			if(!$first){
				$str .= $glue;
			}
			else{
				$first = false;
			}
			
			$str .= $left_wrap . $element . $right_wrap;
		}
		
		return $str;	
	}
}
