<?php
	if ( ISSET( $_OFFICE_PUT_ServiceFootprint_LOADED ) == true )
		return ;

	$_OFFICE_PUT_ServiceFootprint_LOADED = true ;
	FUNCTION ServiceFootprint_put_Footprint( &$dbh,
					$ip,
					$url,
					$aspid )
	{
		if ( ( $ip == "" ) || ( $url == "" )
			|| ( $aspid == "" ) )
		{
			return false ;
		}
		$ip = database_mysql_quote( $ip ) ;
		$url = database_mysql_quote( $url ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		$now = time() ;

		$query = "INSERT INTO chatfootprints VALUES( 0, '$ip', $now, '$url', $aspid )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

	/*****  ServiceFootprint_put_FootprintURLStat  *******************************
	 *
	 *  History:
	 *	Holger				May 24, 2002
	 *
	 *****************************************************************/
	FUNCTION ServiceFootprint_put_FootprintURLStat( &$dbh,
					$aspid,
					$statdate,
					$url,
					$clicks )
	{
		if ( ( $aspid == "" ) || ( $statdate == "" )
			|| ( $url == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$statdate = database_mysql_quote( $statdate ) ;
		$url = database_mysql_quote( $url ) ;
		$clicks = database_mysql_quote( $clicks ) ;
		$now = time() ;

		$query = "SELECT * FROM chatfootprinturlstats WHERE aspID = $aspid AND statdate = $statdate AND url = '$url'" ;
		database_mysql_query( $dbh, $query ) ;
		$data = database_mysql_fetchrow( $dbh ) ;

		if ( !isset( $data['aspID'] ) )
		{
			$query = "INSERT INTO chatfootprinturlstats VALUES( 0, '$aspid', '$statdate', $now, '$url', '$clicks' )" ;
			database_mysql_query( $dbh, $query ) ;
		}

		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

	/*****  ServiceFootprint_put_FootprintStat  *******************************
	 *
	 *  History:
	 *	Holger				May 24, 2002
	 *
	 *****************************************************************/
	FUNCTION ServiceFootprint_put_FootprintStat( &$dbh,
					$aspid,
					$statdate,
					$pageviews,
					$uniquevisits )
	{
		if ( ( $aspid == "" ) || ( $statdate == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$statdate = database_mysql_quote( $statdate ) ;
		$pageviews = database_mysql_quote( $pageviews ) ;
		$uniquevisits = database_mysql_quote( $uniquevisits ) ;
		$now = time() ;

		$query = "INSERT INTO chatfootprintstats VALUES( '$aspid', '$statdate', $now, '$pageviews', '$uniquevisits' )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}
?>