<?php
	/*****  AdminASP::update  ***************************************
	 *
	 *  $Id: update.php,v 1.4 2005/02/06 00:42:27 osicodes Exp $
	 *
	 ****************************************************************/

	if ( ISSET( $_OFFICE_UPDATE_AdminASP_LOADED ) == true )
		return ;

	$_OFFICE_UPDATE_AdminASP_LOADED = true ;

	/*****  AdminASP_update_User  *********************
	 *
	 *  History:
	 *	Kyle Hicks				Dec 19, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminASP_update_User( &$dbh,
						$aspid,
						$login,
						$password,
						$company,
						$contact_name,
						$contact_email,
						$max_dept,
						$max_users,
						$footprints,
						$active_status,
						$initiate_chat )
	{
		if ( ( $login == "" ) || ( $password == "" )
			|| ( $contact_name == "" ) || ( $contact_email == "" )
			|| ( $company == "" ) || ( $max_dept == "" )
			|| ( $max_users == "" ) || ( $aspid == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$login = database_mysql_quote( $login ) ;
		$password = database_mysql_quote( $password ) ;
		$company = database_mysql_quote( $company ) ;
		$contact_name = database_mysql_quote( $contact_name ) ;
		$contact_email = database_mysql_quote( $contact_email ) ;
		$max_dept = database_mysql_quote( $max_dept ) ;
		$max_users = database_mysql_quote( $max_users ) ;
		$footprints = database_mysql_quote( $footprints ) ;
		$active_status = database_mysql_quote( $active_status ) ;
		$initiate_chat = database_mysql_quote( $initiate_chat ) ;

		$query = "UPDATE chat_asp SET login = '$login', password = '$password', company = '$company', contact_name = '$contact_name', contact_email = '$contact_email', max_dept = '$max_dept', max_users = '$max_users', footprints = '$footprints', active_status = '$active_status', initiate_chat = '$initiate_chat' WHERE aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

	/*****  AdminASP_update_UserActiveStatus  *********************
	 *
	 *  History:
	 *	Kyle Hicks				Jan 9, 2002
	 *
	 *****************************************************************/
	FUNCTION AdminASP_update_UserActiveStatus( &$dbh,
						$aspid,
						$status )
	{
		if ( $aspid == "" )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$status = database_mysql_quote( $status ) ;

		$query = "UPDATE chat_asp SET active_status = '$status' WHERE aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

	/*****  AdminASP_update_TableValue  *********************
	 *
	 *  History:
	 *	Kyle				July 1, 2002
	 *
	 *****************************************************************/
	FUNCTION AdminASP_update_TableValue( &$dbh,
					  $aspid,
					  $tbl_name,
					  $value )
	{
		if ( ( $aspid == "" ) || ( $tbl_name == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$tble_name = database_mysql_quote( $tbl_name ) ;
		$value = database_mysql_quote( $value ) ;

		$query = "UPDATE chat_asp SET $tbl_name = '$value' WHERE aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

	/*****  AdminASP_update_UserOperatorCompany  *********************
	 *
	 *  History:
	 *	Kyle Hicks				Feb 26, 2003
	 *
	 *****************************************************************/
	FUNCTION AdminASP_update_UserOperatorCompany( &$dbh,
						$aspid,
						$company_name )
	{
		if ( ( $aspid == "" ) || ( $company_name == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$company_name = database_mysql_quote( $company_name ) ;

		$query = "UPDATE chat_admin SET company = '$company_name' WHERE aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

?>