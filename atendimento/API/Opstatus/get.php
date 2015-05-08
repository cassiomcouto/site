<?php
	if ( ISSET( $_OFFICE_GET_OpStatus_LOADED ) == true )
		return ;

	$_OFFICE_GET_OpStatus_LOADED = true ;

	/*****  OpStatus_get_UserStatusLogs  *********************
	 *
	 *  History:
	 *	Kyle Hicks				Oct 15, 2003
	 *
	 *****************************************************************/
	FUNCTION OpStatus_get_UserStatusLogs( &$dbh,
								$userid,
								$aspid,
								$begin,
								$end )
	{
		if ( ( $userid == "" ) || ( $aspid == "" ) )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		$begin = database_mysql_quote( $begin ) ;
		$end = database_mysql_quote( $end ) ;
		$logs = Array() ;

		$query = "SELECT chat_adminstatus.*, chat_admin.login AS login, chat_admin.name AS name FROM chat_adminstatus, chat_admin WHERE chat_admin.userID = chat_adminstatus.userID AND chat_adminstatus.userID = $userid AND chat_admin.aspID = $aspid AND chat_adminstatus.created > $begin AND chat_adminstatus.created <= $end ORDER BY chat_adminstatus.created ASC" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
				$logs[] = $data ;
			return $logs ;
		}
		return false ;
	}

?>