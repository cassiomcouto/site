<?php
	/*****  AdminASP::get  ***************************************
	 *
	 *  $Id: get.php,v 1.5 2005/02/06 00:42:27 osicodes Exp $
	 *
	 ****************************************************************/

	if ( ISSET( $_OFFICE_GET_AdminASP_LOADED ) == true )
		return ;

	$_OFFICE_GET_AdminASP_LOADED = true ;

	/*****  AdminASP_get_AllUsers  *******************************
	 *
	 *  History:
	 *	Kyle Hicks				Jan 19, 2002
	 *
	 *****************************************************************/
	FUNCTION AdminASP_get_AllUsers( &$dbh,
					$page,
					$page_per )
	{
		$users = ARRAY() ;
		$page = database_mysql_quote( $page ) ;
		$page_per = database_mysql_quote( $page_per ) ;

		$page -= 1 ;
		if ( $page < 1 )
			$begin_index = 0 ;
		else
			$begin_index = $page * $page_per ;

		if ( $page_per )
			$query = "SELECT * FROM chat_asp ORDER BY login ASC LIMIT $begin_index, $page_per" ;
		else
			$query = "SELECT * FROM chat_asp ORDER BY login ASC" ;

		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			while ( $data = database_mysql_fetchrow( $dbh ) )
			{
				$users[] = $data ;
			}
			return $users ;
		}
		return false ;
	}

	/*****  AdminASP_get_UserInfo  *******************************
	 *
	 *  History:
	 *	Kyle Hicks				Jan 19, 2002
	 *
	 *****************************************************************/
	FUNCTION AdminASP_get_UserInfo( &$dbh,
									$aspid )
	{
		if ( $aspid == "" )
		{
			return false ;
		}

		$aspid = database_mysql_quote( $aspid ) ;
		$query = "SELECT * FROM chat_asp WHERE aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	/*****  AdminASP_get_UserInfoByLoginPass  ************************
	 *
	 *  History:
	 *	Kyle Hicks				Jan 19, 2002
	 *
	 *****************************************************************/
	FUNCTION AdminASP_get_UserInfoByLoginPass( &$dbh,
						$login,
						$password )
	{
		if ( ( $login == "" ) || ( $password == "" ) )
		{
			return false ;
		}

		$login = database_mysql_quote( $login ) ;
		$password = database_mysql_quote( $password ) ;

		$query = "SELECT * FROM chat_asp WHERE login = '$login' AND password = '$password'" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	/*****  AdminASP_get_ASPInfoByASPLogin  ************************
	 *
	 *  History:
	 *	Kyle Hicks				Jan 19, 2002
	 *
	 *****************************************************************/
	FUNCTION AdminASP_get_ASPInfoByASPLogin( &$dbh,
						$login )
	{
		if ( $login == "" )
		{
			return false ;
		}
		$login = database_mysql_quote( $login ) ;

		$query = "SELECT * FROM chat_asp WHERE login = '$login'" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	/*****  AdminASP_get_IsLoginTaken  ******************************
	 *
	 *  History:
	 *	Kyle Hicks				Jan 19, 2002
	 *
	 *****************************************************************/
	FUNCTION AdminASP_get_IsLoginTaken( &$dbh,
						$login )
	{
		if ( $login == "" )
		{
			return false ;
		}
		$login = database_mysql_quote( $login ) ;

		$query = "SELECT * FROM chat_asp WHERE login = '$login'" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data['aspID'] ;
		}
		return false ;
	}

	/*****  AdminASP_get_TotalUsers  ******************************
	 *
	 *  History:
	 *	Kyle Hicks				Jan 23, 2002
	 *
	 *****************************************************************/
	FUNCTION AdminASP_get_TotalUsers( &$dbh )
	{

		$query = "SELECT count(*) AS total FROM chat_asp" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data['total'] ;
		}
		return false ;
	}

?>