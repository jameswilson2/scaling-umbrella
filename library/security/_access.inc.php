<?php
require 'library/config/_editor_config.php';
require 'library/_dbconn.inc.php';
require 'library/_queries.inc.php';
require 'library/_email.inc.php';
require 'library/_dates.inc.php';
require 'library/FilesystemUtils.php';
require 'library/FormValidationFunctions.php';
require 'library/Swift/swift_required.php';
require "library/rb.php";

session_start();

function default_value(&$array, $name, $value){
	if(!isset($array[$name])){
		$array[$name] = $value;
	}
}

function pluralize($value, $singular, $plural){
	return ($value == 1 ? $singular : $plural);
}

function strip_embedded_template_tags($content){
    return preg_replace(array("/<!-- [^ ]+ START -->/", "/<!-- [^ ]+ END -->/"), "", $content);
}

function redirect_to_self(){
    
    $redirect = $_SERVER["REQUEST_URI"];
    
    if(strlen($_SERVER["QUERY_STRING"]) > 0){
        $redirect .= "?" . $_SERVER["QUERY_STRING"];
    }
    
    header("location: $redirect");
    exit;
}

require 'library/Twig/Autoloader.php';
Twig_Autoloader::register();

require 'library/Autoloader.php';
Autoloader::register(dirname(dirname(__FILE__)));

function include_html($filename){
    
    $resolved_filename = find_file($filename, parent_search_paths());
    
    if($resolved_filename === false){
        return;
    }
    
    return file_get_contents($resolved_filename);
}

function create_twig_env(){
    
    $loader = new Twig_Loader_Filesystem(EDITABLE_ROOT . "editor/templates/twig");
    
    $env = new Twig_Environment($loader, array(
        "cache" => EDITABLE_ROOT . "editor/templates_c",
        "auto_reload" => true
    ));
    
    $env->addFunction("include_html", new Twig_Function_Function("include_html"));
    
    return $env;
}

function redbean_setup(){
    $host = DB_SERVER;
    $dbname = DB_DATABASE;
    $username = DB_USER;
    $password = DB_PASSWORD;
    $dsn = "mysql:dbname=$dbname;host=$host";
    R::setup($dsn, $username, $password);
}

function render_sql_column_assignments($assignments){
    
    $sql_assignments = array();
    
    for($i = 0, $count = count($assignments); $i < $count; $i += 2){
        $sql_assignments[] = $assignments[$i] . " = '" . safeaddslashes($assignments[$i + 1]) . "'";
    }
    
    $sql = implode(",\n", $sql_assignments);
    return $sql;
}

function isValidEmailAddress( $email = null ) {
	// A positive match will yield a result of 1
	return preg_match( "/^
	[\d\w\/+!=#|$?%{^&}*`'~-]
	[\d\w\/\.+!=#|$?%{^&}*`'~-]*@
	[A-Z0-9]
	[A-Z0-9.-]{0,61}
	[A-Z0-9]\.
	[A-Z]{2,6}$/ix", $email );
}
