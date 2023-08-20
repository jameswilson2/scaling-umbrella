<?php

class Sql_Values{
    
    private $columns = array();
    private $row = array();
    private $bind_variables = array();
    private $values = array();
    
    public function __construct($values){
        
        $this->values = $values;

        foreach($values as $key => $value){
            $this->columns[] = $key;
            $this->bind_variables[] = ":$key";
            $this->row[$key] = ":$key";
        }
    }
    
    function getColumns(){
        return $this->columns;
    }

    function getRow(){
        return $this->row;
    }
    
    function getBindVariables(){
        return $this->bind_variables;
    }

    function bindValues($pdo_statement){
        foreach($this->values as $key => $value){
            $pdo_statement->bindValue(":$key", $value, PDO::PARAM_STR);        
        }
    }
}
