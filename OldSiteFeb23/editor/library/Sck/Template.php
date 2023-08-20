<?php
/*
    Template rendering
    
    Author:         Graham Daws
    Created:        July 2011
*/

class Sck_Template{
    
    private $source_filename;
    private $output_filename;
    private $search_paths;
    private $variables = array();
    
    public function __construct($template_file, $search_paths = null){
        
        if(!is_array($search_paths)){
            $search_paths = array();
        }
        
        $search_paths[] = TEMPLATE_DIR;
        
        $this->search_paths = $search_paths;
        $this->source_filename = find_file($template_file, $search_paths);
        
        if($this->source_filename === false){
            throw new Exception("Template file '$template_file' not found");
        }
    }
    
    public function assign($name, $value){
        $this->variables[$name] = $value;
    }
    
	protected function getSourceFilename(){
		return $this->source_filename;
	}
	
	protected function getVariableContent($name){
		return $this->variables[$name];
	}
	
    private function compile(){
        
        $this->output_filename = TEMPLATE_CACHE_DIR . "/" . relative_filename($this->source_filename) . ".php";
        
        $pathinfo = pathinfo($this->output_filename);
        $dir = $pathinfo['dirname'];
        
        if(!is_dir($dir)){
            $old_umask = umask(0);
            if(@mkdir($dir, 0777, true) === false){
                throw new Exception("Permission denied for directory creation '$dir'");
            }
            umask($old_umask);
        }
        
        if(is_file($this->output_filename)){
            
            $output_file_mtime = filemtime($this->output_filename);
            
            if($output_file_mtime >= filemtime($this->source_filename) &&
            $output_file_mtime >= filemtime(__FILE__)){
                return false;
            }
        }
        
        $parser = new Sck_TemplateParser($this->search_paths);
        $php = $parser->parse(file_get_contents($this->source_filename));
        
        file_put_contents($this->output_filename, $php);
        
        return true;
    }
    
    public function render(){
        
        $this->compile();
        
        ob_start();
        include($this->output_filename);
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
}

