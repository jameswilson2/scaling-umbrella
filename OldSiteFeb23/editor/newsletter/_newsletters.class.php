<?php
require_once 'library/_table.class.php';

// class to produce a list of comments to a given set of filters/sorts

class NewsletterTable extends Table{

	function NewsletterTable(){
		parent::Table();
	}


	function setQuery(){
		$this->sql = "SELECT newsletter_id, newsletter_title, newsletter_date, newsletter_status
					FROM tbl_newsletter WHERE newsletter_id".$this->sql_filter.$this->sql_sort;
	}


	function getOrder(){
		if (isset($_GET['order']) && $_GET['order']!=''){
			$order = $_GET['order'];
			switch ($order){
				case 'titleasc':
					$this->sql_sort = " ORDER BY newsletter_title ASC";
					$this->query_sort = "order=titleasc";
					$this->form_sort = "titleasc";
					break;

				case 'titledesc':
					$this->sql_sort = " ORDER BY newsletter_title DESC";
					$this->query_sort = "order=titledesc";
					$this->form_sort = "titledesc";
					break;

				case 'dateasc':
					$this->sql_sort = " ORDER BY newsletter_date ASC";
					$this->query_sort = "order=dateasc";
					$this->form_sort = "dateasc";
					break;

				case 'datedesc':
					$this->sql_sort = " ORDER BY newsletter_date DESC";
					$this->query_sort = "order=datedesc";
					$this->form_sort = "datedesc";
					break;

				default:
					$this->sql_sort = " ORDER BY newsletter_date DESC";
					$this->query_sort = "order=datedesc";
					$this->form_sort = "datedesc";
					break;
			}
		}
	}


	function getFilters(){

	}


	function getTableHeader(){

		$query_string = implode('&amp;',$this->query_filter);

		if ($this->form_sort == 'titleasc'){
			$link_title = $this->self.'?'.$query_string.'&amp;order=namedesc';
		} else {
			$link_title = $this->self.'?'.$query_string.'&amp;order=titleasc';
		}

		if ($this->form_sort == 'datedesc'){
			$link_date = $this->self.'?'.$query_string.'&amp;order=dateasc';
		} else {
			$link_date = $this->self.'?'.$query_string.'&amp;order=datedesc';
		}



		$this->header = <<<EOD
		<table width="100%" border="0" cellpadding="3" cellspacing="0" class="table">
			<tr class="rowstrong">
				<td><a href="$link_title">Title</a></td>
				<td><a href="$link_date">Posted</a></td>
				<td>Status</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
EOD;

	}


	function renderRow($row){
		
		$newsletter_id = $row['newsletter_id'];
		$newsletter_title = htmlspecialchars($row['newsletter_title']);
		$newsletter_status = htmlspecialchars($row['newsletter_status']);
		$newsletter_date = $row['newsletter_date'];

		if ($newsletter_status!='sent'){
			$sent = <<<EOD
			<form method="POST" action="newsletter/send.php?nid=$newsletter_id" onsubmit="return sendNewsletterOnSubmit();">
				<input type="submit" onclick="return confirmSendNewsletter(this)" value="Send" />
			</form>
EOD;
		} else {
			$sent = "&nbsp;";
		}

		$content_row = <<<EOD
		<tr class="row">
			<td>$newsletter_title</td>
			<td>$newsletter_date</td>
			<td>$newsletter_status</td>
			<td><a href='newsletter/edit.php?nid=$newsletter_id'>Edit</a></td>
			<td>$sent</td>
			<td class="rowright"><a href="newsletter/send.php?test=yes&amp;nid=$newsletter_id">Send Test</a></td>
		</tr>
EOD;

		return $content_row;

	}

}

?>