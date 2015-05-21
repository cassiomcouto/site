<?php
	if ( ISSET( $_OFFICE_GET_ServiceFootprint_LOADED ) == true )
		return ;
	$_OFFICE_GET_ServiceFootprint_LOADED = true ;
	include_once( "$DOCUMENT_ROOT/API/Users/get.php" ) ;
	FUNCTION ServiceFootprint_get_DayFootprint( &$dbh,
								$ip,
								$begin,
								$end,
								$limit,
								$aspid,
								$day,
								$optimize )
	{
		if ( ( $begin == "" ) || ( $end == "" ) 
			|| ( $aspid == "" ) )
		{
			return false ;
		}
		$ip = database_mysql_quote( $ip ) ;
		$begin = database_mysql_quote( $begin ) ;
		$end = database_mysql_quote( $end ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		$limit = database_mysql_quote( $limit ) ;
		$day = database_mysql_quote( $day ) ;
		$optimize = database_mysql_quote( $optimize ) ;
		// check to see if checking today's stat or old stats (old stats are logged into
		// different table for compression and speed)
		$now = mktime( 0,0,0,date("m"),date("j"),date("Y") ) ;

		$ip_string = $limit_string = "" ;
		$footprints = ARRAY() ;

		if ( $ip )
			$ip_string = "ip = '$ip' AND" ;
		if ( $limit )
			$limit_string = "LIMIT $limit" ;

		if ( ( $begin < $now ) && !$ip && !$optimize )
		{
			// if $day is passed, then we want to get stat for JUST that date... else
			// we want to get all the stat for that month.
			if ( $day )
				$query = "SELECT url, clicks AS total FROM chatfootprinturlstats WHERE statdate = $begin AND aspID = $aspid ORDER BY clicks DESC $limit_string" ;
			else
				$query = "SELECT url, SUM(clicks) AS total FROM chatfootprinturlstats WHERE statdate >= $begin AND statdate <= $end AND aspID = $aspid GROUP BY url ORDER BY total DESC $limit_string" ;
		}
		else
			$query = "SELECT url, count(*) AS total FROM chatfootprints WHERE $ip_string created > $begin AND created <= $end AND aspID = $aspid GROUP BY url ORDER BY total DESC $limit_string" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			while ( $data = database_mysql_fetchrow( $dbh ) )
			{
				$footprints[] = $data ;
			}
			return $footprints ;
		}
		return false ;
	}

	/*****  ServiceFootprint_get_TotalDayFootprint  *********************
	 *
	 *  History:
	 *	Nate Lee				Dec 27, 2001
	 *		modify by Holger	May 31, 2002
	 *
	 *****************************************************************/
	FUNCTION ServiceFootprint_get_TotalDayFootprint( &$dbh,
								$begin,
								$end,
								$aspid ,
								$optimize )
	{
		if ( ( $begin == "" ) || ( $end == "" ) 
			|| ( $aspid == "" ) )
		{
			return false ;
		}
		$begin = database_mysql_quote( $begin ) ;
		$end = database_mysql_quote( $end ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		$optimize = database_mysql_quote( $optimize ) ;
		// check to see if checking today's stat or old stats (old stats are logged into
		// different table for compression and speed)
		$now = mktime( 0,0,0,date("m"),date("j"),date("Y") ) ;

		if ( ( $begin < $now ) && !$optimize )
			$query = "SELECT pageviews AS total FROM chatfootprintstats WHERE statdate = $begin AND aspID = $aspid" ;
		else
			$query = "SELECT count(*) AS total FROM chatfootprints WHERE created > $begin AND created <= $end AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data['total'] ;
		}
		return false ;
	}

	/*****  ServiceFootprint_get_TotalUniqueDayVisits  *********************
	 *
	 *  History:
	 *	Nate Lee				Dec 27, 2001
	 *		modify by Holger	May 31, 2002
	 *
	 *****************************************************************/
	FUNCTION ServiceFootprint_get_TotalUniqueDayVisits( &$dbh,
								$begin,
								$end,
								$aspid,
								$optimize )
	{
		if ( ( $begin == "" ) || ( $end == "" )
			|| ( $aspid == "" ) )
		{
			return false ;
		}
		$begin = database_mysql_quote( $begin ) ;
		$end = database_mysql_quote( $end ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		$optimize = database_mysql_quote( $optimize ) ;
		// check to see if checking today's stat or old stats (old stats are logged into
		// different table for compression and speed)
		$now = mktime( 0,0,0,date("m"),date("j"),date("Y") ) ;

		if ( ( $begin < $now ) && !$optimize )
			$query = "SELECT uniquevisits AS total FROM chatfootprintstats WHERE statdate = $begin AND aspID = $aspid" ;
		else
			$query = "SELECT count(DISTINCT(ip)) AS total FROM chatfootprints WHERE created > $begin AND created <= $end AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data['total'] ;
		}
		return false ;
	}

	/*****  ServiceFootprint_get_TotalUniqueURLDayVisits  *********************
	 *
	 *  History:
	 *	Kyle Hicks				Feb 26, 2002
	 *
	 *****************************************************************/
	FUNCTION ServiceFootprint_get_TotalUniqueURLDayVisits( &$dbh,
								$begin,
								$end,
								$aspid,
								$url )
	{
		if ( ( $begin == "" ) || ( $end == "" )
			|| ( $aspid == "" ) || ( $url == "" ) )
		{
			return false ;
		}
		$begin = database_mysql_quote( $begin ) ;
		$end = database_mysql_quote( $end ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		$url = database_mysql_quote( $url ) ;
		$query = "SELECT count(DISTINCT(ip)) AS total FROM chatfootprints WHERE created > $begin AND created <= $end AND aspID = $aspid AND url = '$url'" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data['total'] ;
		}
		return false ;
	}

	/*****  ServiceFootprint_get_BeforeDayFootprint  *********************
	 *
	 *  History:
	 *	Nate Lee				Dec 27, 2001
	 *
	 *****************************************************************/
	FUNCTION ServiceFootprint_get_BeforeDayFootprint( &$dbh,
								$ip,
								$begin,
								$limit,
								$aspid )
	{
		if ( ( $begin == "" ) || ( $aspid == "" ) )
		{
			return false ;
		}
		$ip = database_mysql_quote( $ip ) ;
		$begin = database_mysql_quote( $begin ) ;
		$limit = database_mysql_quote( $limit ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		$limit_string = "" ;
		$footprints = ARRAY() ;

		if ( $limit )
			$limit_string = "LIMIT $limit" ;

		$query = "SELECT url, count(*) AS total FROM chatfootprints WHERE ip = '$ip' AND created < $begin AND aspID = $aspid GROUP BY url ORDER BY total DESC $limit_string" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			while ( $data = database_mysql_fetchrow( $dbh ) )
			{
				$footprints[] = $data ;
			}
			return $footprints ;
		}
		return false ;
	}

	/*****  ServiceFootprint_get_TotalFootprints  *********************
	 *
	 *  History:
	 *	Kyle				May 2, 2002
	 *
	 *****************************************************************/
	FUNCTION ServiceFootprint_get_TotalFootprints( &$dbh,
								$aspid )
	{
		if ( $aspid == "" )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;

		$query = "SELECT count(*) AS total FROM chatfootprints WHERE aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data['total'] ;
		}
		return false ;
	}

	/*****  ServiceFootprint_get_LatestFootprintStatDate  *****************
	 *
	 *  History:
	 *	Kyle				May 2, 2002
	 *
	 *****************************************************************/
	FUNCTION ServiceFootprint_get_LatestFootprintStatDate( &$dbh,
								$aspid )
	{
		if ( $aspid == "" )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;

		$query = "SELECT MAX(statdate) AS statdate FROM chatfootprintstats WHERE aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			if ( !isset( $data['statdate'] ) )
			{
				$query = "SELECT MIN(created) AS statdate FROM chatfootprints WHERE aspID = $aspid" ;
				database_mysql_query( $dbh, $query ) ;

				$data = database_mysql_fetchrow( $dbh ) ;
				return $data['statdate'] ;
			}
			else
			{
				// add another day so it knows to stop during auto optimize
				$nextday = $data['statdate'] + (60*60*24) ;
				return $nextday ;
			}
		}
		return false ;
	}

	/*****  ServiceFootprint_get_SPFootprint  *********************
	 *
	 *  History:
	 *	Kyle Hicks				Sept 30, 2003
	 *
	 *****************************************************************/
	FUNCTION ServiceFootprint_get_SPFootprint( &$dbh,
								$aspid,
								$ip )
	{
		if ( ( $aspid == "" ) || ( $ip == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$ip = database_mysql_quote( $ip ) ;
		$footprints = ARRAY() ;

		$query = "SELECT * FROM chatfootprints WHERE ip = '$ip' AND aspID = $aspid ORDER BY created DESC" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			while ( $data = database_mysql_fetchrow( $dbh ) )
			{
				$footprints[] = $data ;
			}
			return $footprints ;
		}
		return false ;
	}
?>