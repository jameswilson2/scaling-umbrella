<?php
require_once(dirname(__FILE__) . "/../recaptchalib.php");

class Form_RecaptchaField extends Form_FieldBase{
	
	private $publickey = "";
	private $privatekey = "";
	protected $theme = "red";
	
    protected $container_classes = array("captcha-field");
    
	function __construct(){
        $this->setName("recaptcha");
		$this->setLabel("Verification");
	}
	
 	function setPublicKey($public_key){
 		$this->publickey = $public_key;
 	}
 	
 	function setPrivateKey($private_key){
 		$this->privatekey = $private_key;	
 	}
 	
 	function setTheme($theme){
 		$this->theme = $theme;
 	}
 	
    function loadFromSubmit($array){
        
		$challenge_value = @$array["recaptcha_challenge_field"];
		$response_value = @$array["recaptcha_response_field"];
	    
		$resp = recaptcha_check_answer($this->privatekey, $_SERVER["REMOTE_ADDR"], $challenge_value, $response_value);
		
        $this->failed = !$resp->is_valid;
		if($this->failed){
			$this->setErrorMessage($resp->error);
		}
		
		return !$this->failed;
    }
    
    function loadFromStorage($array){
    	return true;
    }
    
    function getStorageKeys(){
    	return array();
    }
     
    function getStorageValues(){
    	return array();
    }
    
    function createWidgetElement(){
    	
    	$html = "";
    	$options_js = "<script type=\"text/javascript\">var RecaptchaOptions={theme:'$this->theme'};</script>";
		$html .= $options_js;
		$html .= recaptcha_get_html($this->publickey);
    	
    	return new Html_PreRendered($html);
    }
}
