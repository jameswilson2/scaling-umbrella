<?php
require_once 'library/_format.inc.php';

class FAQList {

	// page content
	// access private
	var $content;

	// page title - depends on selected category
	// access private
	var $title;

	// page header (h3)
	// access private
	var $header;

	// faq list
	// access private
	var $faqs;

	// sql resource - category ids and names
	// access private
	var $categories;


	function FAQList(){

		$this->buildForm();

		$this->title = SITE_NAME.' - Frequently Asked Questions';
		if (isset($_GET['filter_keyword']) && $_GET['filter_keyword'] != ""){
			// display by keyword
			$this->getByKeyword();
			$this->buildFAQ();
		} elseif (!isset($_GET['filter_category']) || $_GET['filter_category'] == ""){
			// display default
			$this->buildCategories();
		} else {
			// display by category
			$this->getByCategory();
			$this->buildFAQ();
		}
	}


	function getCategories(){
		$sql = "SELECT category_id, category_name, count(faq_id) AS num_faq FROM tbl_faq_category LEFT JOIN tbl_faq ON faq_category_id=category_id GROUP BY category_id ORDER BY category_name ASC";
		$this->categories = getPublicQuery($sql, 'Could not get categories: ');
	}


	function buildForm(){
		$this->getCategories();
		$options = "";
		while ($category = mysql_fetch_array($this->categories)){
			$_id = $category['category_id'];
			$_num_faq = $category['num_faq'];
			if ( $_num_faq > 0 ) {
				$_name = htmlspecialchars($category['category_name']);
				if ($_id==$_GET['filter_category'] && !isset($_GET['filter_keyword'])){
					$options .= "<option value='$_id' selected='selected'>$_name</option>";
				} else {
					$options .= "<option value='$_id'>$_name</option>";
				}
			}
		}
		$this->content = <<<EOD
		<form name="category" action="faq/index.php" method="get">
			<p><strong>Category:</strong> <select name="filter_category" onchange="category.submit();">
				<option value="">Please Select</option>
				$options
			</select>
			&nbsp;or &nbsp; <strong>Search:</strong> <input type="text" id="filter_keyword" name="filter_keyword" value="" />
			<input type="submit" value="GO" /></p>
		</form>
		<hr />
EOD;
	}


	function getByKeyword(){
		$filter_keyword = $_GET['filter_keyword'];
		$_filter_keyword = safeAddSlashes($filter_keyword);
		$sql2 = " AND MATCH(faq_question,faq_answer) AGAINST('$_filter_keyword')";
		$sql3 = "ORDER BY MATCH(faq_question,faq_answer) AGAINST('$_filter_keyword') DESC";

		$sql = "SELECT faq_id, faq_question, faq_answer, faq_status, category_name
				FROM tbl_faq LEFT JOIN tbl_faq_category ON faq_category_id=category_id WHERE faq_status='active'".$sql2.$sql3;
		$questions = getPublicQuery($sql, 'Could not get faqs: ');

		$this->faqs=array();

		if (mysql_num_rows($questions)!=0){

			while($question = mysql_fetch_array($questions)){
				$faq_id = $question['faq_id'];
				$faq_question = htmlspecialchars($question['faq_question']);
				$faq_answer = htmlspecialchars($question['faq_answer']);
				$category_name = htmlspecialchars($question['category_name']);

				$faq_answer = ereg_replace("\r\n", "\n", $faq_answer);
				$faq_answer = ereg_replace("\r", "\n", $faq_answer);
				$faq_answer = ereg_replace("\n\n", '</p><p>', $faq_answer);
				$faq_answer = ereg_replace("\n", '<br />', $faq_answer);

				$question  = "$faq_question ($category_name)";

				$this->faqs[] = array('Question'=> $question, 'Answer'=>$faq_answer);

			}

		}

		$this->header = "Search Results: '$filter_keyword'";
	}


	function getByCategory(){
		$filter_category = $_GET['filter_category'];
		$sql2 .= " AND faq_category_id='$filter_category'";

		$sql = "SELECT faq_id, faq_question, faq_answer, faq_status
				FROM tbl_faq WHERE faq_status='active'".$sql2." ORDER BY faq_question ASC";
		$questions = getPublicQuery($sql, 'Could not get faqs: ');

		$sql = "SELECT category_name FROM tbl_faq_category WHERE category_id='$filter_category'";
		$category = getPublicQuery($sql, 'Could not get category: ');

		$category = mysql_fetch_array($category);
		$category_name = htmlspecialchars($category['category_name']);

		$this->faqs=array();

		if (mysql_num_rows($questions)!=0){

			while($question = mysql_fetch_array($questions)){
				$faq_id = $question['faq_id'];
				$faq_question = htmlspecialchars($question['faq_question']);
				$faq_answer = encodetext($question['faq_answer']);

				$this->faqs[] = array('Question'=> $faq_question, 'Answer'=>$faq_answer);
			}

		}

		$this->title .= ' - '.$category_name;
		$this->header = "$category_name";

	}


	function buildFAQ(){
		if (count($this->faqs)==0){
			$this->content .= <<<EOD
			<h3>$this->header</h3>
			<p>No FAQs Found</p>
EOD;
		} else {
			$this->content .= "<h3 style=\"margin-bottom:0;\">$this->header</h3>";
			$this->content .= "<p><strong><a href=\"faq/\">Back to category list</a></strong></p>";
			$this->content .= "<div id=\"jquery-accordion\">";
			foreach($this->faqs as $faq){
				$question = $faq['Question'];
				$answer = $faq['Answer'];
				$this->content .= <<<EOD
			<h4>$question</h4>
			<div>
				<p>$answer</p>
			</div>
EOD;
			}
			$this->content .= "</div>";
		}

	}


	function buildCategories(){
		$this->getCategories();
		$this->content .= "<h3>Please select a category</h3>";
		$this->content .= "<ul class=\"list\">";
		while ($category = mysql_fetch_array($this->categories)) {
			$_id = $category['category_id'];
			$_num_faq = $category['num_faq'];
			if ( $_num_faq > 0 ) {
				$_name = htmlspecialchars($category['category_name']);
				$this->content .= "\n<li><a href=\"faq/index.php?filter_category=$_id\">$_name</a> ($_num_faq)</li>";
			}
		}
		$this->content .= "</ul>";
	}


	function getTitle(){
		return $this->title;
	}

	function getContent(){
		return $this->content;
	}

}

?>