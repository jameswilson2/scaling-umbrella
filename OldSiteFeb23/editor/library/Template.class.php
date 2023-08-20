<?php
/*
    Template rendering
    
    Author:         Graham Daws
    Created:        July 2011
*/
require_once "FilesystemUtils.php";

class TemplateParser{
    
    private $search_paths;
    
    public function __construct($search_paths){
        $this->search_paths = $search_paths;
    }
    
    public function parse($content){
        
        $content = preg_replace_callback(
            array(
                '/<!-- FOREACH ([^ ]+) IN ([^ ]+) -->(.*)<!-- END FOREACH -->/smU',
                '/<!-- INCLUDE MODULE "([^"]+)" -->/',
                '/<!-- ### ([^#]+) ### -->/',
                '/### ([^#]+) ###/'
            ),
            array(&$this, "translateSCKToPHP"), $content);
        
        if($content === null){
            throw new Exception("preg_replace_callback returned NULL");
        }
        
        return $content;
    }
    
    private function translateSCKToPHP($matches){
        
        if($matches[0][0] == '<'){
            switch(substr($matches[0], 0, 6)){
                case '<!-- I':
                    return $this->translateIncludeModule($matches[1]);
                case '<!-- #':
                    return $this->translateInterpolation($matches[1]);
                case '<!-- F':
                    return $this->translateForeach($matches[1], $matches[2], $matches[3]);
            }
        }
        else{
            return $this->translateInterpolation($matches[1]);
        }
    }
    
    private function translateInterpolation($name){
        $lookup = $this->translateKeyLookup($name);
        return "<?php echo \$this->variables$lookup;?>";
    }
    
    private function translateIncludeModule($filename){
        
        $resolved_filename = find_file($filename, $this->search_paths);
        $resolved_filename_code = "find_file('$filename', \$this->search_paths)";
		
        if($resolved_filename === false){
            return null;   
        }
        
        $file_type = filename_extension($resolved_filename);
        
        switch($file_type){
            case "inc":
                return "<?php echo file_get_contents($resolved_filename_code);?>";
            case "tpl":
                return "<?php\n\$t=new Template($resolved_filename_code, \$this->search_paths);\n\$t->variables=\$this->variables;\necho \$t->render();\n?>";
            case "php":
                return "<?php include('$resolved_filename');?>";
        }
    }
    
    private function translateForeach($element_name, $array_name, $body){
                
        if($element_name == $array_name){
            throw new Exception("Invalid template near 'FOREACH $element_name IN $array_name': element and array have the same name");
        }
        
        $template = new TemplateParser($this->search_paths);
        $php = $template->parse($body);
        
        $array_name_lookup = $this->translateKeyLookup($array_name);
        
        return "<?php if(is_array(\$this->variables$array_name_lookup)){\nforeach(\$this->variables$array_name_lookup as \$value){\n\$this->variables['$element_name']=\$value;?>\n$php\n<?php }}?>";
    }
    
    private function translateKeyLookup($key){
        
        $members = explode("." , $key);
        $output = '';
        
        foreach($members as $member){
            $output .= "['". $member . "']";
        }
        return $output;
    }
}

class Template{
    
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
        
        $parser = new TemplateParser($this->search_paths);
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

