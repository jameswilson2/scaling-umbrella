<?php

class Pager {
	// total pages in results
	var $total_pages;

	// number of links to show
	var $num_links = 10;

	// paged query - to provide
	var $paged_sql;

	// paging links
	var $paging_links;


	function Pager($sql, $rows_per_page, $self, $query_string, $current_page='1'){

		// write paged sql
		$offset = ($current_page - 1) * $rows_per_page;
		$this->paged_sql = $sql." LIMIT $offset, $rows_per_page";

		// calculate total number of results
		$result = getQuery($sql, $error_message='Could not get paging links: ');

		$total_results = mysql_num_rows($result);
		$this->total_pages = ceil($total_results / $rows_per_page);

		if ($this->total_pages > 1){

			// print 'previous' link only if we're not
			// on page one
			if ($current_page > 1) {
				$page = $current_page - 1;
				if ($page > 1) {
					$prev = " - <a href=\"$self?page=$page&amp;$query_string\">Previous</a> ";
				} else {
					$prev = " - <a href=\"$self?$query_string\">Previous</a> ";
				}
			} else {
				$prev  = ' - Previous '; // we're on page one, don't show 'previous' link
			}

			// print 'next' link only if we're not
			// on the last page
			if ($current_page < $this->total_pages) {
				$page = $current_page + 1;
				$next = " | <a href=\"$self?page=$page&amp;$query_string\">Next</a> ";
			} else {
				$next = ' | Next'; // we're on the last page, don't show 'next' link
			}

			$start = $current_page - 3;
			$start = max(1, $start);

			$end  = $start + $this->num_links - 1;
			$end  = min($this->total_pages, $end);

			$page_numbers = "<strong> - Page $current_page of $this->total_pages</strong>";

			$pagingLink = array();
			for($page = $start; $page <= $end; $page++)	{
				if ($page == $current_page) {
					$pagingLink[] = " <span class='paging_selected'><strong>$page</strong></span> ";   // no need to create a link to current page
				} else {
					if ($page == 1) {
						$pagingLink[] = " <span class='paging_page'><a href=\"$self?$query_string\">$page</a></span> ";
					} else {
						$pagingLink[] = " <span class='paging_page'><a href=\"$self?page=$page&amp;$query_string\">$page</a></span> ";
					}
				}

			}

			$pagingLink = implode(' ', $pagingLink);

			// return the page navigation link
			$this->paging_links = "<div class='paging_panel'>" . $pagingLink . $prev . $next . $page_numbers . "</div>";

		} else {
			$this->paging_links="";
		}

	}

	function getPagedQuery(){
		return $this->paged_sql;
	}

	function getPagingLinks(){
		return $this->paging_links;
	}

	function getTotalPages(){
		return $this->total_pages;
	}
}


class FriendlyPager {
	// total pages in results
	var $total_pages;

	// number of links to show
	var $num_links = 10;

	// paged query - to provide
	var $paged_sql;

	// paging links
	var $paging_links;


