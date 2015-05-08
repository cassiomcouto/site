<?php
	if ( ISSET( $_OFFICE_GET_ServiceFootprintUnique_LOADED ) == true )
		return ;

	$_OFFICE_GET_ServiceFootprintUnique_LOADED = true ;
	FUNCTION ServiceFootprintUnique_get_ActiveFootprints( &$dbh,
								$aspid,
								$dept_string )
	{
		if ( ( $aspid == "" ) || ( $dept_string == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$dept_string = database_mysql_quote( $dept_string ) ;
		global $FOOTPRINT_IDLE ;
		$idle = time() - $FOOTPRINT_IDLE ;
		$footprints = ARRAY() ;

		$query = "SELECT * FROM chatfootprintsunique WHERE updated > $idle AND aspID = $aspid ORDER BY created ASC" ;
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

	/*****  ServiceFootprintUnique_get_TotalActiveFootprints  *********************
	 *
	 *  History:
	 *	Holger					Feb 26, 2002
	 *
	 *****************************************************************/
	FUNCTION ServiceFootprintUnique_get_TotalActiveFootprints( &$dbh,
								$aspid,
								$dept_string )
	{
		if ( ( $aspid == "" ) || ( $dept_string == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$dept_string = database_mysql_quote( $dept_string ) ;
		global $FOOTPRINT_IDLE ;
		$idle = time() - $FOOTPRINT_IDLE ;

		$query = "SELECT count(*) AS total FROM chatfootprintsunique WHERE updated > $idle AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data['total'] ;
		}
		return false ;
	}

	/*****  ServiceFootprintUnique_get_IPFootprintInfo  *********************
	 *
	 *  History:
	 *	Holger					Feb 26, 2002
	 *
	 *****************************************************************/
	FUNCTION ServiceFootprintUnique_get_IPFootprintInfo( &$dbh,
								$ip,
								$aspid )
	{
		if ( ( $ip == "" ) || ( $aspid == "" ) )
		{
			return false ;
		}
		$ip = database_mysql_quote( $ip ) ;
		$aspid = database_mysql_quote( $aspid ) ;

		$query = "SELECT * FROM chatfootprintsunique WHERE ip = '$ip' AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

?>