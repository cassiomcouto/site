<?php
	/*****  AdminASP::remove  ***************************************
	 *
	 *  $Id: remove.php,v 1.4 2005/02/06 00:42:27 osicodes Exp $
	 *
	 ****************************************************************/

	if ( ISSET( $_OFFICE_REMOVE_AdminASP_LOADED ) == true )
		return ;

	$_OFFICE_REMOVE_AdminASP_LOADED = true ;

	include_once( "$DOCUMENT_ROOT/API/ASP/get.php" ) ;
	include_once( "$DOCUMENT_ROOT/API/Users/get.php" ) ;

	/*****  AdminASP_remove_user  ************************************
	 *
	 *  History:
	 *	Nate Lee				jan 23, 2002
	 *
	 *****************************************************************/
	FUNCTION AdminASP_remove_user( &$dbh,
						$aspid )
	{
		if ( $aspid == "" )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		global $DOCUMENT_ROOT ;

		/*************************************************
		* we do not mess with the chatsession tables.. tables related
		* to chat gets auto cleaned
		*************************************************/

		$aspinfo = AdminASP_get_UserInfo( $dbh, $aspid ) ;

		if ( isset( $aspinfo['aspID'] ) )
		{
			$users = AdminUsers_get_AllUsers( $dbh, 0, 0, $aspid ) ;
			for ( $c = 0; $c < count( $users ); ++$c )
			{
				$user = $users[$c] ;
				$query = "DELETE FROM chatcanned WHERE userID = $user[userID]" ;
				database_mysql_query( $dbh, $query ) ;
				$query = "DELETE FROM chatuserdeptlist WHERE userID = $user[userID]" ;
				database_mysql_query( $dbh, $query ) ;
				$query = "DELETE FROM chat_admin WHERE userID = $user[userID]" ;
				database_mysql_query( $dbh, $query ) ;
				$query = "DELETE FROM chat_adminstatus WHERE userID = $user[userID]" ;
				database_mysql_query( $dbh, $query ) ;
			}
			// do another run down ONLY using aspID
			$query = "DELETE FROM chat_admin WHERE aspID = $aspid" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "DELETE FROM chatdepartments WHERE aspID = $aspid" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "DELETE FROM chatfootprints WHERE aspID = $aspid" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "DELETE FROM chatfootprintstats WHERE aspID = $aspid" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "DELETE FROM chatrequestlogs WHERE aspID = $aspid" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "DELETE FROM chattranscripts WHERE aspID = $aspid" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "DELETE FROM chatfootprintsunique WHERE aspID = $aspid" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "DELETE FROM chatrefer WHERE aspID = $aspid" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "DELETE FROM chat_adminrate WHERE aspID = $aspid" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "DELETE FROM chatclicktracking WHERE aspID = $aspid" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "DELETE FROM chatclicks WHERE aspID = $aspid" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "DELETE FROM chatkbcats WHERE aspID = $aspid" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "DELETE FROM chatkbquestions WHERE aspID = $aspid" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "DELETE FROM chatkbsearchterms WHERE aspID = $aspid" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "DELETE FROM chatsprefer WHERE aspID = $aspid" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "DELETE FROM chatspfootprints WHERE aspID = $aspid" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "DELETE FROM chatspamips WHERE aspID = $aspid" ;
			database_mysql_query( $dbh, $query ) ;

			// final remove of user
			$query = "DELETE FROM chat_asp WHERE aspID = $aspid" ;
			database_mysql_query( $dbh, $query ) ;

			// now let's clean the user files from the system
			if ( $dir = @opendir( "$DOCUMENT_ROOT/web/$aspinfo[login]" ) )
			{
				while( $file = readdir( $dir ) )
				{
					if ( preg_match( "/[A-Za-z0-9]/", $file ) )
						unlink( "$DOCUMENT_ROOT/web/$aspinfo[login]/$file" ) ;
				} 
				closedir($dir) ;
				rmdir( "$DOCUMENT_ROOT/web/$aspinfo[login]" ) ;
			}
			return true ;
		}
		else
			return false ;
	}

?>