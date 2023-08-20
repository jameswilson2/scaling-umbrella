<?php

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
        
        $resolved_filename_code = "find_file('$filename', \$this->search_paths)";
		
		$filename_pathinfo = pathinfo($filename);
        switch($filename_pathinfo["extension"]){
            case "inc":
                return "<?php echo @file_get_contents($resolved_filename_code);?>";
            case "tpl":
                return "<?php\n\$t=new Template($resolved_filename_code, \$this->search_paths);\n\$t->variables=\$this->variables;\necho \$t->render();\n?>";
            case "php":
                return "<?php include('$filename');?>";
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