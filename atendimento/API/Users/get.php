<?php
	if ( ISSET( $_OFFICE_GET_AdminUsers_LOADED ) == true )
		return ;

	$_OFFICE_GET_AdminUsers_LOADED = true ;

	include_once( "$DOCUMENT_ROOT/API/ASP/get.php" ) ;

	/*****  AdminUsers_get_AreAnyAdminOnline  *********************
	 *
	 *  History:
	 *	Nate Lee				Dec 27, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_AreAnyAdminOnline( &$dbh,
								$deptid,
								$idle,
								$aspid )
	{
		if ( ( $idle == "" ) || ( $aspid == "" ) )
		{
			return false ;
		}
		$deptid = database_mysql_quote( $deptid ) ;
		$idle = database_mysql_quote( $idle ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		// if $deptid is passed, then let's check ONLY admins of that department
		if ( $deptid )
		{
			$query = "SELECT count(*) AS total FROM chat_admin, chatuserdeptlist WHERE chat_admin.userID = chatuserdeptlist.userID AND chatuserdeptlist.deptID = $deptid AND chat_admin.available_status > 0 AND chat_admin.last_active_time > $idle AND chat_admin.aspID = $aspid" ;
		}
		else
		{
			$include_departments = "" ;
			$visible_departments = AdminUsers_get_AllDepartments( $dbh, $aspid, 0 ) ;
			for ( $c = 0; $c < count( $visible_departments ); ++$c )
			{
				$visible_department = $visible_departments[$c] ;
				if ( $c == 0 )
					$include_departments .= "chatuserdeptlist.deptID = $visible_department[deptID] " ;
				else
					$include_departments .= " OR chatuserdeptlist.deptID = $visible_department[deptID] " ;
			}

			if ( $include_departments )
				$include_departments = "AND ( $include_departments )" ;

			
			$query = "SELECT count(*) AS total FROM chat_admin, chatuserdeptlist WHERE chat_admin.userID = chatuserdeptlist.userID AND chat_admin.available_status > 0 AND chat_admin.last_active_time > $idle AND chat_admin.aspID = $aspid $include_departments" ;
		}
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data['total'] ;
		}
		return false ;
	}

	/*****  AdminUsers_get_HiddenDepatments  *******************************
	 *
	 *  History:
	 *	Kyle Hicks				August 26, 2004
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_HiddenDepatments( &$dbh,
					$aspid )
	{
		if ( $aspid == "" )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$departments = ARRAY() ;

		$query = "SELECT * FROM chatdepartments WHERE aspID = $aspid AND visible = 0" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			while ( $data = database_mysql_fetchrow( $dbh ) )
				$departments[] = $data ;
			return $departments ;
		}
		return false ;
	}

	/*****  AdminUsers_get_AreAnyAdminConsolesOnline  *********************
	 *
	 *  History:
	 *	Nate Lee				Dec 27, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_AreAnyAdminConsolesOnline( &$dbh,
								$deptid,
								$idle,
								$aspid )
	{
		if ( ( $idle == "" ) || ( $aspid == "" ) )
		{
			return false ;
		}
		$deptid = database_mysql_quote( $deptid ) ;
		$idle = database_mysql_quote( $idle ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		// if $deptid is passed, then let's check ONLY admins of that department
		if ( $deptid )
			$query = "SELECT count(*) AS total FROM chat_admin, chatuserdeptlist WHERE chat_admin.userID = chatuserdeptlist.userID AND chatuserdeptlist.deptID = $deptid AND chat_admin.last_active_time > $idle AND chat_admin.aspID = $aspid" ;
		else
			$query = "SELECT count(*) AS total FROM chat_admin WHERE last_active_time > $idle AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data['total'] ;
		}
		return false ;
	}

	/*****  AdminUsers_get_AllUsers  *******************************
	 *
	 *  History:
	 *	Holger				Nov 5, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_AllUsers( &$dbh,
					$page,
					$page_per,
					$aspid )
	{
		if ( $aspid == "" )
		{
			return false ;
		}
		$page = database_mysql_quote( $page ) ;
		$page_per = database_mysql_quote( $page_per ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		$users = ARRAY() ;

		$page -= 1 ;
		if ( $page < 1 )
			$begin_index = 0 ;
		else
			$begin_index = $page * $page_per ;

		if ( $page_per )
			$query = "SELECT * FROM chat_admin WHERE aspID = $aspid ORDER BY login ASC LIMIT $begin_index, $page_per" ;
		else
			$query = "SELECT * FROM chat_admin WHERE aspID = $aspid ORDER BY login ASC" ;

		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			while ( $data = database_mysql_fetchrow( $dbh ) )
				$users[] = $data ;
			return $users ;
		}
		return false ;
	}

	/*****  AdminUsers_get_TotalUsers  *******************************
	 *
	 *  History:
	 *	Holger				Nov 5, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_TotalUsers( &$dbh,
										$aspid )
	{
		if ( $aspid == "" )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$query = "SELECT count(*) AS total FROM chat_admin WHERE aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data['total'] ;
		}
		return false ;
	}

	/*****  AdminUsers_get_TotalUsersDeptList  *******************************
	 *
	 *  History:
	 *	Holger				Nov 5, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_TotalUsersDeptList( &$dbh,
										$aspid,
										$deptid )
	{
		if ( ( $aspid == "" ) || ( $deptid == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$deptid = database_mysql_quote( $deptid ) ;

		$query = "SELECT count(*) AS total FROM chatuserdeptlist WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data['total'] ;
		}
		return false ;
	}

	/*****  AdminUsers_get_TotalDepartments  *******************************
	 *
	 *  History:
	 *	Holger				Nov 5, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_TotalDepartments( &$dbh,
										$aspid )
	{
		if ( $aspid == "" )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;

		$query = "SELECT count(*) AS total FROM chatdepartments WHERE aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data['total'] ;
		}
		return false ;
	}

	/*****  AdminUsers_get_AllDeptUsers  *******************************
	 *
	 *  History:
	 *	Holger				Dec 25, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_AllDeptUsers( &$dbh,
					$deptid )
	{
		if ( $deptid == "" )
		{
			return false ;
		}
		$deptid = database_mysql_quote( $deptid ) ;
		$users = ARRAY() ;

		$query = "SELECT chat_admin.* FROM chat_admin, chatuserdeptlist WHERE chat_admin.userID = chatuserdeptlist.userID and chatuserdeptlist.deptID = $deptid ORDER BY chat_admin.login ASC" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			while ( $data = database_mysql_fetchrow( $dbh ) )
				$users[] = $data ;
			return $users ;
		}
		return false ;
	}

	/*****  AdminUsers_get_AllDeptUsers  *******************************
	 *
	 *  History:
	 *	Holger				Dec 25, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_AllDeptUsersOrder( &$dbh,
					$deptid )
	{
		if ( $deptid == "" )
		{
			return false ;
		}
		$deptid = database_mysql_quote( $deptid ) ;
		$users = ARRAY() ;

		$query = "SELECT chat_admin.* FROM chat_admin, chatuserdeptlist WHERE chat_admin.userID = chatuserdeptlist.userID and chatuserdeptlist.deptID = $deptid ORDER BY chatuserdeptlist.ordernum ASC" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			while ( $data = database_mysql_fetchrow( $dbh ) )
				$users[] = $data ;
			return $users ;
		}
		return false ;
	}

	/*****  AdminUsers_get_UserInfo  *******************************
	 *
	 *  History:
	 *	Holger				Nov 5, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_UserInfo( &$dbh,
										$userid,
										$aspid )
	{
		if ( ( $userid == "" ) || ( $aspid == "" ) )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$aspid = database_mysql_quote( $aspid ) ;

		$query = "SELECT * FROM chat_admin WHERE userID = $userid AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	/*****  AdminUsers_get_DeptInfo  *******************************
	 *
	 *  History:
	 *	Holger				Dec 25, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_DeptInfo( &$dbh,
									$deptid,
									$aspid )
	{
		if ( ( $deptid == "" ) || ( $aspid == "" ) )
		{
			return false ;
		}
		$deptid = database_mysql_quote( $deptid ) ;
		$aspid = database_mysql_quote( $aspid ) ;

		$query = "SELECT * FROM chatdepartments WHERE deptID = $deptid AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	/*****  AdminUsers_get_AllDeptUsersAvailable  *******************************
	 *
	 *  History:
	 *	Holger				NOv 5, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_AllDeptUsersAvailable( &$dbh,
										$aspid,
										$deptid,
										$exclude_userid )
	{
		if ( ( $aspid == "" ) || ( $deptid == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$deptid = database_mysql_quote( $deptid ) ;
		$exclude_userid = database_mysql_quote( $exclude_userid ) ;
		$admins = ARRAY() ;
		$aspinfo = AdminASP_get_UserInfo( $dbh, $aspid ) ;

		$order_by = "" ;
		if ( $aspinfo['admin_polling_type'] == 0 )
			$order_by = "ORDER BY chatuserdeptlist.ordernum ASC" ;
		else if ( $aspinfo['admin_polling_type'] == 1 )
			$order_by = "ORDER BY chatuserdeptlist.last_active ASC" ;
		else if ( $aspinfo['admin_polling_type'] == 2 )
			$order_by = "ORDER BY RAND()" ;

		$query = "SELECT * FROM chat_admin, chatuserdeptlist WHERE chatuserdeptlist.userID = chat_admin.userID AND chatuserdeptlist.deptID = $deptid AND chat_admin.available_status > 0 $exclude_userid $order_by" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
				$admins[] = $data ;
			return $admins ;
		}
		return false ;
	}

	/*****  AdminUsers_get_LessLoadedDeptUser  *******************************
	 *
	 *  History:
	 *	Holger				Dec 14, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_LessLoadedDeptUser( &$dbh,
										$deptid,
										$exclude_userid,
										$aspid )
	{
		if ( ( $deptid == "" ) || ( $aspid == "" ) )
		{
			return false ;
		}
		$deptid = database_mysql_quote( $deptid ) ;
		$exclude_userid = database_mysql_quote( $exclude_userid ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		$lessloaded_admin = ARRAY() ;
		$admins = AdminUsers_get_AllDeptUsersAvailable( $dbh, $aspid, $deptid, $exclude_userid ) ;

		// if there is only one admin in that department, then just return that admin's info
		if ( count( $admins ) == 1 )
			return $admins[0] ;

		for ( $c = 0; $c < count( $admins ); ++$c )
		{
			$admin = $admins[$c] ;
			$query = "SELECT COUNT(*) AS total FROM chatrequests WHERE userID = $admin[userID]" ;
			database_mysql_query( $dbh, $query ) ;

			if ( $dbh[ 'ok' ] )
			{
				$data = database_mysql_fetchrow( $dbh ) ;

				if ( !isset( $lessloaded_count ) || ( $data['total'] < $lessloaded_count ) )
				{
					$lessloaded_admin = $admin ;
					$lessloaded_count = $data['total'] ;

					if ( $data['total'] == 0 )
						break 1 ;
				}
			}
		}
		
		return $lessloaded_admin ;
	}

	/*****  AdminUsers_get_UserInfoByLoginPass  ************************
	 *
	 *  History:
	 *	Holger				Nov 5, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_UserInfoByLoginPass( &$dbh,
						$login,
						$password,
						$aspid )
	{
		if ( ( $login == "" ) || ( $password == "" ) ||
			( $aspid == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$login = database_mysql_quote( $login ) ;
		$password = md5( database_mysql_quote( $password ) ) ;

		$query = "SELECT * FROM chat_admin WHERE login = '$login' AND password = '$password' AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	/*****  AdminUsers_get_IsLoginTaken  ******************************
	 *
	 *  History:
	 *	Holger				Nov 5, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_IsLoginTaken( &$dbh,
						$login,
						$aspid )
	{
		if ( ( $login == "" ) || ( $aspid == "" ) )
		{
			return false ;
		}
		$login = database_mysql_quote( $login ) ;
		$aspid = database_mysql_quote( $aspid ) ;

		$query = "SELECT * FROM chat_admin WHERE login = '$login' AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			if ( $data['userID'] )
				return $data['userID'] ;
		}
		return false ;
	}

	/*****  AdminUsers_get_IsNameTaken  ******************************
	 *
	 *  History:
	 *	Kyle				June 18, 2002
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_IsNameTaken( &$dbh,
						$name,
						$aspid )
	{
		if ( ( $name == "" ) || ( $aspid == "" ) )
		{
			return false ;
		}
		$name = database_mysql_quote( $name ) ;
		$aspid = database_mysql_quote( $aspid ) ;

		$query = "SELECT * FROM chat_admin WHERE name = '$name' AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			if ( $data['userID'] )
				return $data['userID'] ;
		}
		return false ;
	}

	/*****  AdminUsers_get_IsUserInDept  ******************************
	 *
	 *  History:
	 *	Kyle Hicks				Dec 28, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_IsUserInDept( &$dbh,
						$userid,
						$deptid )
	{
		if ( ( $userid == "" ) || ( $deptid == "" ) )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$deptid = database_mysql_quote( $deptid ) ;

		$query = "SELECT chat_admin.* FROM chat_admin, chatuserdeptlist WHERE chat_admin.userID = chatuserdeptlist.userID AND chat_admin.userID = $userid AND chatuserdeptlist.deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			if ( $data['userID'] )
				return $data['userID'] ;
		}
		return false ;
	}

	/*****  AdminUsers_get_AllDepartments  ******************************
	 *
	 *  History:
	 *	Holger				Dec 14, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_AllDepartments( &$dbh,
						$aspid,
						$get_all )
	{
		if ( $aspid == "" )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$get_all = database_mysql_quote( $get_all ) ;
		$departments = ARRAY() ;

		$all_string = "" ;
		if ( !$get_all )
			$all_string = "AND visible = 1" ;

		$query = "SELECT * FROM chatdepartments WHERE aspID = $aspid $all_string ORDER BY name ASC" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
				$departments[] = $data ;
			return $departments ;
		}
		return false ;
	}

	/*****  AdminUsers_get_UserDepartments  ******************************
	 *
	 *  History:
	 *	Kyle Hicks				Dec 28, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_UserDepartments( &$dbh,
								$userid )
	{
		if ( $userid == "" )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$departments = ARRAY() ;

		$query = "SELECT * FROM chatdepartments, chatuserdeptlist WHERE chatdepartments.deptID = chatuserdeptlist.deptID AND chatuserdeptlist.userID = $userid ORDER BY chatdepartments.name ASC" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
				$departments[] = $data ;
			return $departments ;
		}
		return false ;
	}

	/*****  AdminUsers_get_TotalDepartmentUsersOnline  ******************************
	 *
	 *  History:
	 *	Holger				Dec 14, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_TotalDepartmentUsersOnline( &$dbh,
					$deptid )
	{
		if ( $deptid == "" )
		{
			return false ;
		}
		$deptid = database_mysql_quote( $deptid ) ;

		$query = "SELECT COUNT(*) AS total FROM chat_admin, chatuserdeptlist WHERE chatuserdeptlist.userID = chat_admin.userID AND chatuserdeptlist.deptID = $deptid AND chat_admin.available_status > 0" ;
		database_mysql_query( $dbh, $query ) ;
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data['total'] ;
		}
		return false ;
	}

	/*****  AdminUsers_get_CanUserInitiate  ******************************
	 *
	 *  History:
	 *	Kyle Hicks				March 13, 2002
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_CanUserInitiate( &$dbh,
								$userid )
	{
		if ( $userid == "" )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;

		$query = "SELECT count(*) AS total FROM chatdepartments, chatuserdeptlist WHERE chatdepartments.deptID = chatuserdeptlist.deptID AND userID = $userid AND chatdepartments.initiate_chat = 1" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data['total'] ;
		}
		return false ;
	}

	/*****  AdminUsers_get_DeptUserOrderNum  **************************
	 *
	 *  History:
	 *	Kyle Hicks				August 28, 2002
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_DeptUserOrderNum( &$dbh,
								$userid,
								$deptid )
	{
		if ( ( $userid == "" ) || ( $deptid == "" ) )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$deptid = database_mysql_quote( $deptid ) ;

		$query = "SELECT ordernum FROM chatuserdeptlist WHERE userID = $userid AND deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data['ordernum'] ;
		}
		return false ;
	}

	/*****  AdminUsers_get_UserInfoBySession  ************************
	 *
	 *  History:
	 *	Kyle Hicks				August 25, 2004
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_get_UserInfoBySession( &$dbh,
						$aspid,
						$sid )
	{
		if ( ( $sid == "" ) || ( $aspid == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$sid = database_mysql_quote( $sid ) ;

		$query = "SELECT * FROM chat_admin WHERE session_sid = $sid AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}
?>
