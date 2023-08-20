<?php

class Crust_DB_Adapter_PDO implements Crust_DB_Adapter{
    
    private $pdo;
    private $statement;
    
    function __construct($pdo){
        $this->pdo = $pdo;    
    }
    
    function getErrorInfo(){
        
        if($this->statement){
            return $this->statement->errorInfo();
        }
        
        return $this->pdo->errorInfo();
    }
    
    function execute($sql, $values = array()){
        
        $statement = $this->pdo->prepare($sql);
        
        foreach($values as $key => $value){
            
            $param_type = PDO::PARAM_STR;
            
            if(is_numeric($value) || is_null($value)){
                $param_type = PDO::PARAM_INT;
            }
            
            if(is_string($key)){
                $statement->bindValue($key, $value, $param_type);
            }
            else if(is_numeric($key)){
                $statement->bindValue($key + 1, $value, $param_type);
            }
        }
        
        $this->statement = $statement;
        
        return $this->statement->execute();
    }
    
    function fetch(){
        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }
    
    function fetchAll(){
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
