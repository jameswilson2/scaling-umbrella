<?php

class Crust_DB_Adapter_Factory{
    
    static function convert($adaptee){
        
        if($adaptee instanceof PDO){
            return new Crust_DB_Adapter_PDO($adaptee);
        }
                
        throw new Crust_DB_Adapter_Exception("could not find appropiate adapter class for " . get_class($adaptee));
    }
}
