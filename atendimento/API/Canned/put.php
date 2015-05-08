<?php
	if ( ISSET( $_OFFICE_PUT_ServiceCanned_LOADED ) == true )
		return ;

	$_OFFICE_PUT_ServiceCanned_LOADED = true ;

	/*****  ServiceCanned_put_UserCanned  *************************
	 *
	 *  History:
	 *	Nate Lee				Nov 12, 2001
	 *
	 *****************************************************************/
	FUNCTION ServiceCanned_put_UserCanned( &$dbh,
				$userid,
				$deptid,
				$type,
				$name,
				$message )
	{
		if ( ( $userid == "" ) || ( $type == "" )
			|| ( $name == "" ) || ( $message == "" )
			|| ( $deptid == "" ) )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$deptid = database_mysql_quote( $deptid ) ;
		$type = database_mysql_quote( $type ) ;
		$name = database_mysql_quote( $name ) ;
		$message = database_mysql_quote( $message ) ;
		
		$query = "INSERT INTO chatcanned VALUES( 0, $userid, '$deptid', '$type', '$name', '$message' )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$id = database_mysql_insertid ( $dbh ) ;
			return $id ;
		}

		return false ;
	}

?>