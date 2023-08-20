<?php

class Form_Date_TextField extends Form_TextField{
    
    private $format = "dd/mm/yy";
    
    function setFormat($format){
        $this->format = $format;
    }
    
    function getValue(){
        
        $value = parent::getValue();
        
        if(!$value){
            return "";
        }
        
        $value = self::tokenizeDateString($value);
        
        $output = $this->format;
        $output = str_replace("dd", str_pad($value[2], 2, "0", STR_PAD_LEFT) , $output);
        $output = str_replace("mm", str_pad($value[1], 2, "0", STR_PAD_LEFT), $output);
        $output = str_replace("yy", str_pad($value[0], 4, "0", STR_PAD_LEFT), $output);
        $output = str_replace("y", str_pad(intval($value[0]) % 100, 2, "0", STR_PAD_LEFT), $output);
        
        return $output;
    }
    
    protected function prefilter($value){
        
        $matches = array();
        
        $status = preg_match(self::toRegexp($this->format), $value, $matches);
        if($status === false){
            return array();
        }
        
        $year = intval($matches["year"]);
        $month = intval($matches["month"]);
        $day = intval($matches["day"]);
        
        if($year < 100){
            $now = getdate();
            $year = (floor($now["year"]/100) * 100) + $year;
        }
        
        return array($year, $month, $day);
    }
    
    protected function validate($value){
        
        if(empty($value)){
            $this->setErrorMessage("invalid date");
            return false;
        }
        
        if(!checkdate($value[1], $value[2], $value[0])){
            $this->setErrorMessage("invalid date");
            return false;
        }
        
        return true;
    }
    
    protected function postfilter($value){
        
        $year = str_pad($value[0], 2, "0", STR_PAD_LEFT);
        $month = str_pad($value[1], 2, "0", STR_PAD_LEFT);
        $day = str_pad($value[2], 2, "0", STR_PAD_LEFT);
        
        return "$year-$month-$day";
    }
    
    private static function toRegexp($format){
        
        $regexp = preg_quote($format, "/");
        
        $regexp = str_replace("dd", "(?<day>\\d{2})",  $regexp);
        $regexp = str_replace("mm", "(?<month>\\d{2})",  $regexp);
        $regexp = str_replace("yy", "(?<year>\\d{4})",  $regexp);
        //$regexp = str_replace("y", "(?<year>\\d{2})",  $regexp);
        
        $regexp = "/" . $regexp . "/";
        return $regexp;
    }
    
    private static function tokenizeDateString($date){
        return explode("-", $date);
    }
    
    function createWidgetElement(){
        
        $textbox = parent::createWidgetElement();
        $textbox_id = $textbox->getAttribute("id");
        
        $format_js = json_encode($this->format);
        
        $script = new Html_Script;
        
        if($textbox_id){
            $js = <<<EOD
        if(jQuery.datepicker !== "undefined"){
            jQuery("#$textbox_id").datepicker({dateFormat: $format_js});
        }
EOD;
            $script->writeJS($js, array("jQuery"));
        }
        
        return array($textbox, $script);
    }
}
