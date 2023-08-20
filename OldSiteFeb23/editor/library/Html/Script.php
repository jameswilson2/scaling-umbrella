<?php

class Html_Script extends Html_Element{
    
    function __construct(){
        parent::__construct("script");
    }
    
    function writeJS($code, $requires = array()){
        
        $this->setAttribute("type", "text/javascript");
        
        if(count($requires)){
            
            $requires_js = json_encode($requires);
            $requires_references_js = implode(", ", $requires);
            
            $js = <<<EOD
if(typeof require === "undefined"){
    require = function(depends, anonymousModule){
        anonymousModule($requires_references_js);
    };
}
require($requires_js, function(){
   $code 
});
EOD;
        }
        else{
            $js = $code;
        }
        
        $this->appendChild(new Html_PreRendered($js));
    }
    
    static function createInlineJS($code, $requires = array()){
        $script = new Html_Script;
        $script->writeJS($code, $requires);
        return $script;
    }
}
