<?php
	if ( ISSET( $_OFFICE_UPDATE_ServiceLogs_LOADED ) == true )
		return ;

	$_OFFICE_UPDATE_ServiceLogs_LOADED = true ;
	FUNCTION ServiceLogs_update_ChatRequestStatus( &$dbh,
					  $requestid,
					  $status )
	{
		if ( $requestid == "" )
		{
				return false ;
		}
		$requestid = database_mysql_quote( $requestid ) ;
		$status = database_mysql_quote( $status ) ;

		$query = "UPDATE chatrequests SET status = '$status' WHERE requestID = $requestid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

?>
