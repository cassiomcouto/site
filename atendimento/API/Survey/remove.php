<?php
	if ( ISSET( $_OFFICE_remove_ServiceSurvey_LOADED ) == true )
		return ;

	$_OFFICE_remove_ServiceSurvey_LOADED = true ;
	FUNCTION ServiceSurvey_remove_OldFootprints( &$dbh,
								$aspid,
								$expireday )
	{
		if ( ( $aspid == "" ) || ( $expireday == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$expireday = database_mysql_quote( $expireday ) ;

		$query = "DELETE FROM chatfootprints WHERE aspID = $aspid AND created < $expireday" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

?>