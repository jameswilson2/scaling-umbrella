<?php

class TableView{
	
	private $query;
	private $columns;
	
	private $url;
	private $limit = 25;
	private $page = 1;
	private $order_by;
	private $order_type;
	
	private $limit_var_name;
	private $page_var_name = "page";
	private $order_by_var_name = "order";
	private $order_type_var_name = "order-type";
	
	function __construct($query, $columns){
		
		if(is_string($query)){
			$query = new Sql_Select($query);
		}
		
		$this->query = clone $query;
		$this->columns = $columns;
	}
	
	function setUrl($url){
		
		if(is_string($url)){
			$url = Url::parse($url);
		}
		
		$this->url = $url;
	}
	
	function setLimit($limit){
		$this->limit = $limit;
	}
	
	function setPage($page){
		$this->page = max(intval($page), 1);
	}
	
	function setOrdering($order_by, $order_type){
		
		$this->order_by = $order_by;
		$this->order_type = strtolower($order_type);
	}
	
	function setQueryVarNames($names){
		$this->limit_var_name = @$names["limit"];
		$this->page_var_name = @$names["page"];
		$this->order_by_var_name = @$names["order_by"];
		$this->order_type_var_name = @$names["order_type"];
	}
	
	function loadViewStateFromUrl($url){
		
		$this->setUrl($url);
		
		if($this->limit_var_name){
			$this->limit = $this->url->getQueryVar($this->limit_var_name);
		}
		
		if($this->page_var_name){
			$this->setPage($this->url->getQueryVar($this->page_var_name));
		}
		
		if($this->order_by_var_name && $this->order_type_var_name){
			$this->setOrdering($this->url->getQueryVar($this->order_by_var_name), $this->url->getQueryVar($this->order_type_var_name));
		}
	}
	
	function getTotalNumberOfRows(){
		$sql = Sql_Select::createCountRowsStatement($this->query);

		$query = getQuery($sql->render());
		$row = mysql_fetch_array($query);
		return $row["count"];
	}
	
	function render(){
		
		$row_count = $this->getTotalNumberOfRows();
		$page = $this->page;
		$rows_per_page = ($this->limit ? $this->limit : $row_count);
		
		$max_page = ceil(max($row_count, 1) / max($rows_per_page, 1));
		$offset = $rows_per_page * (max(min($page, $max_page) - 1, 0));
		
		$this->query->setLimit($this->limit);
		$this->query->setOffset($offset);
		
		$html = "";
		$html .= "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" class=\"table\">";
		
		$html .= "<tr class=\"rowstrong\">";
		foreach($this->columns as $column){
			
			$label = htmlspecialchars(@$column["label"]);
			
			if(!$label && isset($column["name"])){
				$label = htmlspecialchars(ucwords(str_replace(array("_", "-"), " ", $column["name"])));
			}
			
			if(isset($column["orderable"]) && $this->order_by_var_name){
				
				$name = $column["name"];
				
				$url = clone $this->url;
				$url->setQueryVar($this->order_by_var_name, $name);
				
				$order_icon = "";
				
				if($this->order_by == $name){
					if($this->order_type == "asc" || !$this->order_type){
						$url->setQueryVar($this->order_type_var_name, "desc");
						$order_icon = "<span class=\"order-asc-icon\">&nbsp;</a>";
					}
					else{
						$url->setQueryVar($this->order_type_var_name, "asc");
						$order_icon = "<span class=\"order-desc-icon\">&nbsp;</a>";
					}
				}
				else{
					$url->setQueryVar($this->order_type_var_name, "asc");
				}
				
				$href = $url->render();
				$label = "<a href=\"$href\">$label$order_icon</a>";
			}
			
			$html .= "<th>$label</th>";
		}
		$html .= "</tr>";
		
		if($this->order_by){
			
			$order_type = $this->order_type;
			if($order_type == "asc" || !$order_type){
				$order_type = "ASC";
			}
			else{
				$order_type = "DESC";
			}
			
			$order_by = $this->order_by;
			
			$this->query->clearOrderBy();
			$this->query->addOrderBy("$order_by $order_type");
		}
		
		$query = getQuery($this->query->render());
		while($row = mysql_fetch_array($query)){
			
			$tr = "";
			
			foreach($this->columns as $column){
				if(isset($column["name"])){

					$name = $column["name"];
					
					$value = $row[$name];;
					$value_html = htmlentities($value);

					switch(@$column["type"]){
						case "boolean":
							
							if($value == "1"){
								$img = "presentation/approve_status.gif";
								$text = "YES";
							}
							else{
								$img = "presentation/reject_status.gif";
								$text = "NO";
							}
							
							$tr .= "<td><img src=\"$img\" alt=\"$text\" /></td>";
							
							break;

						default:
							$tr .= "<td>$value_html</td>";
					}
					
				}
				else if(isset($column["render"])){
					$tr .= "<td>" . call_user_func($column["render"], $row, $column) . "</td>";
				}
			}
			
			$html .= "<tr class=\"row\">$tr</tr>";
		}
		
		$html .= "</table>";
		
		$html .= Html_PageControl::render($page, $max_page, $this->url, $this->page_var_name);
		
		return $html;
	}
	
	public static function encodeCSVFieldValue($value){
		if(preg_match("/[\",\\r\\n]/m", $value)){
			$value = str_replace("\"", "\"\"", $value);
			return "\"$value\"";
		}
		else{
			return $value;
		}
	}
	
	function renderCSV(){
		
		$row_count = $this->getTotalNumberOfRows();
		$page = $this->page;
		$rows_per_page = ($this->limit ? $this->limit : $row_count);
		
		$max_page = ceil(max($row_count, 1) / max($rows_per_page, 1));
		$offset = $rows_per_page * (max(min($page, $max_page) - 1, 0));
		
		$this->query->setLimit($this->limit);
		$this->query->setOffset($offset);
		
		$csv = "";
		
		$header_fields = array();
		foreach($this->columns as $column){
		
			$label = @$column["label"];
			
			if(!$label && isset($column["name"])){
				$label = ucwords(str_replace(array("_", "-"), " ", $column["name"]));
			}
			
			$label = self::encodeCSVFieldValue($label);
			$header_fields[] = $label; 
		}
		$csv = implode(",", $header_fields) . "\r\n";
		unset($header_fields);
		
		$query = getQuery($this->query->render());
		while($row = mysql_fetch_array($query)){
			
			$fields = array();
			
			foreach($this->columns as $column){
				if(isset($column["name"])){
					$name = $column["name"];
					$fields[] = self::encodeCSVFieldValue($row[$name]);
				}
				else if(isset($column["render"])){
					$fields[] = self::encodeCSVFieldValue(call_user_func($column["render"], $row, $column));
				}
			}
			
			$csv .= implode(",", $fields) . "\r\n";
		}
		
		return $csv;
	}
}
