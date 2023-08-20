<?php

class Form_Validator_Email{
    
    static function isValid($email){
        return preg_match("/^[\d\w\/+!=#|$?%{^&}*`'~-][\d\w\/\.+!=#|$?%{^&}*`'~-]*@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,6}$/ix", $email ) == 1;
    }
}
