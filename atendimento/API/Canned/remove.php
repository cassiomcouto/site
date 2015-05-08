<?php
	if ( ISSET( $_OFFICE_REMOVE_ServiceCanned_LOADED ) == true )
		return ;

	$_OFFICE_REMOVE_ServiceCanned_LOADED = true ;
	FUNCTION ServiceCanned_remove_UserCanned( &$dbh,
						$userid,
						$cannedid )
	{
		if ( ( $userid == "" ) || ( $cannedid == "" ) )
		{
			return false ;
		}

		$query = "DELETE FROM chatcanned WHERE userID = $userid AND cannedID = $cannedid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}

		return false ;
	}

?>
