<?php

define('SUNDAY', 0);
define('MONDAY', 1);
define('TUESDAY', 2);
define('WEDNESDAY', 3);
define('THURSDAY', 4);
define('FRIDAY', 5);
define('SATURDAY', 6);

function days_in_month($month, $year){
    return cal_days_in_month(CAL_GREGORIAN, $month, $year);
}

function weeks_in_month($month, $year, $first_weekday = MONDAY){
    
    $days_in_month = days_in_month($month, $year);
    $date = getdate(mktime(0, 0, 0, $month, 1, $year));
    
    $correction = 7 - $first_weekday;
    $padding = ($date["wday"] + $correction) % 7;
    
    return ceil(($days_in_month + $padding) / 7);
}
