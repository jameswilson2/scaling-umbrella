<?php
require_once "DateTimeUtils.php";

class CalendarView{
	
	private $month;
	private $year;
	private $date;
	private $first_weekday = MONDAY;
	
	private $WEEK_DAY_NAMES = array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");
	private $WEEK_DAY_CLASSES = array("calendar-sun", "calendar-mon", "calendar-tue", "calendar-wed", "calendar-thu", "calendar-fri", "calendar-sat");
	
	private $week_column_heading = "";
	private $week_column_class = "";
	private $week_column_callback = null;
    
	private $week_callback = null;
	private $day_callback = null;
    	
	public function __construct($month, $year){
	    
	    assert($month > 0 && $month < 13);
	    
	    $this->month = $month;
	    $this->year = $year;
	    $this->date = mktime(0, 0, 0, $month, 1, $year);
	}
	
	public function setFirstWeekday($week_day){
	    $this->first_weekday = $week_day;
	}
	
	public function setDayCallback($callback){
	    $this->day_callback = $callback;
	}
	
	public function enableWeekColumn($heading, $class, $callback){
	
	    if(!($heading instanceof HtmlElement)){
	        $heading = new HtmlTextNode($heading);
	    }
	    
	    $this->week_column_heading = $heading;
	    $this->week_column_class = $class;
	    $this->week_column_callback = $callback;
	}
	
	public function render(){
		
		$start_date = getdate($this->date);
		$use_week_column = $this->week_column_callback !== null;
		
		$table = new HtmlElement("table");
		$table->setAttribute("cellpadding", "0");
		$table->setAttribute("cellspacing", "0");
		$table->setAttribute("border", "0");
		$table->addClass("calendar");
		
		$caption = new HtmlElement("caption");
		$caption->addChild(new HtmlTextNode($start_date["month"] . " " . $start_date["year"]));
		$table->addChild($caption);
		
		$header_tr = new HtmlElement("tr");
		for($i = 0; $i < 7; $i++){
		    $th = new HtmlElement("th");
		    $weekday = ($this->first_weekday + $i) % 7;
			$th->addChild(new HtmlTextNode($this->WEEK_DAY_NAMES[$weekday]));
			$th->addClass($this->WEEK_DAY_CLASSES[$weekday]);
			$header_tr->addChild($th);
		}
		
		if($use_week_column){
		    $th = new HtmlElement("th");
		    $th->addChild($this->week_column_heading);
		    if($this->week_column_class){
		        $th->addClass($this->week_column_class);
		    }
		    $header_tr->addChild($th);
		}
		
		$table->addChild($header_tr);
		
		$week_day =  $start_date["wday"];
		$week_day_offset = ($week_day + (7 - $this->first_weekday)) % 7;
		$week = 1;
		
		$days_in_month = days_in_month($this->month, $this->year);
		$days_in_last_month = $this->getDaysInLastMonth();
		
		$row = new HtmlElement("tr");
		
		if($week_day_offset != 0){
		    $this->addPaddingDays($this->first_weekday, $week, $days_in_last_month - ($week_day_offset - 1), $days_in_last_month, $row);
		}
		
		for($day = 1; $day <= $days_in_month; $day++){
		
		    $td = new HtmlElement("td");
		    
		    $td->addClass($this->WEEK_DAY_CLASSES[$week_day]);
		    $td->addClass("calendar-week-$week");
		    $td->addClass("calendar-day-$day");
		    $td->addClass("calendar-day");
		    
		    $td->setAttribute("data-day", $day);
		    $td->setAttribute("data-week", $week);
		    
		    $td->addChild($this->getDayChildElement($day, $week_day, $td));
		    
		    $row->addChild($td);
		    
		    $week_day = ($week_day + 1) % 7;
		    $week_day_offset = ($week_day_offset + 1) % 7;
		    
		    if($week_day_offset == 0){
		        
		        if($use_week_column){
                    $row->addChild($this->renderWeekColumn($week));
		        }
		        
		        $table->addChild($row);
		        $row = new HtmlElement("tr");
		        $week++;
		    }
		}
		
		if($week_day_offset != 0){
		
		    $this->addPaddingDays($week_day, $week, 1, 7 - $week_day_offset, $row);
		    
		    if($use_week_column){
                $row->addChild($this->renderWeekColumn($week));
	        }
		}
		
		$table->addChild($row);
		
		return $table;
	}
	
	private function addPaddingDays($week_day, $week, $start_day, $end_day, $row){
	
	    for($day = $start_day; $day <= $end_day; $day++){
	        
	        $td = new HtmlElement("td");
	        $td->addChild(new HtmlTextNode($day));
	        $td->addClass($this->WEEK_DAY_CLASSES[$week_day]);
	        $td->addClass("calendar-week-$week");
	        $td->addClass("calendar-padding");
	        
	        $row->addChild($td);
	        
	        $week_day = ($week_day + 1) % 7;
	    }
	}
	
	private function getDaysInLastMonth(){
	
	    $month = $this->month;
	    $year = $this->year;
	    
	    if($month > 1){
            $month--;
	    }
	    else{
	    	$month = 12;
	        $year--;
	    }
	    
	    return days_in_month($month, $year);
	}
	
	private function getDayChildElement($day, $week_day, $td){
	    if(!$this->day_callback){
	        return new HtmlTextNode($day);
	    }
	    else{
	        $value = call_user_func($this->day_callback, $day, $week_day, $this->month, $td);
	        if(!($value instanceof HtmlElement)){
	           $value = new HtmlTextNode($value);
	        }
	        return $value;
	    }
	}
	
    private function renderWeekColumn($week){
        $td = new HtmlElement("td");
        if($this->week_column_class){
            $td->addClass($this->week_column_class);
        }
        $content = call_user_func($this->week_column_callback, $week);
        if(!($content instanceof HtmlElement)){
            $content = new HtmlTextNode($content);
        }
        $td->addChild($content);
        return $td;
	}
}
