<?php
	if ( ISSET( $_OFFICE_update_ServiceCanned_LOADED ) == true )
		return ;

	$_OFFICE_update_ServiceCanned_LOADED = true ;
	FUNCTION ServiceCanned_update_Canned( &$dbh,
						$userid,
						$cannedid,
						$deptid,
						$name,
						$message )
	{
		if ( ( $userid == "" ) || ( $cannedid == "" )
			|| ( $name == "" ) || ( $message == "" ) )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$cannedid = database_mysql_quote( $cannedid ) ;
		$deptid = database_mysql_quote( $deptid ) ;
		$name = database_mysql_quote( $name ) ;
		$message = database_mysql_quote( $message ) ;

		$query = "UPDATE chatcanned SET deptID = '$deptid', name = '$name', message = '$message' WHERE cannedID = $cannedid AND userID = $userid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

?>
