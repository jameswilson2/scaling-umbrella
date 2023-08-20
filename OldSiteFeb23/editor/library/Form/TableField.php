<?php

class Form_TableField extends Form_FieldBase{
    
    private $prototype;
    private $rows = array();
    private $add_button_text = "Add Row";
    private $min_rows;
    private $max_rows;
    
    function setPrototype($prototype){
        
        $name = $this->getName();
        assert($name);
        
        $this->prototype = $prototype;
    }
    
    function setAddButtonText($text){
        $this->add_button_text = $text;
    }
    
    function setMinRows($min_rows){
        $this->min_rows = $min_rows;    
    }
    
    function setMaxRows($max_rows){
        $this->max_rows = $max_rows;
    }
    
    function getStorageKeys(){
        return $this->prototype->getStorageKeys();
    }
    
    function getStorageValues(){
        
        $values = array();
        
        foreach($this->rows as $row){
            
            $row_value = array();
            
            foreach($row->getFields() as $column){
                $row_value = array_merge($row_value, $column->getStorageValues());
            }
            
            $values[] = $row_value;
        }
        
        return array($this->getName() => $values);
    }
    
    function loadFromSubmit($data){
        
        $data = @$data[$this->getName()];
        
        if(!is_array($data)){
            
            $this->rows = array();
            
            if($this->min_rows !== null && count($rows) < $this->min_rows){
                $this->setErrorMessage("not enough rows: the minimum number of rows is " . $this->min_rows);
                return false;
            }
            
            return true;
        }
        
        $status = true;
        $values = array();

        $field_prototypes = $this->prototype->getFields();
        
        foreach($field_prototypes as $field){
            
            $name = $field->getName();
            
            $count = count(@$data[$name]);
            for($i = 0; $i < $count; $i++){
                $values[$i][$name] = $data[$name][$i];
            }
        }
        
        $rows = array();
        
        foreach($values as $value){
            
            if($this->max_rows !== null && count($rows) == $this->max_rows){
                $this->setErrorMessage("too many rows: the maximum number of rows is " . $this->max_rows);
                return false;
            }
            
            $row = clone $this->prototype;
            $status = min($status, $row->loadFromSubmit($value));
            $rows[] = $row;
        }
        
        $this->rows = $rows;
        
        if($this->min_rows !== null && count($rows) < $this->min_rows){
            $this->setErrorMessage("not enough rows: the minimum number of rows is " . $this->min_rows);
            return false;
        }
        
        return $status;
    }
    
    function loadFromStorage($data){
        
        $name = $this->getName();
        
        if(!isset($data[$name])){
            return false;
        }
        
        $values = $data[$name];
        
        if(!is_array($values)){
            return false;
        }
        
        $rows = array();
        
        foreach($values as $row_values){
            
            $row = clone $this->prototype;
              
            $status = min($status, $row->loadFromStorage($row_values));
            $rows[] = $row;
        }
        
        $this->rows = $rows;
        
        return true;
    }
    
    
    function createContainerElement(){
        return new Html_Element("fieldset");
    }
    
    function createLabelElement(){
        return Html_TextNode::wrap("legend", $this->getLabel());
    }
        
    function createWidgetElement(){
        
        $name = $this->getName();
        
        $table_id = "{$name}_table";
        
        $table = new Html_TableElement;
        $table->setAttribute("id", $table_id);
        $table->setAttribute("width", "100%");
        $table->setATtribute("style", "display:none");
        
        $template_source = "";
        
        $thead = $table->getTHead();
        foreach($this->prototype->getFields() as $field_prototype){
            
            $thead->appendChild(Html_TextNode::wrap("th", $field_prototype->getLabel()));
            
            $template_source .= "<td>";
            $template_source .= $this->createAggregateWidgetElement($field_prototype)->render();
            $template_source .= "</td>";
        }
        
        $tbody = $table->getTBody();
        foreach($this->rows as $row){
            
            $tr = new Html_Element("tr");
            
            foreach($row->getFields() as $column){
                
                $widget = $this->createAggregateWidgetElement($column);
                
                $td = new Html_Element("td");
                $td->appendChild($widget);
                
                $tr->appendChild($td);
            }
            
            $tbody->appendChild($tr);
        }
        
        $template_id = $this->getName() . "_row_template";
        
        $template = new Html_Element("script");
        $template->setAttribute("type", "text/template");
        $template->setAttribute("id", $template_id);
        $template->appendChild(new Html_PreRendered($template_source));
        
        $script = new Html_Element("script");
        $script->setAttribute("type", "text/javascript");
        
        $add_button_text_js = json_encode($this->add_button_text);
        $max_rows_js = ($this->max_rows !== null ? intval($this->max_rows) : "Infinity");
        
        $script_source = <<<EOD
(function(require){

if(require === undefined){
    var require = function(depends, anonymousModule){
        anonymousModule(jQuery);
    }
}

require(["jquery"], function($){
    
    var table = document.getElementById("$table_id");
    var tbody = $("tbody", table).get(0);
    var templateSource = $("#$template_id").html();
    var rowCount = 0;
    var maxRows = $max_rows_js;
    
    function updateViewState(){
        
        if(rowCount >= maxRows){
            add.setAttribute("disabled", "disabled");
        }
        else{
            add.removeAttribute("disabled");
        }
        
        if(rowCount == 0){
            $(table).hide();
        }
        else{
            $(table).show();
        }
    }
    
    function addDeleteColumn(tr){
        
        var td = document.createElement("td");
        td.className = "delete-td";
        
        var deleteButton = document.createElement("span");
        deleteButton.className = "delete";
        
        deleteButton.onclick = function(){
            $(tr).fadeOut(500, function(){
                $(tr).remove();
                rowCount -= 1;
                updateViewState();
            });
            return false;
        }
        
        td.appendChild(deleteButton);
        tr.appendChild(td);
    }
    
    $("tr", tbody).each(function(){
        addDeleteColumn(this);
        rowCount += 1;
    });
    
    var add = document.createElement("button");
    add.appendChild(document.createTextNode($add_button_text_js));
    
    add.onclick = function(){
        
        var tr = document.createElement("tr");
        tbody.appendChild(tr);
        $(tr).html(templateSource);
        
        addDeleteColumn(tr);
        
        rowCount += 1;
        updateViewState();
        
        return false;
    }
    
    $(table).addClass("form_table").after(add).show();
    updateViewState();
});

})((typeof require !== "undefined" && require) || undefined);

EOD;
            
        $script->appendChild(new Html_PreRendered($script_source));
        
        $noscript = Html_TextNode::wrap("noscript", "Error! This form control requires the support of Javascript in your web browser. Check your web browser settings to enable Javascript.");
         
        return array($table, $template, $script, $noscript);
    }
    
    private function createAggregateWidgetElement($field){
        
        $table_name = $this->getName();
        
        $widget = $field->createWidgetElement();
        $widget->setAttribute("name", $table_name . "[" . $widget->getAttribute("name") . "][]");
        $widget->unsetAttribute("id");
        return $widget;
    }
}

