<?php

class Form_TimeField extends Form_TextField{

    private $hour = -1;
    private $minute = -1;
    private $second = -1;

    protected function prefilter($value){

        $capture = array();
        $valid = preg_match("/^(?<hour>\d+):(?<minute>\d+)(:(?<second>\d+))?$/S", $value, $capture);
        
        if($valid === false){
            $hour = -1;
            $minute = -1;
            $second = -1;
        }
        else{

            $hour = $capture["hour"];
            $minute = $capture["minute"];

            if(isset($capture["second"])){
                $second = 0;
            }
            else{
                $second = $capture["second"];
            }
            
            if($hour > 23){
                $hour = -1;
            }

            if($minute > 59){
                $minute = -1;
            }

            if($second > 59){
                $second = -1;
            }
        }

        return array(
            "hour" => $hour,
            "minute" => $minute,
            "second" => $second
        );
    }

    protected function validate($value){
        
        $hour_invalid = $value["hour"] === -1;
        $minute_invalid = $value["minute"] === -1;
        $second_invalid = $value["second"] === -1;

        if($hour_invalid && $minute_invalid && $second_invalid){
            $this->setErrorMessage("invalid value (should be a 24 hour clock value written as HH:MM or HH:MM:SS)");
            return false;
        }

        if($hour_invalid){
            $this->setErrorMessage("hour value is invalid");
            return false;
        }

        if($minute_invalid){
            $this->setErrorMessage("minute value is invalid");
            return false;
        }

        if($second_invalid){
            $this->setErrorMessage("second value is invalid");
            return false;
        }

        return true;
    }

    protected function postfilter($value){
        
        $hour = $value["hour"];
        $minute = $value["minute"];
        $second = $value["second"];

        if($hour === -1 || $tminute === -1 || $second === -1){
            return "";
        }

        $elements = array();

        $elements[] = str_pad($hour, 2, "0", STR_PAD_LEFT);
        $elements[] = str_pad($minute, 2, "0", STR_PAD_LEFT);
        
        if($second > 0){
            $elements[] = str_pad($second, 2, "0", STR_PAD_LEFT);
        }

        return implode(":", $elements);
    }

    function createWidgetElement(){
        $input = parent::createWidgetElement();
        $input->setAttribute("type", "time");
        return $input;   
    }
}
