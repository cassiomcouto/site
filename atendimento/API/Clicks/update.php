<?php
	if ( ISSET( $_OFFICE_update_ServiceClicks_LOADED ) == true )
		return ;

	$_OFFICE_update_ServiceClicks_LOADED = true ;

	/*****  ServiceClicks_update_TrackingURL  *********************
	 *
	 *  History:
	 *	Holger				Feb 21, 2003
	 *
	 *****************************************************************/
	FUNCTION ServiceClicks_update_TrackingURL( &$dbh,
								$aspid,
								$trackid,
								$name,
								$landing_url,
								$color )
	{
		if ( ( $aspid == "" ) || ( $trackid == "" )
			|| ( $name == "" ) || ( $landing_url == "" )
			|| ( $color == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$trackid = database_mysql_quote( $trackid ) ;
		$name = database_mysql_quote( $name ) ;
		$landing_url = database_mysql_quote( $landing_url ) ;
		$color = database_mysql_quote( $color ) ;

		$query = "UPDATE chatclicktracking SET name = '$name', landing_url = '$landing_url', color = '$color' WHERE aspID = $aspid AND trackID = $trackid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

?>