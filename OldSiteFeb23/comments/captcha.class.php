<?php

class Captcha{
	function __construct($presentation, $answer){
		$this->presentation = $presentation;
		$this->answer = $answer;
		$this->key = md5($this->presentation . $this->answer);
	}
	
	function getPresentation(){
		return $this->presentation;
	}
	
	function getAnswer(){
		return $this->answer;
	}
	
	function saveToSession(){
		if(!array_key_exists("captchas", $_SESSION)){
			$_SESSION["captchas"] = array();
		}
		$_SESSION["captchas"][$this->key] = serialize($this);
	}
	
	function getKey(){
		return $this->key;
	}
	
	static function getFromSession($key){
		if(!array_key_exists("captchas", $_SESSION)) return;
		return unserialize($_SESSION["captchas"][$key]);
	}
	
	static function generateArithmeticProblem(){
	
		//$add = function($a,$b){return $a + $b;};
		//$subtract = function($a,$b){return $a - $b;};
		$add = create_function('$a,$b', 'return $a + $b;');
		$subtract = create_function('$a,$b', 'return $a - $b;');
		$operations = array(array('plus','+', $add), array('minus','-', $subtract));
		
		srand(time());
		
		$first = rand(1,9);
		$second = rand(1,9);
		
		// Swap the numbers around so the biggest number is in $first
		if($second > $first){
			$temp = $second;
			$second = $first;
			$first = $temp;
		}
		
		$operation = $operations[rand(0,count($operations) - 1)];
		$operation_function = $operation[2];
		
		$present_first = $first;
		if($first < 10){
			$names = array('zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine');
			$present_first = $names[$first];
		}
		
		$present_operation = $operation[rand(0,1)];
		
		$presentation = "$present_first $present_operation $second = ";
		$answer = $operation_function($first, $second);
		
		return new Captcha($presentation, $answer);
	}
}

?>