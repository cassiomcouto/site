<?php
	if ( ISSET( $_OFFICE_REMOVE_ServiceTranscripts_LOADED ) == true )
		return ;

	$_OFFICE_REMOVE_ServiceTranscripts_LOADED = true ;
	FUNCTION ServiceTranscripts_remove_Transcript( &$dbh,
						$aspid,
						$sessionid )
	{
		if ( ( $aspid == "" ) || ( $sessionid == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$sessionid = database_mysql_quote( $sessionid ) ;

		$query = "DELETE FROM chattranscripts WHERE chat_session = $sessionid AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return true ;
	}

?>
