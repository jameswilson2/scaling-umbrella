<?php
require_once 'library/_table.class.php';

// class to produce a list of comments to a given set of filters/sorts

class CommentTable extends Table{

	function CommentTable(){
		parent::Table();
	}


	function setQuery(){
		$this->sql = "SELECT comment_id, comment_name, comment_text, comment_datetime, comment_status
					FROM tbl_comments WHERE comment_id".$this->sql_filter.$this->sql_sort;
	}


	function getOrder(){
		$order = $_GET['order'];
		switch ($order){
			case 'datedesc':
				$this->sql_sort = " ORDER BY comment_datetime DESC";
				$this->query_sort = "order=datedesc";
				$this->form_sort = "datedesc";
				break;

			case 'dateasc':
				$this->sql_sort = " ORDER BY comment_datetime ASC";
				$this->query_sort = "order=dateasc";
				$this->form_sort = "dateasc";
				break;

			case 'nameasc':
				$this->sql_sort = " ORDER BY comment_name ASC";
				$this->query_sort = "order=nameasc";
				$this->form_sort = "nameasc";
				break;

			case 'namedesc':
				$this->sql_sort = " ORDER BY comment_name DESC";
				$this->query_sort = "order=namedesc";
				$this->form_sort = "namedesc";
				break;

			case 'textasc':
				$this->sql_sort = " ORDER BY comment_text ASC";
				$this->query_sort = "order=textasc";
				$this->form_sort = "textasc";
				break;

			case 'textdesc':
				$this->sql_sort = " ORDER BY comment_text DESC";
				$this->query_sort = "order=textdesc";
				$this->form_sort = "textdesc";
				break;

			default:
				$this->sql_sort = " ORDER BY comment_datetime DESC";
				$this->query_sort = "order=datedesc";
				$this->form_sort = "datedesc";
				break;

		}
	}


	function getFilters(){

		$this->form_filters = "";

		// filter by status
		if (isset($_GET['comment_status']) && $_GET['comment_status'] != ""){
			$this->setFilter('comment_status', $_GET['comment_status']);
		}

		$options = array('pending'=>'Pending','approved'=>'Approved', 'rejected'=>'Rejected');
		foreach($options AS $key=>$option){
			if ($key == $_GET['comment_status']){
				$options_list = "<option value=\"$key\" selected=\"selected\">$option</option>";
			} else {
				$options_list = "<option value=\"$key\">$option</option>";
			}
			$form .= $options_list;
		}
		$this->form_filters .= <<<EOD
		<select name="comment_status">
		<option value=''>Any Status</option>
		$form
		</select>
EOD;

	}


	function getTableHeader(){

		$query_string = implode('&amp;',$this->query_filter);
		if ($this->form_sort == 'textasc'){
			$link_comment = $this->self.'?'.$query_string.'&amp;order=textdesc';
		} else {
			$link_comment = $this->self.'?'.$query_string.'&amp;order=textasc';
		}

		if ($this->form_sort == 'nameasc'){
			$link_name = $this->self.'?'.$query_string.'&amp;order=namedesc';
		} else {
			$link_name = $this->self.'?'.$query_string.'&amp;order=nameasc';
		}

		if ($this->form_sort == 'datedesc'){
			$link_date = $this->self.'?'.$query_string.'&amp;order=dateasc';
		} else {
			$link_date = $this->self.'?'.$query_string.'&amp;order=datedesc';
		}



		$this->header = <<<EOD
		<table width="100%" border="0" cellpadding="3" cellspacing="0" class="table">
			<tr class="rowstrong">
				<td><a href="$link_comment">Comment</a></td>
				<td><a href="$link_name">Name</a></td>
				<td><a href="$link_date">Date</a></td>
				<td>Status</td>
			</tr>
EOD;

	}


	function renderRow($row){
		$comment_id = $row['comment_id'];
		$comment_name = htmlspecialchars($row['comment_name']);
		$comment_text = htmlspecialchars($row['comment_text']);
		$comment_datetime = $row['comment_datetime'];
		$comment_status = $row['comment_status'];
		$comment_status_ucfirst = ucfirst($comment_status);

		$comment_text = ereg_replace("\r\n", "\n", $comment_text);
		$comment_text = ereg_replace("\r", "\n", $comment_text);
		$comment_text = ereg_replace("\n\n", '</p><p>', $comment_text);
		$comment_text = ereg_replace("\n", '<br />', $comment_text);

		$comment_datetime = date('l dS \of F Y \a\t h:ia' ,strtotime($comment_datetime));

		switch ($comment_status){
			case 'pending':
				$status = "<a href=\"comments/moderate.php?cid=$comment_id&amp;action=approve\" onclick=\"return updateElm('comments/moderate_ajax.php?cid=$comment_id&amp;action=approve', 'comment_status_$comment_id');\"><img src=\"presentation/approve_pending.gif\" width=\"27\" height=\"14\" alt=\"Pending Approval\" /></a>&nbsp;<img src=\"presentation/status_or.gif\" width=\"7\" height=\"14\" alt=\"\" />&nbsp;<a href=\"comments/moderate.php?cid=$comment_id&amp;action=reject\" onclick=\"return updateElm('comments/moderate_ajax.php?cid=$comment_id&amp;action=reject', 'comment_status_$comment_id');\"><img src=\"presentation/reject_pending.gif\" width=\"27\" height=\"14\" alt=\"Pending Rejection\" /></a>";
			break;

			case 'approved':
				$status = "<img src=\"presentation/approve_status.gif\" width=\"27\" height=\"14\" alt=\"Approved\" />&nbsp;<img src=\"presentation/status_or.gif\" width=\"7\" height=\"14\" alt=\"\" />&nbsp;<a href=\"comments/moderate.php?cid=$comment_id&amp;action=reject\" onclick=\"return updateElm('comments/moderate_ajax.php?cid=$comment_id&amp;action=reject', 'comment_status_$comment_id');\"><img src=\"presentation/reject_status.gif\" width=\"27\" height=\"14\" alt=\"Reject\" /></a>";
			break;

			case 'rejected':
				$status = "<img src=\"presentation/reject_status.gif\" width=\"27\" height=\"14\" alt=\"Rejected\" />&nbsp;<img src=\"presentation/status_or.gif\" width=\"7\" height=\"14\" alt=\"\" />&nbsp;<a href=\"comments/moderate.php?cid=$comment_id&amp;action=approve\" onclick=\"return updateElm('comments/moderate_ajax.php?cid=$comment_id&amp;action=approve', 'comment_status_$comment_id');\"><img src=\"presentation/approve_status.gif\" width=\"27\" height=\"14\" alt=\"Approve\" /></a>";
			break;
		}

		$content_row = <<<EOD
		<tr class="row">
			<td><p>$comment_text</p></td>
			<td>$comment_name</td>
			<td width="160">$comment_datetime</td>
			<td id="comment_status_$comment_id" nowrap="nowrap">$status</td>
		</tr>
EOD;

		return $content_row;

	}

}

?>