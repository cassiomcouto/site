<?php
	if ( ISSET( $_OFFICE_remove_ServiceFootprint_LOADED ) == true )
		return ;

	$_OFFICE_remove_ServiceFootprint_LOADED = true ;

	/*****  ServiceFootprint_remove_OldFootprints  *********************
	 *
	 *  History:
	 *	Holger				May 31, 2002
	 *
	 *****************************************************************/
	FUNCTION ServiceFootprint_remove_OldFootprints( &$dbh )
	{
		// keep footprints for 10 days
		$expireday = time() - (60*60*24*10) ;

		$query = "DELETE FROM chatfootprints WHERE created < $expireday" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

?>