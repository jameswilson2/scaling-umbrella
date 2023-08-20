<?php

class Form_HtmlField extends Form_TextareaField{

    private $tinymce_options = array();
    
    function setTinyMCEOptions($tinymce_options){
        $this->tinymce_options = $tinymce_options;
    }
    
    function createWidgetElement(){
        
        $textarea = parent::createWidgetElement();
        $textarea->removeAttribute("required"); // This is broken in Firefox when the textarea is hidden
        
        $script = new Html_Script;
        
        $tinymce_options = array_merge($this->tinymce_options, array(
            "mode" => "exact",
            "elements" => $textarea->getAttribute("id"),
            "mode" => "exact",
            "theme" => "advanced",
            "theme_advanced_toolbar_location" => "top",
            "theme_advanced_toolbar_align" => "left",
            "theme_advanced_statusbar_location" => "bottom",
            "plugins" => "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
            "theme_advanced_buttons1" => "newdocument,bold,italic,underline,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,template,cut,copy,paste,pastetext,pasteword,search,replace,bullist,numlist,outdent,indent,blockquote",
            "theme_advanced_buttons2" => "undo,redo,link,unlink,anchor,image,cleanup,help,code,tablecontrols,hr,removeformat,visualaid,sub,sup,charmap,fullscreen",
            "theme_advanced_buttons3" => ""
        ));
        
        $script->writeJS("tinyMCE.init(" . json_encode($tinymce_options) . ");", array("tinyMCE"));
        
        return array($textarea, $script);
    }
}
