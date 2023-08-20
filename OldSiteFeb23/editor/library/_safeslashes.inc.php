<?php

function safeAddSlashes($string){
	if (get_magic_quotes_gpc()){
		return $string;
	} else {
		return addslashes($string);
	}
}

?>