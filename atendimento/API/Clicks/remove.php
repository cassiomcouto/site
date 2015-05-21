<?php
	if ( ISSET( $_OFFICE_remove_ServiceClicks_LOADED ) == true )
		return ;

	$_OFFICE_remove_ServiceClicks_LOADED = true ;

	/*****  ServiceClicks_remove_TrackingURL  *********************
	 *
	 *  History:
	 *	Holger				Feb 21, 2003
	 *
	 *****************************************************************/
	FUNCTION ServiceClicks_remove_TrackingURL( &$dbh,
								$aspid,
								$trackid )
	{
		if ( ( $aspid == "" ) || ( $trackid == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$trackid = database_mysql_quote( $trackid ) ;

		$query = "DELETE FROM chatclicktracking WHERE aspID = $aspid AND trackID = $trackid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM chatclicks WHERE aspID = $aspid AND trackID = $trackid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

?>