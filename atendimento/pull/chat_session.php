<?php
	session_start() ;
	$j_string = $action = "" ;
	$session_chat = $_SESSION['session_chat'] ;
	$sid = ( isset( $_GET['sid'] ) ) ? $_GET['sid'] : "" ;
	$sessionid = ( isset( $_GET['sessionid'] ) ) ? $_GET['sessionid'] : "" ;
	$requestid = ( isset( $_GET['requestid'] ) ) ? $_GET['requestid'] : "" ;
	$start = ( isset( $_GET['start'] ) ) ? $_GET['start'] : "" ;
	$action = ( isset( $_GET['action'] ) ) ? $_GET['action'] : "" ;

	if ( !file_exists( "../web/".$session_chat[$sid]['asp_login']."/".$session_chat[$sid]['asp_login']."-conf-init.php" ) || !file_exists( "../web/conf-init.php" ) )
	{
		print "<font color=\"#FF0000\">[Configuration Error: config files not found!] Exiting pull/chat_session.php ...</font>" ;
		exit ;
	}
	include_once("../web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("$DOCUMENT_ROOT/web/".$session_chat[$sid]['asp_login']."/".$session_chat[$sid]['asp_login']."-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/Util.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/remove.php") ;

	if ( $action == "submit" )
	{
		$message = stripslashes( $_GET['message'] ) ;
		// below was modify submitted by member Onion - but it seems to work without on latest browsers
		//$message = stripslashes( iconv( "UTF-8", "GBK", $_GET['message'] ) ) ;
		$message = preg_replace( "/</", "&lt;", $message ) ;
		$message = preg_replace( "/>/", "&gt;", $message ) ;
		$message = preg_replace( "/-br-/", "<br>", $message ) ;
		$timestamp = date( "$TIMEZONE_FORMAT:i:s$TIMEZONE_AMPM", ( time() + $TIMEZONE ) ) ;

		// put in admin commands IF session is admin
		if ( $session_chat[$sid]['isadmin'] )
			$message = UtilChat_ParseForCommands( $message ) ;
		else
		{
			// see if the operator is still the same.  if the chat has been
			// transferred, we want to make sure we update the admin name
			$session_parties = ServiceChat_get_ChatSessionLogins( $dbh, $sessionid ) ;
			if ( isset ( $session_parties['admin'] ) && ( $session_parties['admin'] != $_SESSION['session_chat'][$sid]['admin_name'] ) )
			{
				$_SESSION['session_chat'][$sid]['admin_name'] = $session_parties['admin'] ;
				$session_chat[$sid]['admin_name'] = $session_parties['admin'] ;
				$reload_options = 1 ;
			}
		}

		$put_string = "$message<br$sessionid>" ;

		// reverse if op2op
		if ( $session_chat[$sid]['op2op'] )
		{
			$transcript_string = "<p class=\"client\"><span>".$_SESSION['session_chat'][$sid]['admin_name']."<ts ($timestamp) ts>:</span> $message</p><br$sessionid>" ;
			if ( $session_chat[$sid]['isadmin'] )
				$transcript_string = "<p class=\"operator\"><span>".$_SESSION['session_chat'][$sid]['visitor_name']."<ts ($timestamp) ts>:</span> $message</p><br$sessionid>" ;
		}
		else
		{
			$transcript_string = "<p class=\"client\"><span>".$_SESSION['session_chat'][$sid]['visitor_name']."<ts ($timestamp) ts>:</span> $message</p><br$sessionid>" ;
			if ( $session_chat[$sid]['isadmin'] )
				$transcript_string = "<p class=\"operator\"><span>".$_SESSION['session_chat'][$sid]['admin_name']."<ts ($timestamp) ts>:</span> $message</p><br$sessionid>" ;
		}

		UtilChat_AppendToChatfile( $session_chat[$sid]['chatfile_put'], $put_string ) ;
		UtilChat_AppendToChatfile( $session_chat[$sid]['chatfile_transcript'], $transcript_string ) ;

		if ( file_exists( "$DOCUMENT_ROOT/web/chatsessions/w_".$session_chat[$sid]['chatfile_put'] ) )
			unlink( "$DOCUMENT_ROOT/web/chatsessions/w_".$session_chat[$sid]['chatfile_put'] ) ;
	}
	else if ( $action == "exit" )
	{
		ServiceChat_remove_ChatSessionListByScreenName( $dbh, $sessionid, $session_chat[$sid]['screen_name'] ) ;
		// dump the chat into a session because the transcript file will be removed
		if ( file_exists( "$DOCUMENT_ROOT/web/chatsessions/".$session_chat[$sid]['chatfile_transcript'] ) )
		{
			$chat_transcript_file = file( "$DOCUMENT_ROOT/web/chatsessions/".$session_chat[$sid]['chatfile_transcript'] ) ;
			$_SESSION['session_chat'][$sid]['transcript'] = $chat_transcript_file[0] ;
		}
		// let's remove chat initiate flag if it exists
		if ( file_exists( "$DOCUMENT_ROOT/web/chatrequests/".$session_chat[$sid]['initiate'] ) && $session_chat[$sid]['initiate'] )
			unlink( "$DOCUMENT_ROOT/web/chatrequests/".$session_chat[$sid]['initiate'] ) ;
		if ( file_exists( "$DOCUMENT_ROOT/web/chatsessions/w_".$session_chat[$sid]['chatfile_put'] ) )
			unlink( "$DOCUMENT_ROOT/web/chatsessions/w_".$session_chat[$sid]['chatfile_put'] ) ;

		Header( "Content-type: image/gif" ) ;
		readfile( "$DOCUMENT_ROOT/images/empty_nodelete.gif" ) ;
	}

	// see if chat file contains data... if so, then get it and put it on their screen
	if ( file_exists( "$DOCUMENT_ROOT/web/chatsessions/".$session_chat[$sid]['chatfile_get'] ) && !is_dir( "$DOCUMENT_ROOT/web/chatsessions/".$session_chat[$sid]['chatfile_get'] ) )
	{
		$data = file( "$DOCUMENT_ROOT/web/chatsessions/".$session_chat[$sid]['chatfile_get'] ) ;
		$string_to_write = join( "", $data ) ;

		// let's delete the file so we know we got the data and is now ready for new data
		unlink( "$DOCUMENT_ROOT/web/chatsessions/".$session_chat[$sid]['chatfile_get'] ) ;
		// remove the write flag file
		if ( file_exists( "$DOCUMENT_ROOT/web/chatsessions/w_".$session_chat[$sid]['chatfile_put'] ) )
			unlink( "$DOCUMENT_ROOT/web/chatsessions/w_".$session_chat[$sid]['chatfile_put'] ) ;

		$j_string = $string_to_write ;
		if ( !preg_match( "/window.parent.frames/", $j_string ) )
			$j_string = preg_replace( "/'/", "&#039;", $j_string ) ;
		print $j_string ;
	}
?>