<?php
	if ( ISSET( $_OFFICE_UPDATE_AdminUsers_LOADED ) == true )
		return ;

	$_OFFICE_UPDATE_AdminUsers_LOADED = true ;

	include_once( "$DOCUMENT_ROOT/API/Opstatus/put.php" ) ;
	FUNCTION AdminUsers_update_Status( &$dbh,
					  $userid,
					  $status )
	{
		if ( $userid == "" )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$status = database_mysql_quote( $status ) ;

		$query = "UPDATE chat_admin SET available_status = $status WHERE userID = $userid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			OpStatus_put_Status( $dbh, $userid, $status ) ;
			return true ;
		}
		return false ;
	}

	/*****  AdminUsers_update_Signal  *********************
	 *
	 *  History:
	 *	Kyle Hicks				Jan 20, 2002
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_update_Signal( &$dbh,
					  $userid,
					  $signal,
					  $aspid )
	{
		if ( ( $userid == "" ) || ( $aspid == "" ) )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$signal = database_mysql_quote( $signal ) ;
		$aspid = database_mysql_quote( $aspid ) ;

		$query = "UPDATE chat_admin SET signal = $signal WHERE userID = $userid AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

	/*****  AdminUsers_update_LastActiveTime  *********************
	 *
	 *  History:
	 *	Nate Lee				Nov 3, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_update_LastActiveTime( &$dbh,
					  $userid,
					  $time,
					  $sid )
	{
		if ( ( $userid == "" ) || ( $sid == "" ) )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$time = database_mysql_quote( $time ) ;
		$sid = database_mysql_quote( $sid ) ;

		$query = "UPDATE chat_admin SET last_active_time = $time, session_sid = '$sid' WHERE userID = $userid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

	/*****  AdminUsers_update_UserValue  *********************
	 *
	 *  History:
	 *	Nate Lee				Dec 12, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_update_UserValue( &$dbh,
					  $userid,
					  $tbl_name,
					  $value )
	{
		if ( ( $userid == "" ) || ( $tbl_name == "" ) )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$tbl_name = database_mysql_quote( $tbl_name ) ;
		$value = database_mysql_quote( $value ) ;

		$query = "UPDATE chat_admin SET $tbl_name = '$value' WHERE userID = $userid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

	/*****  AdminUsers_update_IdleAdminStatus  *********************
	 *
	 *  History:
	 *	Nate Lee				Nov 3, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_update_IdleAdminStatus( &$dbh,
					$idle )
	{
		if ( $idle == "" )
		{
			return false ;
		}
		$idle = database_mysql_quote( $idle ) ;
		$users = Array() ;

		$query = "SELECT userID FROM chat_admin WHERE last_active_time < $idle AND available_status = 1" ;
		database_mysql_query( $dbh, $query ) ;

		while ( $data = database_mysql_fetchrow( $dbh ) )
			$users[] = $data ;

		for ( $c = 0; $c < count( $users ); ++$c )
		{
			$user = $users[$c] ;
			OpStatus_put_Status( $dbh, $user['userID'], 0 ) ;
		}
		$query = "UPDATE chat_admin SET available_status = 0 WHERE last_active_time < $idle AND available_status = 1" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}

	/*****  AdminUsers_update_Password  *********************
	 *
	 *  History:
	 *	Kyle Hicks				Dec 18, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_update_Password( &$dbh,
					  $userid,
					  $password )
	{
		if ( $password == "" )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$password = md5( database_mysql_quote( $password ) ) ;

		$query = "UPDATE chat_admin SET password = '$password' WHERE userID = $userid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

	/*****  AdminUsers_update_User  *********************
	 *
	 *  History:
	 *	Kyle Hicks				Dec 19, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_update_User( &$dbh,
						$userid,
						$login,
						$password,
						$name,
						$email,
						$aspid,
						$rateme,
						$op2op )
	{
		if ( ( $login == "" ) || ( $password == "" )
			|| ( $name == "" ) || ( $email == "" )
			|| ( $userid == "" ) || ( $aspid == "" ) )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$login = database_mysql_quote( $login ) ;
		$name = database_mysql_quote( $name ) ;
		$email = database_mysql_quote( $email ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		$rateme = database_mysql_quote( $rateme ) ;
		$op2op = database_mysql_quote( $op2op ) ;
		$password = md5( database_mysql_quote( $password ) ) ;

		$query = "UPDATE chat_admin SET login = '$login', password = '$password', name = '$name', email = '$email', rateme = '$rateme', op2op = '$op2op' WHERE userID = $userid AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

	/*****  AdminUsers_update_DeptValue  *********************
	 *
	 *  History:
	 *	Kyle				April 27, 2002
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_update_DeptValue( &$dbh,
					  $aspid,
					  $deptid,
					  $tbl_name,
					  $value )
	{
		if ( ( $aspid == "" ) || ( $deptid == "" )
			|| ( $tbl_name == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$deptid = database_mysql_quote( $deptid ) ;
		$tbl_name = database_mysql_quote( $tbl_name ) ;
		$value = database_mysql_quote( $value ) ;

		$query = "UPDATE chatdepartments SET $tbl_name = '$value' WHERE deptID = $deptid AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

	/*****  AdminUsers_update_UserDeptOrder  *********************
	 *
	 *  History:
	 *	Kyle				August 28, 2002
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_update_UserDeptOrder( &$dbh,
					  $userid,
					  $deptid,
					  $order )
	{
		if ( ( $userid == "" ) || ( $deptid == "" ) )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$deptid = database_mysql_quote( $deptid ) ;
		$order = database_mysql_quote( $order ) ;

		$query = "UPDATE chatuserdeptlist SET ordernum = '$order' WHERE userID = $userid AND deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

	/*****  AdminUsers_update_UserDeptActvyTime  *********************
	 *
	 *  History:
	 *	Kyle				July 23, 2003
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_update_UserDeptActvyTime( &$dbh,
					  $userid,
					  $deptid )
	{
		if ( ( $userid == "" ) || ( $deptid == "" ) )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$deptid = database_mysql_quote( $deptid ) ;
		$now = time() ;

		$query = "UPDATE chatuserdeptlist SET last_active = '$now' WHERE userID = $userid AND deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

	/*****  AdminUsers_update_AutoUpdateOnlineStatus  *********************
	 *
	 *  History:
	 *	Kyle				April 18, 2004
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_update_AutoUpdateOnlineStatus( &$dbh,
					$userid )
	{
		if ( $userid == "" )
			return false ;
		$userid = database_mysql_quote( $userid ) ;

		$query = "UPDATE chat_admin SET available_status = 1 WHERE userID = $userid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			OpStatus_put_Status( $dbh, $userid, 1 ) ;
			return true ;
		}
		return false ;
	}

?>