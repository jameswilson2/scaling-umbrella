<?php

class Html_PageControl{
	
	public static function render($current_page, $max_page, $url, $page_var = "page"){
	
		$BUTTONS = 10;
		
		$current_page = min($current_page, $max_page);
		$page_links = array();
		
		if($max_page < 2){
			return "";
		}
		
		$start = ($BUTTONS * (ceil($current_page / $BUTTONS) - 1)) + 1;
		$end = min(($start - 1)  + $BUTTONS, $max_page);
		
		if($current_page > 1){
			$url->setQueryVar($page_var, $current_page - 1);
			$url_string = $url->render();
			$page_links[] = "<span class=\"paging_page\"><a href=\"$url_string\" title=\"Previous\">&lt;&lt;</a></span>";
		}
		
		for($i = $start; $i <= $end; $i++){
			if($i == $current_page){
				$page_links[] = "<span class=\"paging_selected\">$i</span>";
			}
			else{
				$url->setQueryVar($page_var, $i);
				$url_string = $url->render();
				$page_links[] = "<span class=\"paging_page\"><a href=\"$url_string\">$i</a></span>";
			}
		}
		
		if($current_page < $max_page){
			$url->setQueryVar($page_var, $current_page + 1);
			$url_string = $url->render();
			$page_links[] = "<span class=\"paging_page\"><a href=\"$url_string\" title=\"Next\">&gt;&gt;</a></span>";
		}
		
		$page_links = implode("", $page_links);
		return "<div class=\"paging\">$page_links - Page $current_page of $max_page</div>";
	}
}
