<?php
	if ( ISSET( $_OFFICE_REMOVE_ServiceLogs_LOADED ) == true )
		return ;

	$_OFFICE_REMOVE_ServiceLogs_LOADED = true ;

	include_once( "$DOCUMENT_ROOT/API/Users/get.php" ) ;
	FUNCTION ServiceLogs_remove_ChatRequest( &$dbh,
						$requestid )
	{
		if ( $requestid == "" )
		{
			return false ;
		}
		$requestid = database_mysql_quote( $requestid ) ;

		$query = "DELETE FROM chatrequests WHERE requestID = $requestid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return true ;
	}

	/*****  ServiceLogs_remove_DeptExpireTranscripts  *****************
	 *
	 *  History:
	 *	Kyle Hicks				Dec 26, 2001
	 *
	 *****************************************************************/
	FUNCTION ServiceLogs_remove_DeptExpireTranscripts( &$dbh,
						$deptid,
						$aspid )
	{
		if ( ( $deptid == "" ) || ( $aspid == "" ) )
		{
			return false ;
		}
		$deptid = database_mysql_quote( $deptid ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		$now = time() ;
		$deptinfo = AdminUsers_get_DeptInfo( $dbh, $deptid, $aspid ) ;

		if ( $deptinfo['transcript_expire'] > 0 )
		{
			$expire_time = $now - $deptinfo['transcript_expire'] ;
			$query = "DELETE FROM chattranscripts WHERE created < $expire_time AND deptID = $deptid" ;
			database_mysql_query( $dbh, $query ) ;
		}

		// other cleaning of DB of old file
		// for chatkbratings, it doesn't matter who it is owned by, just
		// remove it.
		$expired = time() - (60*60*24*60) ; // 90 days or 3 months
		$query = "DELETE FROM chatkbratings WHERE created < $expired" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}

?>
