<?php

function renderSQLColumnName($table_prefix, $field_name){
	$field_name = strtolower(str_replace("-", "_", $field_name));
	return $table_prefix . "_" . $field_name;
}

function renderSQLColumnDefinition($table_prefix, $field){
	$column_name = renderSQLColumnName($table_prefix, $field['name']);
	return "`$column_name`	varchar(255)";
}

function renderSQLColumnDefinitions($table_prefix, $fields){
	$columns = array();
	foreach($fields as $field){
		if(@$field['no_persist'] || $field['type'] == 'captcha') continue;
		$columns[] = renderSQLColumnDefinition($table_prefix, $field);
	}
	return $columns;
}

function renderSQLColumnAssignment($table_prefix, $field, $value){
	$column_name = renderSQLColumnName($table_prefix, $field['name']);
	$value_literal = mysql_real_escape_string($value);
	return "$column_name = '$value_literal'";
}

function renderSQLColumnAssignments($table_prefix, $fields, $values){
	$columns = array();
	foreach($fields as $field){
		if(@$field['no_persist'] || $field['type'] == 'captcha') continue;
		$columns[] = renderSQLColumnAssignment($table_prefix, $field, $values->getField($field['name']));
	}
	return $columns;
}
