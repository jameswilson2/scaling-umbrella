<?php

function snippetwop($text,$length=64,$tail="...") {
    $text = trim($text);
    $txtl = strlen($text);
    if($txtl > $length) {
        for($i=1;$text[$length-$i]!=" ";$i++) {
            if($i == $length) {
                return substr($text,0,$length) . $tail;
            }
        }
        for(;$text[$length-$i]=="," || $text[$length-$i]=="." || $text[$length-$i]==" ";$i++) {;}
        $text = substr($text,0,$length-$i+1) . $tail;
    }
    return $text;
}

function encodetext($text, $flags = ENT_COMPAT, $encoding = "ISO-8859-1"){
    
    $text = htmlentities($text, $flags, $encoding);
    
    // Bold and italics
    $text = str_replace(array('[b]', '[B]'), '<strong>', $text);
    $text = str_replace(array('[eb]', '[EB]'), '</strong>', $text);
    $text = str_replace(array('[i]', '[I]'), '<em>', $text);
    $text = str_replace(array('[ei]', '[EI]'), '</em>', $text);

    // Paragraphs and line breaks
    $text = ereg_replace("\r\n", "\n", $text);
    $text = ereg_replace("\r", "\n", $text);
    $text = ereg_replace("\n\n", '</p><p>', $text);
    $text = ereg_replace("\n", '<br />', $text);

    // Hyperlinks
    $text = eregi_replace(
      '\\[L]([-_./a-z0-9!&%#?+,\'=:;@~]+)\\[EL]',
      '<a href="http://\\1" target="_blank">\\1</a>', $text);
    $text = eregi_replace(
      '\\[L=([-_./a-z0-9!&%#?+,\'=:;@~]+)]([^\\[]+)\\[EL]',
      '<a href="http://\\1" target="_blank">\\2</a>', $text);

    return $text;

}

function parse_date_DDMMYYYY($date_string){
	$date_parts = explode("/", trim($date_string));
	return mktime(0, 0, 0, $date_parts[1], $date_parts[0], $date_parts[2]);
}

?>