<?php
	if ( ISSET( $_OFFICE_REMOVE_ServiceRefer_LOADED ) == true )
		return ;

	$_OFFICE_REMOVE_ServiceRefer_LOADED = true ;
	FUNCTION ServiceRefer_remove_OldRefer( &$dbh,
						$aspid )
	{
		$aspid = database_mysql_quote( $aspid ) ;
		$expired = time() - (60*60*24*10) ;

		$query = "DELETE FROM chatrefer WHERE created < $expired" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return true ;
	}

?>