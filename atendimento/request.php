<?php
	session_start() ;
	$page = $action = $ip = $page = $error = $userid = $resolution = $datetime = $question = $op2op = $from_screen_name = $email = $sessionid = "" ;
	$x = $deptid = $method = $messageid = 0 ;
	$remote_addr = $_SERVER['REMOTE_ADDR'] ;
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_GET['deptid'] ) ) { $deptid = $_GET['deptid'] ; }
	if ( isset( $_POST['deptid'] ) ) { $deptid = $_POST['deptid'] ; }
	if ( isset( $_POST['pagex'] ) ) { $page = $_POST['pagex'] ; }
	if ( isset( $_GET['pagex'] ) ) { $page = $_GET['pagex'] ; }
	if ( isset( $_POST['l'] ) ) { $l = $_POST['l'] ; }
	if ( isset( $_GET['l'] ) ) { $l = $_GET['l'] ; }
	if ( isset( $_POST['x'] ) ) { $x = $_POST['x'] ; }
	if ( isset( $_GET['x'] ) ) { $x = $_GET['x'] ; }
	if ( isset( $_POST['sessionid'] ) ) { $sessionid = $_POST['sessionid'] ; }
	if ( isset( $_GET['sessionid'] ) ) { $sessionid = $_GET['sessionid'] ; }
	if ( isset( $_POST['requestid'] ) ) { $requestid = $_POST['requestid'] ; }
	if ( isset( $_GET['requestid'] ) ) { $requestid = $_GET['requestid'] ; }
	if ( isset( $_POST['ip'] ) ) { $ip = $_POST['ip'] ; }
	if ( isset( $_GET['ip'] ) ) { $ip = $_GET['ip'] ; }
	if ( isset( $_POST['userid'] ) ) { $userid = $_POST['userid'] ; }
	if ( isset( $_GET['userid'] ) ) { $userid = $_GET['userid'] ; }
	if ( isset( $_POST['email'] ) ) { $email = $_POST['email'] ; }
	if ( isset( $_GET['email'] ) ) { $email = $_GET['email'] ; }
	if ( isset( $_POST['question'] ) ) { $question = $_POST['question'] ; }
	if ( isset( $_GET['question'] ) ) { $question = $_GET['question'] ; }
	if ( isset( $_POST['display_width'] ) ) { $display_width = $_POST['display_width'] ; }
	if ( isset( $_GET['display_width'] ) ) { $display_width = $_GET['display_width'] ; }
	if ( isset( $_POST['display_height'] ) ) { $display_height = $_POST['display_height'] ; }
	if ( isset( $_GET['display_height'] ) ) { $display_height = $_GET['display_height'] ; }
	if ( isset( $_POST['datetime'] ) ) { $datetime = $_POST['datetime'] ; }
	if ( isset( $_GET['datetime'] ) ) { $datetime = $_GET['datetime'] ; }
	if ( isset( $_POST['from_screen_name'] ) ) { $from_screen_name = $_POST['from_screen_name'] ; }
	if ( isset( $_GET['from_screen_name'] ) ) { $from_screen_name = $_GET['from_screen_name'] ; }
	$method = ( isset( $_GET['method'] ) ) ? $_GET['method'] : 0 ;
	$messageid = ( isset( $_GET['messageid'] ) ) ? $_GET['messageid'] : "" ;
	$op2op = ( isset( $_GET['op2op'] ) ) ? $_GET['op2op'] : "" ;

	// initialize
	if ( !isset( $_SESSION['session_chat'] ) )
	{
		session_register( "session_chat" ) ;
		$session_chat = ARRAY() ;
		$_SESSION['session_chat'] = ARRAY() ;
	}

	include_once( "./API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( ".", $l ) || !isset( $_SESSION['session_chat'] ) )
	{
		if ( preg_match( "/(chatsupportlive.com)|(osicode.com)|(osicodes.com)|(osicodes.net)|(phplivesupport.com)/", $_SERVER['SERVER_NAME'] ) )
		{
			HEADER( "location: http://www.codedeli.com/ad.php" ) ;
			exit ;
		}
		else
		{
			print "<font color=\"#FF0000\">[Configuration Error: config files not found!] Exiting... [request.php]</font>" ;
			exit ;
		}
	}
	include_once("./web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("./web/$l/$l-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/API/Util_Error.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Util.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/Util.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/put.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/remove.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/update.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/update.php") ;
	include_once("$DOCUMENT_ROOT/API/Logs/remove.php") ;
	include_once("$DOCUMENT_ROOT/API/Footprint_unique/remove.php") ;
	include_once("$DOCUMENT_ROOT/API/ASP/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Canned/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Spam/get.php") ;

	// initialize
	// this is used to generate a unique chat session file. it is used to reference
	// this new chat session.
	$sid = time() ;

	$session_ended = $session_busy = 0 ;
	// update all admins status to not available if they have been idle
	AdminUsers_update_IdleAdminStatus( $dbh, $admin_idle ) ;
	// lets delete old transcripts if deptid is passed
	// this also remove other old files... look in function
	ServiceLogs_remove_DeptExpireTranscripts( $dbh, $deptid, $x ) ;
	// delete old unique footprints used for initiate request tracking
	ServiceFootprintUnique_remove_IdleFootprints( $dbh, $x ) ;
	$aspinfo = AdminASP_get_UserInfo( $dbh, $x ) ;

	if ( !$aspinfo['active_status'] )
	{
		if ( preg_match( "/(chatsupportlive.com)|(osicode.com)|(osicodes.com)|(osicodes.net)|(phplivesupport.com)/", $_SERVER['SERVER_NAME'] ) )
		{
			HEADER( "location: http://www.codedeli.com/ad.php" ) ;
			exit ;
		}
		else
		{
			print "
			<html><head>
			<title>RESTRICTION ERROR</title>
			<!-- copyright OSI Codes Inc. http://www.osicodes.com [DO NOT DELETE] -->
			<link rel=\"Stylesheet\" href=\"./css/base.css\"></head><body>
			<span class=basetxt><big><b>RESTRICTED</b></big><p>
			Service inactive.  Please contact <a href=\"mailto:$aspinfo[contact_email]\">$aspinfo[contact_email]</a> for additional information.<p>
			<hr>
			<span class=smalltxt>$LANG[DEFAULT_BRANDING] &copy; OSI Codes Inc.</span>
			</body></html>
			" ;
		}
		exit ;
	}

	$blocked = 0 ;
	$ips = ServiceSpam_get_IPs( $dbh, $x ) ;
	for ( $c = 0; $c < count( $ips ); ++$c )
	{
		$ipblock = $ips[$c] ;
		if ( $ipblock['ip'] == $remote_addr )
		{
			$blocked = 1 ;
			break ;
		}
	}

	// check to see if chat is initiated by admin... if so, then take them to the initiated
	// chat, not to requesting chat.
	$remote_addr = $_SERVER['REMOTE_ADDR'] ;
	if ( ( file_exists( "$DOCUMENT_ROOT/web/chatrequests/$remote_addr.scr" ) || file_exists( "$DOCUMENT_ROOT/web/chatrequests/$remote_addr.pop" ) ) && $remote_addr )
		$action = "initiate_accept" ;

	$THEME = ( isset( $_GET['theme'] ) && $_GET['theme'] ) ? $_GET['theme'] : $THEME ;
	$theme = ( isset( $_GET['theme'] ) && $_GET['theme'] ) ? $_GET['theme'] : "" ;

	if ( file_exists( "web/$l/$LOGO" ) && $LOGO )
		$logo = "web/$l/$LOGO" ;
	else if ( file_exists( "web/$LOGO_ASP" ) && $LOGO_ASP )
		$logo = "web/$LOGO_ASP" ;
	else if ( file_exists( "themes/$THEME/images/logo.gif" ) )
		$logo = "themes/$THEME/images/logo.gif" ;
	else
		$logo = "images/logo.gif" ;

	// conditions

	$from_screen_name = Util_Format_ConvertSpecialChars( strip_tags( $from_screen_name ) ) ;
	$email = Util_Format_ConvertSpecialChars( strip_tags( $email ) ) ;

	if ( ( $action == "request" ) && !$blocked )
	{
		// set cookie for later use (180 days)
		$cookie_lifespan = time() + 60*60*24*180 ;
		if ( $from_screen_name != "offline" )
		{
			setcookie( "COOKIE_PHPLIVE_VLOGIN", stripslashes( $from_screen_name ), $cookie_lifespan ) ;
			setcookie( "COOKIE_PHPLIVE_VEMAIL", stripslashes( $email ), $cookie_lifespan ) ;
		}

		$admin = AdminUsers_get_LessLoadedDeptUser( $dbh, $deptid, "", $x ) ;
		$deptinfo = AdminUsers_get_DeptInfo( $dbh, $deptid, $x ) ;

		if ( isset( $admin['userID'] ) && ( $admin['available_status'] == 1 ) && ( $admin['last_active_time'] > $admin_idle ) && ( $admin['aspID'] == $aspinfo['aspID'] ) )
		{
			$resolution = "" ;
			if( $display_width && $display_height )
				$resolution = "$display_width x $display_height" ;

			$from_screen_name = stripslashes( "<o$sid>$from_screen_name" ) ;
			$question = stripslashes( $question ) ;
			$question = preg_replace( "/</", "&lt;", $question ) ;
			$question = preg_replace( "/>/", "&gt;", $question ) ;
			$question = preg_replace( "/'/", "&#039;", $question ) ;
			$question = preg_replace( "/\"/", "&quot;", $question ) ;

			// the zero (0) at the put_ChatSession is a flag to tell if the chatsession has
			// been initiated by admin or by visitor.  0 is visitor, ip or php session var is
			// used if initiated by operator admin.
			$sessionid = ServiceChat_put_ChatSession( $dbh, $from_screen_name, 0 ) ;
			$requestid = ServiceChat_put_ChatRequest( $dbh, $admin['userID'], $deptid, 0, $from_screen_name, $email, $sessionid, $resolution, $datetime, $page, $aspinfo['aspID'], $question, 0, $remote_addr, $_SERVER['HTTP_USER_AGENT'] ) ;
			ServiceChat_put_ChatSessionList( $dbh, $from_screen_name, $sessionid ) ;

			if ( $sessionid && $requestid )
			{
				// initialize session settings
				UtilChat_InitializeChatSession( $sid, $sessionid, " AND chat_admin.userID <> $admin[userID]", $from_screen_name, $admin['name'], $from_screen_name, $admin['userID'], $deptid, "", $aspinfo['aspID'], $aspinfo['login'], 0, 0, $admin['theme'] ) ;

				// chat files goes like:
				// [sessionid]_[visitor OR admin].txt
				$_SESSION['session_chat'][$sid]['chatfile_put'] = $sessionid."_admin.txt" ;
				$_SESSION['session_chat'][$sid]['chatfile_get'] = $sessionid."_visitor.txt" ;
				$_SESSION['session_chat'][$sid]['chatfile_transcript'] = $sessionid."_transcript.txt" ;

				if ( !file_exists( "./web/chatsessions/".$_SESSION['session_chat'][$sid]['chatfile_put'] ) )
				{
					$total_requests = ServiceChat_get_TotalChatRequests( $dbh, $x ) - 1 ;
					$status_string = "" ;
					if ( ( $total_requests > 0 ) && $deptinfo['show_que'] )
						$status_string .= "<br><br>Currently <b>$total_requests</b> chat request session(s) before you. " ;

					$date = date( "D m/d/y $TIMEZONE_FORMAT:i$TIMEZONE_AMPM", ( time() + $TIMEZONE ) ) ;
					$timestamp = date( "$TIMEZONE_FORMAT:i:s$TIMEZONE_AMPM", ( time() + $TIMEZONE ) ) ;
					$greeting = preg_replace( "/%%user%%/", $from_screen_name, Util_Format_ConvertSpecialChars( $deptinfo['greeting'] ) ) ;
					$greeting = preg_replace( "/%%date%%/", $date, $greeting ) ;
					$greeting = preg_replace( "/(\r\n)/", "", nl2br( $greeting ) ) ;
					$question = preg_replace( "/(\r\n)/", "", nl2br( $question ) ) ;

					$transcript_string = "<p class=\"notice\">$from_screen_name<ts ($timestamp) ts>: <question>$question</question> <admin_strip><br><br>$greeting$status_string</admin_strip></p><br$sessionid>" ;
					UtilChat_AppendToChatfile( $_SESSION['session_chat'][$sid]['chatfile_transcript'], $transcript_string ) ;
				}
			}
			else
				$error = "E1: Chat session did not create (SID: $sessionid - RID: $requestid)" ;
		}
		else
		{
			$_SESSION['session_chat'][$sid]['question'] = $question ;
			HEADER( "location: message_box.php?l=$l&x=$x&deptid=$deptid&sid=$sid" ) ;
			exit ;
		}
	}
	else if ( $action == "initiate" )
	{
		// first, let's see if they are already on support (initiated by another admin or
		// they request support)... if so, we don't want to initiate again.
		$ip_requestinfo = ServiceChat_get_IPChatRequestInfo( $dbh, $x, $ip ) ;
		$page = urldecode( $page ) ;
		if ( isset( $ip_requestinfo['status'] ) && $ip_requestinfo['status'] )
			$session_busy = 1 ;
		else
		{
			$admin = AdminUsers_get_UserInfo( $dbh, $userid, $x ) ;
			$deptinfo = AdminUsers_get_DeptInfo( $dbh, $deptid, $x ) ;

			// we don't check for online or idle status because the chat is being initiated.  so
			// operator can be offline status and still initiate chat.
			if ( $admin['aspID'] == $aspinfo['aspID'] )
			{
				$from_screen_name = "<o$sid>Visitor" ;
				$cannedinfo = ServiceCanned_get_CannedInfo( $dbh, $messageid, $admin['userID'] ) ;
				$question = $cannedinfo['message'] ;

				$sessionid = ServiceChat_put_ChatSession( $dbh, "Visitor", $ip ) ;
				$requestid = ServiceChat_put_ChatRequest( $dbh, $admin['userID'], $deptid, 0, $from_screen_name, "", $sessionid, $resolution, $datetime, $page, $x, $question, 4, $ip, "" ) ;
				ServiceChat_put_ChatSessionList( $dbh, $admin['name'], $sessionid ) ;

				if ( $sessionid && $requestid )
				{
					// chat files goes like:
					// [sessionid]_[visitor OR admin].txt
					$_SESSION['session_chat'][$sid]['chatfile_put'] = $sessionid."_visitor.txt" ;
					$_SESSION['session_chat'][$sid]['chatfile_get'] = $sessionid."_admin.txt" ;
					$_SESSION['session_chat'][$sid]['chatfile_transcript'] = $sessionid."_transcript.txt" ;
					$transcript_data_file = $sessionid."_transcript_info.txt" ;
					$transcript_data = $admin['userID']."<:><initiated>$from_screen_name<:>$email<:>".$deptid."<:>$x<:>" ;

					$pic_string = "" ;
					if ( $admin['pic'] )
						$pic_string = "<br><img src=$BASE_URL/web/$l/$admin[pic]> " ;
					$string = "$admin[name]: $question$pic_string" ;

					if ( !file_exists( "./web/chatsessions/".$_SESSION['session_chat'][$sid]['chatfile_put'] ) )
					{
						$timestamp = date( "$TIMEZONE_FORMAT:i:s$TIMEZONE_AMPM", ( time() + $TIMEZONE ) ) ;
						$transcript_string = "<initiated><p class=\"notice\">$string</p><br$sessionid><p class=\"notice\">Please hold while connecting...</p><br$sessionid>" ;
						UtilChat_AppendToChatfile( $_SESSION['session_chat'][$sid]['chatfile_transcript'], $transcript_string ) ;
						UtilChat_AppendToChatfile( $transcript_data_file, $transcript_data ) ;

						UtilChat_InitializeChatSession( $sid, $sessionid, "", $admin['name'], $admin['name'], $from_screen_name, $admin['userID'], $deptid, $ip, $x, $l, 1, 0, $admin['theme'] ) ;

						// now let's put the chat request so the visitor is notified.  we use a flat text
						// file instead of database because it is much faster access time then calling
						// db just to get request.
						if ( !$method )
							$method = 0 ;
						$initiate_file = "$ip.pop" ;
						if ( $method )
							$initiate_file = "$ip.scr" ;
	
						$initiate_string = "$requestid\n$sessionid\n$admin[userID]\n$method" ;
						$fp = fopen( "$DOCUMENT_ROOT/web/chatrequests/$initiate_file", "wb+" ) ;
						fwrite( $fp, $initiate_string, strlen( $initiate_string ) ) ;
						fclose( $fp ) ;
					}
				}
				else
					$error = "E2: Chat session did not create." ;
			}
			else
			{
				HEADER( "location: message_box.php?l=$l&x=$x&deptid=$deptid" ) ;
				exit ;
			}
		}
	}
	else if ( $action == "op2op" )
	{
		$question = "<question>[ Operator-to-Operator Request ]</question>" ;
		$admin = AdminUsers_get_UserInfo( $dbh, $userid, $x ) ;
		$admin_op2op = AdminUsers_get_UserInfo( $dbh, $op2op, $x ) ;
		$from_screen_name = stripslashes( $admin['name'] ) ;

		if ( isset( $admin_op2op['userID'] ) && ( $admin_op2op['available_status'] == 1 ) && ( $admin_op2op['last_active_time'] > $admin_idle ) && ( $admin_op2op['aspID'] == $admin_op2op['aspID'] ) )
		{
			$resolution = "" ;
			$from_screen_name = "<o$sid>$from_screen_name" ;

			// the zero (0) at the put_ChatSession is a flag to tell if the chatsession has
			// been initiated by admin or by visitor.  0 is visitor, ip or php session var is
			// used if initiated by operator admin.
			$sessionid = ServiceChat_put_ChatSession( $dbh, $from_screen_name, 0 ) ;
			$requestid = ServiceChat_put_ChatRequest( $dbh, $admin_op2op['userID'], $deptid, 0, $from_screen_name, $admin_op2op['email'], $sessionid, $resolution, $datetime, $page, $aspinfo['aspID'], $question, 0, $remote_addr, $_SERVER['HTTP_USER_AGENT'] ) ;
			ServiceChat_put_ChatSessionList( $dbh, $from_screen_name, $sessionid ) ;

			if ( $sessionid && $requestid )
			{
				// initialize session settings
				UtilChat_InitializeChatSession( $sid, $sessionid, " AND chat_admin.userID <> $admin_op2op[userID]", $from_screen_name, $admin_op2op['name'], $from_screen_name, $admin_op2op['userID'], $deptid, "", $aspinfo['aspID'], $aspinfo['login'], 1, 1, $admin['theme'] ) ;

				// chat files goes like:
				// [sessionid]_[visitor OR admin].txt
				$_SESSION['session_chat'][$sid]['chatfile_put'] = $sessionid."_admin.txt" ;
				$_SESSION['session_chat'][$sid]['chatfile_get'] = $sessionid."_visitor.txt" ;
				$_SESSION['session_chat'][$sid]['chatfile_transcript'] = $sessionid."_transcript.txt" ;

				if ( !file_exists( "./web/chatsessions/".$_SESSION['session_chat'][$sid]['chatfile_put'] ) )
				{
					$date = date( "D m/d/y $TIMEZONE_FORMAT:i$TIMEZONE_AMPM", ( time() + $TIMEZONE ) ) ;
					$timestamp = date( "$TIMEZONE_FORMAT:i:s$TIMEZONE_AMPM", ( time() + $TIMEZONE ) ) ;

					$transcript_string = "<p class=\"operator\"><span>$from_screen_name<ts ($timestamp) ts>:</span> $question</p><br$sessionid>" ;
					UtilChat_AppendToChatfile( $_SESSION['session_chat'][$sid]['chatfile_transcript'], $transcript_string ) ;
				}
			}
			else
				$error = "E3: Chat session did not create." ;
		}
		else
		{
			HEADER( "location: message_box.php?l=$l&x=$x&deptid=$deptid" ) ;
			exit ;
		}
	}
	else if ( ( $action == "initiate_accept" ) && ( file_exists( "$DOCUMENT_ROOT/web/chatrequests/$remote_addr.scr" ) || file_exists( "$DOCUMENT_ROOT/web/chatrequests/$remote_addr.pop" ) ) && $remote_addr )
	{
		$initiate_file = "$remote_addr.pop" ;
		if ( file_exists( "$DOCUMENT_ROOT/web/chatrequests/$remote_addr.scr" ) )
			$initiate_file = "$remote_addr.scr" ;

		$requestarray = file( "$DOCUMENT_ROOT/web/chatrequests/$initiate_file" ) ;
		$requestinfo = ServiceChat_get_ChatRequestInfo( $dbh, rtrim( $requestarray[0] ) ) ;
		$aspinfo = AdminASP_get_UserInfo( $dbh, $requestinfo['aspID'] ) ;
		$requestid = $requestinfo['requestID'] ;
		$sessionid = $requestinfo['sessionID'] ;

		// now remove the initiate file so it does not popup chat box again if they decide to browse to
		// another webpage.
		unlink( "$DOCUMENT_ROOT/web/chatrequests/$initiate_file" ) ;
		if ( isset( $requestinfo['requestID'] ) && $requestinfo['requestID'] )
		{
			$admin = AdminUsers_get_UserInfo( $dbh, $requestinfo['userID'], $aspinfo['aspID'] ) ;
			$sessioninfo = ServiceChat_get_ChatSessionInfo( $dbh, $requestinfo['sessionID'] ) ;
			$department = AdminUsers_get_DeptInfo( $dbh, $requestinfo['deptID'], $aspinfo['aspID'] ) ;
			ServiceChat_put_ChatSessionList( $dbh, $requestinfo['from_screen_name'], $requestinfo['sessionID'] ) ;

			$timestamp = date( "$TIMEZONE_FORMAT:i:s$TIMEZONE_AMPM", ( time() + $TIMEZONE ) ) ;

			// chat files goes like:
			// [sessionid]_[visitor OR admin].txt
			$_SESSION['session_chat'][$sid]['chatfile_put'] = $sessioninfo['sessionID']."_admin.txt" ;
			$_SESSION['session_chat'][$sid]['chatfile_get'] = $sessioninfo['sessionID']."_visitor.txt" ;
			$_SESSION['session_chat'][$sid]['chatfile_transcript'] = $sessionid."_transcript.txt" ;

			$string = "window.parent.frames[\"main\"].window.addMessage( \"Visitor has joined.\", \"\", \"notice\", \"receive\" ) ;<br$sessionid>" ;
			$transcript_string = "<p class=\"notice\">Visitor has joined.<ts ($timestamp) ts></p><br$sessionid>" ;
			UtilChat_AppendToChatfile( $_SESSION['session_chat'][$sid]['chatfile_put'], $string ) ;
			UtilChat_AppendToChatfile( $_SESSION['session_chat'][$sid]['chatfile_transcript'], $transcript_string ) ;

			UtilChat_InitializeChatSession( $sid, $sessionid, "", $requestinfo['from_screen_name'], $admin['name'], $requestinfo['from_screen_name'], $requestinfo['userID'], $requestinfo['deptID'], "", $aspinfo['aspID'], $aspinfo['login'], 0, 0, $admin['theme'] ) ;
		}
		else
			$session_ended = 1 ;
	}
	else if ( $action == "accept" )
	{
		$admin = AdminUsers_get_UserInfo( $dbh, $userid, $x ) ;
		$requestinfo = ServiceChat_get_ChatRequestInfo( $dbh, $requestid ) ;
		$question = strip_tags( stripslashes( $requestinfo['question'] ) ) ;
		$question = preg_replace( "/(\r\n)/", "", nl2br( $question ) ) ;
		$from_screen_name = strip_tags( stripslashes( $requestinfo['from_screen_name'] ) ) ;

		// grab the visitor chat $sid from the screen_name
		$visitor_sid = "no sid" ;
		preg_match( "/<o(.*)>/", $requestinfo['from_screen_name'], $matches ) ;
		if ( isset( $matches[1] ) )
			$visitor_sid = $matches[1] ;

		// if the request still exist, then let's go ahead and start the session
		// if NOT exist (party has left or they were taken to "leave a message" form),
		// then let's put a message that the session has ended
		if ( isset( $requestinfo['requestID'] ) && $requestinfo['requestID'] && !$requestinfo['status'] )
		{
			$sid = time() ;
			$sessionid = $requestinfo['sessionID'] ;
			$sessioninfo = ServiceChat_get_ChatSessionInfo( $dbh, $requestinfo['sessionID'] ) ;
			$department = AdminUsers_get_DeptInfo( $dbh, $requestinfo['deptID'], $x ) ;
			ServiceChat_put_ChatSessionList( $dbh, $admin['name'], $requestinfo['sessionID'] ) ;
			ServiceChat_update_ChatRequestValue( $dbh, $requestid, "status", 1 ) ;
			ServiceChat_update_ChatRequestLogStatus( $dbh, $requestinfo['sessionID'], 1 ) ;

			$timestamp = date( "$TIMEZONE_FORMAT:i:s$TIMEZONE_AMPM", ( time() + $TIMEZONE ) ) ;
			$op2op = ( preg_match( "/\[ Operator-to-Operator Request \]/", $requestinfo['question'] ) ) ? 1 : 0 ;

			// chat files goes like:
			// [sessionid]_[visitor OR admin].txt
			$_SESSION['session_chat'][$sid]['chatfile_put'] = $sessioninfo['sessionID']."_visitor.txt" ;
			$_SESSION['session_chat'][$sid]['chatfile_get'] = $sessioninfo['sessionID']."_admin.txt" ;
			$_SESSION['session_chat'][$sid]['chatfile_transcript'] = $sessionid."_transcript.txt" ;

			$pic_string = "" ;
			if ( $admin['pic'] && !$op2op )
				$pic_string = "<br><img src=$BASE_URL/web/$l/$admin[pic]> " ;

			if ( $op2op )
			{
				$string = "$from_screen_name: $question<br><br>$LANG[CHAT_REQUEST_NOWSPEAKINGWITH] <b>$admin[name]</b>.$pic_string" ;
				$transcript_string = "$LANG[CHAT_REQUEST_NOWSPEAKINGWITH] <b>$admin[name]</b>.$pic_string" ;
			}
			else
			{
				$string = "$from_screen_name: $question<br><br>$LANG[CHAT_REQUEST_NOWSPEAKINGWITH] <b>$admin[name]</b> of <b>$department[name]</b>.$pic_string" ;
				$transcript_string = "$LANG[CHAT_REQUEST_NOWSPEAKINGWITH] <b>$admin[name]</b> of <b>$department[name]</b>.$pic_string" ;
			}

			if ( $op2op || !$requestinfo['tstatus'] || ( $requestinfo['tstatus'] && !$requestinfo['status'] ) )
			{
				if ( ( $requestinfo['tstatus'] == 1 ) && ( $requestinfo['tflag'] == 3 ) || $op2op )
				{
					$visitor_string = "window.parent.frames[\"main\"].window.addMessage( \"$transcript_string\", \"\", \"notice\", \"\" ) ;<br$sessionid>" ;
				}
				else
				{
					// refresh the visitor's chat window if call is picked up - replace the
					// wait intro message
					$string = urlencode( $string ) ;
					// update is typing to reflect the new operator
					if ( $requestinfo['tstatus'] )
						$visitor_string = "window.parent.frames[\"main\"].window.write_typing('$admin[name]');<br$sessionid>window.parent.frames[\"main\"].window.frames[\"fmain\"].location.href = \"files/nodelete_chat.php?text=$string&sid=$visitor_sid&admin_id=$admin[userID]\";<br$sessionid><respawn$sessionid><br$sessionid>" ;
					else
						$visitor_string = "window.parent.frames[\"main\"].window.frames[\"fmain\"].location.href = \"files/nodelete_chat.php?text=$string&sid=$visitor_sid\";<br$sessionid><respawn$sessionid><br$sessionid>" ;
				}
			}
			else
				$visitor_string = $string ;

			UtilChat_AppendToChatfile( $_SESSION['session_chat'][$sid]['chatfile_put'], $visitor_string ) ;
			UtilChat_AppendToChatfile( $_SESSION['session_chat'][$sid]['chatfile_transcript'], "<p class=\"notice\">$transcript_string<ts ($timestamp) ts></p><br$sessionid>" ) ;

			$transcript_data_file = $requestinfo['sessionID']."_transcript_info.txt" ;
			$transcript_data = $admin['userID']."<:>".$requestinfo['from_screen_name']."<:>".$requestinfo['email']."<:>".$requestinfo['deptID']."<:>".$x."<:>" ;
			if ( !file_exists( "$DOCUMENT_ROOT/web/chatsessions/$transcript_data_file" ) )
				UtilChat_AppendToChatfile( $transcript_data_file, $transcript_data ) ;

			// always set $op2op variable (last one) to 0 on this one so we know which operator
			// was the initiator
			$isadmin = ( $op2op ) ? 0 : 1 ;
			UtilChat_InitializeChatSession( $sid, $requestinfo['sessionID'], "", $admin['name'], $admin['name'], $sessioninfo['screen_name'], $admin['userID'], $requestinfo['deptID'], "", $x, $l, $isadmin, $op2op, $admin['theme'] ) ;
			AdminUsers_update_UserDeptActvyTime( $dbh, $admin['userID'], $requestinfo['deptID'] ) ;
		}
		else
			$session_ended = 1 ;
	}
	else
	{
		ServiceChat_remove_CleanChatSessionList( $dbh ) ;
		ServiceChat_remove_CleanChatSessions( $dbh ) ;
		ServiceChat_remove_CleanChatRequests( $dbh ) ;

		$department = AdminUsers_get_DeptInfo( $dbh, $deptid, $x ) ;
		if ( isset( $department['deptID'] ) )
		{
			$total = AdminUsers_get_TotalDepartmentUsersOnline( $dbh, $deptid ) ;
			// the $page == "message" is forced because it is viewing example
			// of the page (from management department area in setup area)
			if ( !$total || ( $page == "message" ) || $blocked )
			{
				HEADER( "location: message_box.php?theme=$theme&l=$l&x=$x&deptid=$deptid" ) ;
				exit ;
			}
		}
		else
		{
			$departments = AdminUsers_get_AllDepartments( $dbh, $x, 0 ) ;
			$total = count( $departments ) ;
			if ( count( $departments ) == 1 )
			{
				if ( ( !$total = AdminUsers_get_TotalDepartmentUsersOnline( $dbh, $departments[0]['deptID'] ) ) || $blocked )
				{
					$location = "message_box.php?theme=$theme&l=$l&x=$x&deptid=".$departments[0]['deptID'] ;
					HEADER( "location: $location" ) ;
					exit ;
				}
				else
					$deptid = $departments[0]['deptID'] ;
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $LANG['TITLE_SUPPORTREQUEST'] ?></title>
<script type="text/javascript" src="js/chat_fn.js"></script>
<script type="text/javascript" src="js/xmlhttp.js"></script>
<script type="text/javascript" src="js/global.js"></script>
<script type="text/javascript" language="JavaScript1.2">
<!--

	function init()
	{
		// Check for browser support
		if ( !document.createElement && !document.createElementNS ) self.location.href = "http://www.osicodes.com/demos/phplive/browser.php";
		if ( !initxmlhttp() ) self.location.href = "http://www.osicodes.com/demos/phplive/browser.php?xmlhttp=1";
		open_chat() ;
	}

	window.onload = init;

	var win_width = window.screen.availWidth ;
	var win_height = window.screen.availHeight ;

	var now = new Date() ;
	var day = now.getDate() ;
	var time = ( now.getMonth() + 1 ) + '/' + now.getDate() + '/' +  now.getYear() + ' ' ;

	var hours = now.getHours() ;
	var minutes = now.getMinutes() ;
	var seconds = now.getSeconds() ;

	if (hours > 12){
		time += hours - 12 ;
	}  else
	if (hours > 10){
		time += hours ;
	} else
	if (hours > 0){
		time += "0" + hours ;
	} else
		time = "12" ;

	time += ((minutes < 10) ? ":0" : ":") + minutes ;
	time += ((seconds < 10) ? ":0" : ":") + seconds ;
	time += (hours >= 12) ? " P.M." : " A.M." ;

	function do_submit()
	{
		var dept_checked = 0 ;
		
		if ( document.form.deptid.value )
			dept_checked = 1 ;
		else
		{
			for( c = 0; c < document.form.deptid.length; ++c )
			{
				if ( document.form.deptid[c].checked )
					dept_checked = 1 ;
			}
		}

		if ( ( document.form.from_screen_name.value == "" ) || ( document.form.question.value == "" ) || ( dept_checked == 0 ) )
		{
			alert( "<?php echo $LANG['MESSAGE_BOX_JS_A_ALLFIELDSSUP'] ?>" ) ;
		}
		else if ( document.form.email.value.indexOf("@") == -1 )
		{
			alert( "<?php echo $LANG['MESSAGE_BOX_JS_A_INVALIDEMAIL'] ?>" ) ;
		}
		else
		{
			document.form.display_width.value = win_width ;
			document.form.display_height.value = win_height ;
			document.form.datetime.value = time ;
			document.form.submit() ;
		}
	}

	function open_chat()
	{
		<?php if ( $sessionid && $requestid && !$session_ended ): ?>
			url = "chat.php?sessionid=<?php echo $sessionid ?>&sid=<?php echo $sid ?>&userid=<?php echo $admin['userID'] ?>&requestid=<?php echo $requestid ?>" ;
			location.href = url ;
			parent.window.focus() ;
		<?php elseif ( $session_ended ): ?>
			alert( "<?php echo $LANG['CHAT_PARTYLEFTSESSION'] ?>" ) ;
			parent.window.close() ;
		<?php elseif ( $session_busy ): ?>
			alert( "Party is currently on another support.  Can't initiate.  Window will now close." ) ;
			parent.window.close() ;
		<?php endif ; ?>
	}

	function opennewwin(url)
	{
		window.open(url, "newwin", "scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes") ;
	}

//-->
</script>

<link href="css/layout.css" rel="stylesheet" type="text/css" />
<link href="themes/<?php echo $THEME ?>/style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style1 {
	color: #0000FF;
	font-weight: bold;
}
-->
</style>
</head>

<body>
<form method="post" action="request.php" name="form" id="chatform">
<input type="hidden" name="action" value="request">
<input type="hidden" name="display_width" value="">
<input type="hidden" name="display_height" value="">
<input type="hidden" name="datetime" value="">
<input type="hidden" name="x" value="<?php echo $x ?>">
<input type="hidden" name="l" value="<?php echo $l ?>">
<input type="hidden" name="pagex" value="<?php echo $page ?>">
<div id="main">

	<?php if ( ( $action != "accept" ) && ( $action != "initiate" ) && ( $action != "op2op" ) && ( $action != "initiate_accept" ) ): ?>
	<div id="logo"><img src="<?php echo $logo ?>" alt="" border=0 /></div>
	<?php endif ; ?>


	<?php
		// this page will call itself with action request. if there is error,
		// then print it out and ask them to try again.
		if ( $action == "request" ):
	?>
	<?php if ( $error ): ?>
		<big><font color="#FA2D01"><b><?php echo $error ?></b></font></big>
		<hr>
		<a href="JavaScript:JavaScript:history.go(-1)"><?php echo $LANG['WORD_BACK'] ?></a>
	<?php endif ; ?>


	<?php
		// if comming from admin area (action = accept), then we don't
		// want to desplay the login screen.  the page will jump
		// to chat window automatically.
		elseif ( ( $action == "accept" ) || ( $action == "initiate" ) || ( $action == "op2op" ) || ( $action == "initiate_accept" ) ):
	?>


	<?php
		else:
		$isonline = $deptok = 0 ;
	?>
	<h1><?php echo $LANG['CHAT_REQUEST_TITLE'] ?></h1>

	<?php
		$department = AdminUsers_get_DeptInfo( $dbh, $deptid, $x ) ;
		if ( $department['deptID'] ):
		if ( $total = AdminUsers_get_TotalDepartmentUsersOnline( $dbh, $deptid ) && !$blocked )
			$isonline = $deptok = 1 ;
		print "<input type=\"hidden\" name=\"deptid\" value=\"$department[deptID]\">" ;
	?>
	<br><br>

	<?php
		else:
		$departments = AdminUsers_get_AllDepartments( $dbh, $x, 0 ) ;
	?>
	<p><?php echo $LANG['CHAT_REQUEST_SELECTDEPT'] ?></p>
	
	<div id="deptlist">
	<?php
		for ( $c = 0; $c < count( $departments ); ++$c )
		{
			$department = $departments[$c] ;
			$dept_name = stripslashes( $department['name'] ) ;

			$status_string = "($LANG[WORD_OFFLINE])" ;
			$status_class = "<div class=\"offline\">" ;
			if ( $total = AdminUsers_get_TotalDepartmentUsersOnline( $dbh, $department['deptID'] ) && !$blocked )
			{
				$status_string = "($LANG[WORD_ONLINE]!)" ;
				$status_class = "<div class=\"online\">" ;
				$isonline = 1 ;
			}

			print "$status_class<label for=\"dept$department[deptID]\"><input type=\"radio\" id=\"dept$department[deptID]\"  value=\"$department[deptID]\" name=\"deptid\" value=1 class=\"radio\" /> $dept_name $status_string</label></div>" ;
			$deptok = 1 ;
		}

		if ( !$deptok )
		{
			print "<br><br><big>Please create your departments in the <a href=\"setup/adddept.php\" target=\"new\" OnClick=\"window.close()\">setup area</a>.</big>" ;
		}
	?>
	</div>
	<?php endif ; ?>
	
	<div id="inputarea">
		<fieldset>
			<?php if ( !$isonline ): $button_text = "Submit" ; ?>
				<input type="hidden" name="from_screen_name" value="offline">
				<input type="hidden" name="email" value="-@-.com">
				<input type="hidden" name="question" value="&nbsp;">
				<br>
				<?php if ( $deptok ): ?>
				<input type="button" class="button" name="send" value="<?php echo "$LANG[WORD_SEND] $LANG[WORD_EMAIL]" ?>" onclick="return do_submit();" />
				<?php endif ; ?>
			<?php else: ?>
			<dl>
				<dt><label for="user_name"><?php echo $LANG['WORD_NAME'] ?></label></dt>
				<dd class="textbox"><input type="text" id="user_name" name="from_screen_name"  value="<?php echo isset( $_COOKIE['COOKIE_PHPLIVE_VLOGIN'] ) ? stripslashes( $_COOKIE['COOKIE_PHPLIVE_VLOGIN'] ) : "" ?>" onKeyPress="return noquotes(event)" /></dd>
			</dl>
			
			<dl>
				<dt><label for="email"><?php echo $LANG['WORD_EMAIL'] ?></label></dt>
				<dd class="textbox"><input type="text" id="email" name="email" value="<?php echo ( isset( $_COOKIE['COOKIE_PHPLIVE_VEMAIL'] ) && ( $_COOKIE['COOKIE_PHPLIVE_VEMAIL'] != "-@-.com" ) ) ? stripslashes( $_COOKIE['COOKIE_PHPLIVE_VEMAIL'] ) : "" ?>" /></dd>
			</dl>
			<label for="message"><?php echo ( $LANG['CHAT_REQUEST_QUESTION'] ) ? $LANG['CHAT_REQUEST_QUESTION'] : "What is your question?" ?></label>
			<textarea cols="25" rows="2" name="question" id="message" class="message1"></textarea>
			<input type="button" id="send" name="send2" value="Enviar" onclick="return do_submit();" />
			<?php endif ; ?>
			
			<?php if ( file_exists( "$DOCUMENT_ROOT/admin/traffic/knowledge_search.php" ) && $aspinfo['knowledgebase'] ) : ?>
			<?php if ( !$isonline ) { print "<br><br><br>" ; } ?>
			<div id="kb_display"><a href="<?php echo $BASE_URL ?>/admin/traffic/knowledge_search.php?l=<?php echo $l ?>&x=<?php echo $x ?>&deptid=<?php echo $deptid ?>&"><b><?php echo $LANG['CLICK_HERE'] ?></b></a> <?php echo $LANG['KB_SEARCH'] ?></a></div>
			<?php endif ; ?>
		</fieldset>
	</div>
<?php endif ; ?>
	
<?php if ( ( $action != "accept" ) && ( $action != "initiate" ) && ( $action != "op2op" ) && ( $action != "initiate_accept" ) ): ?>
	<div id="options">
		&nbsp;
	</div>
	
	<?php
		// because of tabbed browsers, we want to call a JavaScript window open function
		
		$branding = preg_replace( "/target=(.*?)(>| >)/i", " >", $branding ) ;
	?>
	<div id="copyright"><span class="style1">Sistema Atendimento Online</span> <?php echo $PHPLIVE_VERSION ?> &copy; <a href="" target="_blank" class="style1"></a></div>
<?php endif; ?>
</div>
</form>
</body>
</html>
