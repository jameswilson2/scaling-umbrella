<?php

function buildNewsLink($news_id, $title){
	$title = encodeNewsURL($title);
	$link = "news/article/$news_id/$title/";
	$link = strtolower($link);
	return $link;

}


function encodeNewsURL($text){
	$text = ereg_replace('[^-_A-Za-z0-9]', '-', $text);
	$text = str_replace('--', '-', $text);
	$text = str_replace('---', '-', $text);
	return $text;
}


?>