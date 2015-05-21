<?php
	if ( ISSET( $_OFFICE_REMOVE_OpStatus_LOADED ) == true )
		return ;
	$_OFFICE_REMOVE_OpStatus_LOADED = true ;

	include_once( "$DOCUMENT_ROOT/API/Users/get.php" ) ;
	include_once( "$DOCUMENT_ROOT/API/Users/put.php" ) ;
	include_once( "$DOCUMENT_ROOT/API/ASP/get.php" ) ;

	/*****  OpStatus_remove_user  ************************************
	 *
	 *  History:
	 *	Nate Lee				Nov 5, 2001
	 *
	 *****************************************************************/
	FUNCTION OpStatus_remove_user( &$dbh,
						$userid,
						$aspid )
	{
		if ( ( $userid == "" ) || ( $aspid == "" ) )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		$userinfo = OpStatus_get_UserInfo( $dbh, $userid, $aspid ) ;

		if ( $userinfo['aspID'] == $aspid )
		{
			$query = "DELETE FROM chatuserdeptlist WHERE userID = $userid" ;
			database_mysql_query( $dbh, $query ) ;

			$query = "DELETE FROM chatcanned WHERE userID = $userid" ;
			database_mysql_query( $dbh, $query ) ;

			$query = "DELETE FROM chat_admin WHERE userID = $userid AND aspID = $aspid" ;
			database_mysql_query( $dbh, $query ) ;
			return true ;
		}

		return false ;
	}

	/*****  OpStatus_remove_DeptUser  ************************************
	 *
	 *  History:
	 *	Holger				Dec 26, 2001
	 *
	 *****************************************************************/
	FUNCTION OpStatus_remove_Deptuser( &$dbh,
						$userid,
						$deptid )
	{
		if ( ( $userid == "" ) || ( $deptid == "" ) )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$deptid = database_mysql_quote( $deptid ) ;

		$query = "DELETE FROM chatuserdeptlist WHERE userID = $userid AND deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;

		return false ;
	}

	/*****  OpStatus_remove_Dept  ************************************
	 *
	 *  History:
	 *	Holger				Dec 26, 2001
	 *
	 *****************************************************************/
	FUNCTION OpStatus_remove_Dept( &$dbh,
						$deptid,
						$transfer_deptid,
						$aspid )
	{
		if ( ( $deptid == "" ) || ( $transfer_deptid == "" ) 
			|| ( $aspid == "" ) )
		{
			return false ;
		}
		$deptid = database_mysql_quote( $deptid ) ;
		$transfer_deptid = database_mysql_quote( $transfer_deptid ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		global $DOCUMENT_ROOT ;

		$aspinfo = AdminASP_get_UserInfo( $dbh, $aspid ) ;
		$deptinfo = OpStatus_get_DeptInfo( $dbh, $deptid, $aspid ) ;

		$query = "UPDATE chatrequestlogs SET deptID = $transfer_deptid WHERE deptID = $deptid AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "UPDATE chattranscripts SET deptID = $transfer_deptid WHERE deptID = $deptid AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "UPDATE chatcanned SET deptID = $transfer_deptid WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;

		// get all department users from dept that is to be deleted
		$dept_users = OpStatus_get_AllDeptUsers( $dbh, $deptid ) ;

		// first delete all data from chatuserdeptlist
		$query = "DELETE FROM chatuserdeptlist WHERE deptID = $deptid AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;

		// now we put them back with new deptID
		for ( $c = 0; $c < count( $dept_users ); ++$c )
		{
			$user = $dept_users[$c] ;
			OpStatus_put_DeptUser( $dbh, $user['userID'], $transfer_deptid ) ;
		}

		$query = "DELETE FROM chatdepartments WHERE deptID = $deptid AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;

		// clean up department online/offline status images
		if ( file_exists ( "$DOCUMENT_ROOT/web/$aspinfo[login]/$deptinfo[status_image_online]" ) && $deptinfo['status_image_online'] )
			unlink( "$DOCUMENT_ROOT/web/$aspinfo[login]/$deptinfo[status_image_online]" ) ;
		if ( file_exists ( "$DOCUMENT_ROOT/web/$aspinfo[login]/$deptinfo[status_image_offline]" ) && $deptinfo['status_image_offline'] )
			unlink( "$DOCUMENT_ROOT/web/$aspinfo[login]/$deptinfo[status_image_offline]" ) ;

		return false ;
	}
?>
