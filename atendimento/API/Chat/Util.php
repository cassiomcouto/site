<?php
	if ( ISSET( $_OFFICE_GET_UtilChat_LOADED ) == true )
		return ;

	$_OFFICE_UtilChat_LOADED = true ;

	/*****

	   Internal Dependencies

	*****/
	include_once( "$DOCUMENT_ROOT/API/Chat/update.php" ) ;

	/*****

	   Module Specifics

	*****/

	/*****

	   Module Functions

	*****/

	/*****  UtilChat_InitializeChatSession  **************************
	 *
	 *  History:
	 *	Holger					March 1, 2002
	 *
	 *****************************************************************/
	FUNCTION UtilChat_InitializeChatSession( $sid,
							$sessionid,
							$poll_list,
							$screen_name,
							$admin_name,
							$visitor_name,
							$adminid,
							$deptid,
							$initiate_flag,
							$aspid,
							$asp_login,
							$isadmin,
							$op2op,
							$theme )
	{
		if ( $sid == "" )
		{
			return false ;
		}

		$_SESSION['session_chat'][$sid]['admin_poll_time'] = time() ;
		$_SESSION['session_chat'][$sid]['sessionid'] = $sessionid ;
		$_SESSION['session_chat'][$sid]['admin_poll_list'] = $poll_list ;
		$_SESSION['session_chat'][$sid]['screen_name'] = $screen_name ;
		$_SESSION['session_chat'][$sid]['admin_name'] = $admin_name ;
		$_SESSION['session_chat'][$sid]['visitor_name'] = $visitor_name ;
		$_SESSION['session_chat'][$sid]['admin_id'] = $adminid ;
		$_SESSION['session_chat'][$sid]['deptid'] = $deptid ;
		$_SESSION['session_chat'][$sid]['op2op'] = $op2op ;
		$_SESSION['session_chat'][$sid]['initiate'] = $initiate_flag ;
		$_SESSION['session_chat'][$sid]['aspID'] = $aspid ;
		$_SESSION['session_chat'][$sid]['asp_login'] = $asp_login ;
		$_SESSION['session_chat'][$sid]['isadmin'] = $isadmin ;
		$_SESSION['session_chat'][$sid]['total_counter'] = 1 ;
		$_SESSION['session_chat'][$sid]['question'] = "" ;
		$_SESSION['session_chat'][$sid]['theme'] = $theme ;
	}

	/*****  UtilChat_AppendToChatfile  *********************************
	 *
	 *  History:
	 *	Kory Cline				Nov 8, 2001
	 *
	 *****************************************************************/
	FUNCTION UtilChat_AppendToChatfile( $chatfile,
							$string )
	{
		if ( ( $chatfile == "" ) || ( $string == "" ) )
		{
			return false ;
		}

		global $DOCUMENT_ROOT ;

		$fp = fopen("$DOCUMENT_ROOT/web/chatsessions/$chatfile", "a");
		fwrite( $fp, $string, strlen( $string ) ) ;
		fclose( $fp ) ;

		return true ;
	}

	/*****  UtilChat_RemoveChatfile  *********************************
	 *
	 *  History:
	 *	Kyle Hicks				April 27, 2003
	 *
	 *****************************************************************/
	FUNCTION UtilChat_RemoveChatfile( $chatfile )
	{
		if ( $chatfile == "" )
		{
			return false ;
		}

		global $DOCUMENT_ROOT ;

		if ( file_exists( "$DOCUMENT_ROOT/web/chatsessions/$chatfile" ) )
			unlink( "$DOCUMENT_ROOT/web/chatsessions/$chatfile" ) ;
		return true ;
	}

	/*****  UtilChat_ParseForCommands  *********************************
	 *
	 *  History:
	 *	Kory Cline				Dec 8, 2001
	 *
	 *****************************************************************/
	FUNCTION UtilChat_ParseForCommands( $string )
	{
		// this will be the name of the new window that gets pushed
		// why?  so admin and client has their ONE window which will
		// load the pushed pages.. NOT a new window per pushed pages
		global $sid ;
		global $session_chat ;
		global $sessionid ;
		global $dbh ;

		// tack a trailing space for a quick fix for cases like
		// url:http://www.osicodes.com WITH NO trailing space.
		$string .= " " ;
		
		preg_match( "/(url|image|push):(http.*?)\:/i", $string, $matches ) ;
		if ( isset( $matches[2] ) )
			$url_prefix = $matches[2] ;
		else
			$url_prefix = "http" ;
		// add personal touch with %%user%% variable
		if ( $session_chat[$sid]['isadmin'] )
		{
			$string = preg_replace( "/%%user%%/", $session_chat[$sid]['visitor_name'], $string ) ;
			$string = preg_replace( "/%%operator%%/", $session_chat[$sid]['admin_name'], $string ) ;
		}

		// url:
		$string = preg_replace( "/url:($url_prefix:\/\/|)(.*?)( |<br>)/i", "<a href=\"JavaScript:void(0)\" OnClick=\"window.open('$url_prefix://\\2', 'admin', 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1')\">$url_prefix://\\2</a> ", $string ) ;
		// image:
		$string = preg_replace( "/image:($url_prefix:\/\/|)(.*?)( |<br>)/i", "<img src=$url_prefix://\\2> ", $string ) ;
		// email:
		$string = preg_replace( "/email:(.*?)( |<br>)/i", "<a href=mailto:\\1>\\1</a> ", $string ) ;
		// push:
		if ( $string = preg_replace( "/push:($url_prefix:\/\/|)(.*?)( |<br>)/i", "<push$sessionid $url_prefix://\\2 >[ PUSHING webpage <a href=$url_prefix://\\2 target=new>$url_prefix://\\2</a> ]", $string ) )
		{
			// if user pushes a page, then we buffer the active time to the future a bit to make up for the
			// stalled browser as it opens up the new window.  let's just put around 15 seconds for now
			$future_buffer = 15 ;	// seconds
			ServiceChat_update_ChatActivityTime( $dbh, $session_chat[$sid]['screen_name'], $sessionid, $future_buffer ) ;
		}

		return $string ;
	}
?>