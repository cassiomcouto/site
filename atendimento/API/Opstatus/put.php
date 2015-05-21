<?php
	if ( ISSET( $_OFFICE_PUT_OpStatus_LOADED ) == true )
		return ;

	$_OFFICE_PUT_OpStatus_LOADED = true ;

	/*****  OpStatus_put_Status  *******************************
	 *
	 *  History:
	 *	Kyle Hicks				Oct 15, 2003
	 *
	 *****************************************************************/
	FUNCTION OpStatus_put_Status( &$dbh,
					$userid,
					$status )
	{
		if ( $userid == "" )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$status = database_mysql_quote( $status ) ;
		$now = time() ;

		$query = "INSERT INTO chat_adminstatus VALUES( 0, $userid, $now, $status )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

?>