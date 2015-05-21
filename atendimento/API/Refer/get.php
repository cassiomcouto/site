<?php
	if ( ISSET( $_OFFICE_GET_ServiceRefer_LOADED ) == true )
		return ;

	$_OFFICE_GET_ServiceRefer_LOADED = true ;

	/*****  ServiceRefer_get_ReferInfo  *********************
	 *
	 *  History:
	 *	Holger					July 17, 2002
	 *
	 *****************************************************************/
	FUNCTION ServiceRefer_get_ReferInfo( &$dbh,
								$aspid,
								$ip )
	{
		if ( ( $aspid == "" ) || ( $ip == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$ip = database_mysql_quote( $ip ) ;

		$query = "SELECT * FROM chatrefer WHERE aspID = '$aspid' AND ip = '$ip'" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	/*****  ServiceRefer_get_ReferOnDate  *********************
	 *
	 *  History:
	 *	Holger					July 20, 2002
	 *
	 *****************************************************************/
	FUNCTION ServiceRefer_get_ReferOnDate( &$dbh,
								$aspid,
								$start,
								$end )
	{
		if ( ( $aspid == "" ) || ( $start == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$start = database_mysql_quote( $start ) ;
		$end = database_mysql_quote( $end ) ;
		$refer = ARRAY() ;

		$query = "SELECT DISTINCT(refer_url) AS refer_url, count(*) AS total FROM chatrefer WHERE aspID = $aspid AND created >= $start AND created < $end AND refer_url <> '' GROUP BY refer_url ORDER BY total DESC LIMIT 500" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			while ( $data = database_mysql_fetchrow( $dbh ) )
				$refer[] = $data ;
			return $refer ;
		}
		return false ;
	}

?>