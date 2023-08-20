<?php

class Form_Decorator_NoSubmit extends Form_DecoratorField{

    function __construct($decorated){
        parent::__construct($decorated);
    }
    
    function loadFromSubmit($assoc_array){
        return true;
    }
}
