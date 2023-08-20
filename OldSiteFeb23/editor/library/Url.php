<?php

class Url{
	
	private $scheme = "http";
	private $userinfo;
	private $host;
	private $port = 80;
	private $path = "/";
	private $fragment;
	private $query = array();
	
	private static $default_ports = null;
	
	public static function parse($url){
		
		$url_renderer = new Url;
		$url_components = parse_url($url);
		
		if(!empty($url_components["scheme"])){
			$url_renderer->setScheme($url_components["scheme"]);
		}
		
		if(!empty($url_components["user"])){
			$userinfo = $url_components["user"];
			if($url_components["pass"] !== null){
				$userinfo = $userinfo . ":" . $url_components["pass"];
			}
			$url_renderer->setUserinfo($userinfo);
		}
		
		if(!empty($url_components["host"])){
			$url_renderer->setHost($url_components["host"]);
		}
		
		if(!empty($url_components["port"])){
			$url_renderer->setPort($url_components["port"]);
		}
		
		if(!empty($url_components["path"])){
			$url_renderer->setPath($url_components["path"]);
		}
		
		if(!empty($url_components["fragment"])){
			$url_renderer->setFragment($url_components["fragment"]);
		}
		
		if(!empty($url_components["query"])){
			$query_vars = array();
			parse_str($url_components["query"], $query_vars);
			foreach($query_vars as $name => $value){
				$url_renderer->setQueryVar($name, $value);
			}
		}
		
		return $url_renderer;
	}
	
	public static function parseFromRequest(){
		
		$url = "";
		
		$scheme = "http";
		if(isset($_SERVER['HTTPS'])){
			$scheme = "https";
		}
		
		$url .= "$scheme://";
		$url .= $_SERVER["HTTP_HOST"];
		
		$server_port = $_SERVER["SERVER_PORT"];
		
		if($server_port != self::getStandardPort($scheme)){
			$url .= ":$server_port";
		}
		
		$url .= $_SERVER["REQUEST_URI"];
		
		return Url::parse($url);
	}
	
	public function setScheme($scheme){
		
		if(!is_string($scheme)){
			throw new Exception("Expected string type for scheme value");
		}
		
		$this->scheme = $scheme;
	}
	
	public function setUserinfo($userinfo){
		
		if(!is_string($userinfo)){
			throw new Exception("Expected string type for userinfo value");
		}
		
		$this->userinfo = $userinfo;
	}
	
	public function setHost($host){
	
		if(!is_string($host)){
			throw new Exception("Expected string type for scheme value");
		}
		
		$this->host = $host;
	}
	
	public function setPort($port){
		$this->port = intval($port);
	}
	
	public function setPath($path){
	
		if($path[0] != "/"){
			$path = "/$path";
		}
		
		$this->path = $path;
	}
	
	public function setFragment($fragment){
		$this->fragment = $fragment;
	}
	
	public function unsetFragment(){
		$this->fragment = null;
	}
	
	public function setQueryVar($name, $value){
		$this->query[$name] = $value;
	}
	
	public function unsetQueryVar($name){
		unset($this->query[$name]);
	}
	
	public function getQueryVar($name){
		return @$this->query[$name];
	}
	
	public function render(){
		
		if(!is_string($this->scheme) || !is_string($this->host)){
			throw new Exception("Cannot render incomplete url (need at least the scheme and host components)");
		}
		
		$output = $this->scheme . "://";
		
		if(isset($this->userinfo)){
			$output .= $this->userinfo . "@";
		}
		
		$output .= $this->host;
		
		if(isset($this->port) && $this->port != self::getStandardPort($this->scheme)){
			$output .= ":" . $this->port;
		}
		
		$output .= $this->path;
		
		if(count($this->query)){
			$output .= "?" . http_build_query($this->query);
		}
		
		if(isset($this->fragment)){
			$output .= "#" . urlencode($this->fragment);
		}
		
		return $output;
	}
	
	protected static function getStandardPort($service){
		
		if(self::$default_ports === null){
			self::$default_ports = array(
				"http" => 80,
				"https" => 443,
				"ftp" => 21
			);
		}
		
		return @self::$default_ports[$service];
	}
}