	function FriendlyPager($sql, $rows_per_page, $self, $query_string, $current_page='1'){

		// write paged sql
		$offset = ($current_page - 1) * $rows_per_page;
		$this->paged_sql = $sql." LIMIT $offset, $rows_per_page";

		// calculate total number of results
		$result = @mysql_query($sql);
		if (!$result){
			exit('Could not get paging links: '.mysql_error());
		}

		if ($query_string!=''){
			$query_string = "?".$query_string;
		}

		$total_results = mysql_num_rows($result);
		$this->total_pages = ceil($total_results / $rows_per_page);

		if ($this->total_pages > 1){

			// print 'previous' link only if we're not
			// on page one
			if ($current_page > 1) {
				$page = $current_page - 1;
				if ($page > 1) {
					$prev = " - <a href=\"$self$page/$query_string\">Previous</a> ";
				} else {
					$prev = " - <a href=\"$self?=/$query_string\">Previous</a> ";
				}
			} else {
				$prev  = ' - Previous '; // we're on page one, don't show 'previous' link
			}

			// print 'next' link only if we're not
			// on the last page
			if ($current_page < $this->total_pages) {
				$page = $current_page + 1;
				$next = " | <a href=\"$self$page/$query_string\">Next</a> ";
			} else {
				$next = ' | Next'; // we're on the last page, don't show 'next' link
			}

			$start = $current_page - 3;
			$start = max(1, $start);

			$end  = $start + $this->num_links - 1;
			$end  = min($this->total_pages, $end);

			$page_numbers = "<strong> - Page $current_page of $this->total_pages</strong>";

			$pagingLink = array();
			for($page = $start; $page <= $end; $page++)	{
				if ($page == $current_page) {
					$pagingLink[] = " <span class='paging_selected'><strong>$page</strong></span> ";   // no need to create a link to current page
				} else {
					if ($page == 1) {
						$pagingLink[] = " <span class='paging_page'><a href=\"$self/$query_string\">$page</a></span> ";
					} else {
						$pagingLink[] = " <span class='paging_page'><a href=\"$self$page/$query_string\">$page</a></span> ";
					}
				}

			}

			$pagingLink = implode(' ', $pagingLink);

			// return the page navigation link
			$this->paging_links = "<div class='paging_panel'>" . $pagingLink . $prev . $next . $page_numbers . "</div>";

		} else {
			$this->paging_links="";
		}

	}

	function getPagedQuery(){
		return $this->paged_sql;
	}

	function getPagingLinks(){
		return $this->paging_links;
	}

	function getTotalPages(){
		return $this->total_pages;
	}
}


class PublicPager {
	// total pages in results
	var $total_pages;

	// number of links to show
	var $num_links = 10;

	// paged query - to provide
	var $paged_sql;

	// paging links
	var $paging_links;


	function PublicPager($sql, $rows_per_page, $self, $query_string, $current_page='1'){

		// write paged sql
		$offset = ($current_page - 1) * $rows_per_page;
		$this->paged_sql = $sql." LIMIT $offset, $rows_per_page";

		// calculate total number of results
		$result = getPublicQuery($sql, $error_message='Could not get paging links: ');

		$total_results = mysql_num_rows($result);
		$this->total_pages = ceil($total_results / $rows_per_page);

		if ($this->total_pages > 1){

			// print 'previous' link only if we're not
			// on page one
			if ($current_page > 1) {
				$page = $current_page - 1;
				if ($page > 1) {
					$prev = " - <a href=\"$self?page=$page&amp;$query_string\">Previous</a> ";
				} else {
					$prev = " - <a href=\"$self?$query_string\">Previous</a> ";
				}
			} else {
				$prev  = ' - Previous '; // we're on page one, don't show 'previous' link
			}

			// print 'next' link only if we're not
			// on the last page
			if ($current_page < $this->total_pages) {
				$page = $current_page + 1;
				$next = " | <a href=\"$self?page=$page&amp;$query_string\">Next</a> ";
			} else {
				$next = ' | Next'; // we're on the last page, don't show 'next' link
			}

			$start = $current_page - 3;
			$start = max(1, $start);

			$end  = $start + $this->num_links - 1;
			$end  = min($this->total_pages, $end);

			$page_numbers = "<strong> - Page $current_page of $this->total_pages</strong>";

			$pagingLink = array();
			for($page = $start; $page <= $end; $page++)	{
				if ($page == $current_page) {
					$pagingLink[] = " <span class='paging_selected'><strong>$page</strong></span> ";   // no need to create a link to current page
				} else {
					if ($page == 1) {
						$pagingLink[] = " <span class='paging_page'><a href=\"$self?$query_string\">$page</a></span> ";
					} else {
						$pagingLink[] = " <span class='paging_page'><a href=\"$self?page=$page&amp;$query_string\">$page</a></span> ";
					}
				}

			}

			$pagingLink = implode(' ', $pagingLink);

			// return the page navigation link
			$this->paging_links = "<div class='paging_panel'>" . $pagingLink . $prev . $next . $page_numbers . "</div>";

		} else {
			$this->paging_links="";
		}

	}

	function getPagedQuery(){
		return $this->paged_sql;
	}

	function getPagingLinks(){
		return $this->paging_links;
	}

	function getTotalPages(){
		return $this->total_pages;
	}
}
?>