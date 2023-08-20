<?php

function createRandString () {
	srand((double)microtime()*1000000);
	$chars=range('A','Z');
	// $numbers=range(0,9);
	// $chars=array_merge($letters,$numbers);
	$randString='';
	for ( $i=0;$i<8;$i++ ) {
		shuffle($chars);
		$randString.=$chars[0];
	}
	return $randString;
}


?>