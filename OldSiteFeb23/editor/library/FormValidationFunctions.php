<?php

function valid_email_address($email){
	return preg_match( "/^
	[\d\w\/+!=#|$?%{^&}*`'~-]
	[\d\w\/\.+!=#|$?%{^&}*`'~-]*@
	[A-Z0-9]
	[A-Z0-9.-]{0,61}
	[A-Z0-9]\.
	[A-Z]{2,6}$/ix", $email ) == 1;
}


function validate_digit($digit){
	return strlen($digit) == 1 && $digit >= '0' && $digit <= '9';
}

function validate_letter($letter){
	return strlen($letter) == 1 && (($letter >= 'a' && $letter <= 'z') || ($letter >='A' && $letter <='Z'));
}

function valid_postcode($postcode){
	/*
		Remember to call test_postcode_validator() when this function changes to make sure it still validates correctly!
	*/
	/*
		This regular expression was copied and adapted from Wikipedia @
			http://en.wikipedia.org/w/index.php?title=Postcodes_in_the_United_Kingdom&oldid=429369832
	*/
	return preg_match("/^GIR 0AA|^[A-PR-UWYZ]([0-9][0-9A-HJKPS-UW]?|[A-HK-Y][0-9][0-9ABEHMNPRV-Y]?) [0-9][ABD-HJLNP-UW-Z]{2}$/i", $postcode) == 1;
}

function test_postcode_validator(){
	
	$valid_postcodes = array(
		"A9 9AA",
		"A99 9AA",
		"AA9 9AA",
		"AA99 9AA",
		"A9A 9AA",
		"AA9A 9AA",
		"SW1A 0AA",
		"SW1A 1AA",
		"SW1A 2AA",
		"GIR 0AA",
		"S2 4SU",
		"SE9 2UG",
		"LA9 4LU"
	);
	
	foreach($valid_postcodes as $postcode){
		if(!valid_postcode($postcode)){
			throw new Exception("TEST FAIL: valid postcode $postcode was incorrectly detected as invalid by valid_postcode()");
		}
	}
	
	$invalid_postcodes = array(
		"9LA9 4LU",
		"LA9  4LU",
		"LA94LU",
		"LA94\$U"
	);
	
	foreach($invalid_postcodes as $postcode){
		if(valid_postcode($postcode)){
			throw new Exception("TEST FAIL: invalid postcode $postcode was incorrectly detected as valid by valid_postcode()");
		}
	}
}

