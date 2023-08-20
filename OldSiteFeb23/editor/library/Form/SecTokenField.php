<?php

class Form_SecTokenField extends Form_HiddenField{
    
    private $key;
    
    function setKey($key){
        $this->key = $key;
    }
    
    function validate($value){
        
        $expected_value = $this->getValue();
        $valid = $value == $expected_value;
        
        if(!$valid){
            $this->setErrorMessage("sent incorrect security token or form has expired");
        }
        
        return $valid;
    }
    
    protected function getValue(){
        return hash_hmac("md5", session_id(), $this->key);
    }
    
    function getStorageKeys(){
        return array();
    }
    
    function getStorageValues(){
        return array();
    }
}
