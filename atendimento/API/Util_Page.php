<?php
	if ( ISSET( $_OFFICE_UTIL_Page_LOADED ) == true )
		return ;
	$_OFFICE_UTIL_Page_LOADED = true ;
	FUNCTION Page_util_CreatePageString( &$dbh,
						$current_page,
						$url,
						$page_per,
						$total_contacts )
	{
		if ( ( $page_per == "" ) || ( $total_contacts == "" ) )
		{
				return false ;
		}

		if ( !$current_page )
		{
			$current_page = 1 ;
		}

		$page_buffer = 5 ;	// num pages show before current
		$page_max = 20 ;	// max pages to show
		$page_string = "" ;
		$pages = floor( $total_contacts/$page_per ) ;
		$remainder = ( $total_contacts % $page_per ) ;

		if ( $remainder > 0 )
		{
			$pages += 1 ;
		}

		// figure out which page begin at
		$difference = $current_page - $page_buffer ;
		if ( $difference >= 1 )
		{
			$page_start = $difference ;
		}
		else
		{
			$page_start = 1 ;
		}

		// figure out which page to end at
		$buffer_end = ( $current_page + ( $page_max - $page_buffer ) ) ;
		if ( $buffer_end <= $pages )
		{
			if ( $buffer_end < $page_max )
			{
				$page_end = $page_start + $page_max ;
			}
			else
			{
				$page_end = $buffer_end ;
			}
		}
		else
		{
			$page_end = $pages ;
		}

		// tack on first of page
		if ( $page_start > 1 )
		{
			$page_string = "<a href=\"$url&page=1\">first</a> - " ;
		}

		// brute code to fix page bug
		if ( $pages < $page_max )
		{
			$page_end = $pages ;
		}

		for ( $page = $page_start; $page <= $page_end ; ++$page )
		{
			if ( $page == $current_page )
			{
				$page_string .= "<b>$page</b> - " ;
			}
			else
			{
				$page_string .= "<a href=\"$url&page=$page\">$page</a> - " ;
			}
		}
	
		// tack on end of page
		if ( $page_end < $pages )
		{
			$page_string .= "<a href=\"$url&page=$pages\">last</a>" ;
		}
		else
		{
			$page_string = substr($page_string, 0, strlen($page_string) - 2); 
		}

		return $page_string ;
	}

?>
