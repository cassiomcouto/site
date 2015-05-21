<?php
	session_start() ;
	if ( isset( $_SESSION['session_setup'] ) ) { $session_setup = $_SESSION['session_setup'] ; } else { HEADER( "location: index.php" ) ; exit ; }
	include_once( "../API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( "..", $session_setup['login'] ) )
	{
		HEADER( "location: index.php" ) ;
		exit ;
	}
	include_once("../web/conf-init.php");
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("../web/$session_setup[login]/$session_setup[login]-conf-init.php") ;
	include_once("../system.php") ;
	include_once("../lang_packs/$LANG_PACK.php") ;
	include_once("../web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/Util.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/update.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/remove.php") ;
	$section = 4 ;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = '<a href="options.php" class="nav">:: Home</a>';

	// initialize
	$action = $error_mesg = $adminid = $sessionid = "" ;

	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
		$text_width = "12" ;
	else
		$text_width = "9" ;

	$success = 0 ;
	// update all admins status to not available if they have been idle
	AdminUsers_update_IdleAdminStatus( $dbh, $admin_idle ) ;

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_GET['adminid'] ) ) { $adminid = $_GET['adminid'] ; }
	if ( isset( $_GET['sessionid'] ) ) { $sessionid = $_GET['sessionid'] ; }

	// conditions

	if ( $action == "kill_chat" )
	{
		$file_visitor = $sessionid."_admin.txt" ;
		$file_admin = $sessionid."_visitor.txt" ;

		$string = "<STRIP_FOR_PLAIN><font color=\"#FF0000\"><b>** Session was closed by root user.  Session has ended. **</b></font><br><script language=\"JavaScript\">alert( \"Session has been closed by root user.  Window will now close in 10 seconds!\" ) ; setTimeout(\"parent.window.close()\", 10000) ;</script></STRIP_FOR_PLAIN>" ;
		UtilChat_AppendToChatfile( $file_visitor, $string ) ;
		UtilChat_AppendToChatfile( $file_admin, $string ) ;

		// call the script again to give it some time so the message above gets
		// written to the chat screen.  Why?  the system auto cleans chat files if
		// there is no chat parties for that session... thus, the message above could
		// get wiped out without ever making it on the screen.  so let's delay it a bit
		HEADER( "location: processes.php?sessionid=$sessionid&action=kill_done" ) ;
		exit ;
	}
	else if ( $action == "kill_done" )
	{
		// just delete the chatsessionlist content... why?  because there is an
		// auto clean that will sweep through and delete the chat session and
		// all chat files for sessions that are not active (no parties in the session)
		ServiceChat_remove_ChatSessionlist( $dbh, $sessionid ) ;
		$action = "chat" ;
		$success = 1 ;
	}
	else if ( $action == "close_consol" )
	{
		// in UNIX -9 is kill... so let's use 9 as kill signal
		AdminUsers_update_Signal( $dbh, $adminid, 9, $session_setup['aspID'] ) ;
		$action = "consol" ;
		$success = 1 ;
	}
?>
<?php include_once( "./header.php" ) ; ?>
<script language="JavaScript">
<!--
	function confirm_kill( sessionid )
	{
		if ( confirm( "This will end this chat session!  Should I proceed?" ) )
			location.href = "processes.php?action=kill_chat&sessionid="+sessionid ;
	}

	function confirm_close( adminid )
	{
		if ( confirm( "This will close this operator's console!  Should I proceed?" ) )
			location.href = "processes.php?action=close_consol&adminid="+adminid ;
	}

	function launch_monitor()
	{
		url = "op_monitor.php" ;
		newwin = window.open(url, "op_monitor", "scrollbars=yes,menubar=no,resizable=1,location=no,width=305,height=305") ;
		newwin.focus() ;
	}

	function do_alert()
	{
		<?php if ( $success ) { print "		alert( 'Success!' ) ;\n" ; } ?>
	}

	function console_stats( userid )
	{
		url = "op_status.php?userid="+userid ;
		newwin = window.open(url, "op_console", "scrollbars=yes,menubar=no,resizable=1,location=no,width=350,height=450") ;
		newwin.focus() ;
	}
//-->
</script>
<?php
	if ( $action == "chat" ):
	$chatsessions = ServiceChat_get_ChatSessions( $dbh, $session_setup['aspID'] ) ;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
  <td height="350" valign="top"> <p><span class="title">Sessions: Active 
	  Chat Sessions</span><br>
	  Below is a complete list of current active chat processes. You may 
	  manually kill and end the process by clicking the "kill" link next 
	  to the process ID. </p>
	<p><a href="processes.php?action=chat"><strong>Reload list</strong></a> 
	</p>
	  <table width="100%" border=0 cellpadding=2 cellspacing=1>
		<tr> 
			<th nowrap>ID</th>
			<th nowrap>Process Started</th>
			<th nowrap>Operator Name</th>
			<th nowrap>Visitor Name</th>
			<th>&nbsp;&nbsp;</th>
	  </tr>
		<?php
			for ( $c = 0; $c < count( $chatsessions ); ++$c )
			{
				$session = $chatsessions[$c] ;

				$sessionlogins = ServiceChat_get_ChatSessionLogins( $dbh, $session['sessionID'] ) ;
				$date = date( "D m/d/y $TIMEZONE_FORMAT:i$TIMEZONE_AMPM", ( $session['created'] + $TIMEZONE ) ) ;

				$bgcolor = "#EEEEF7" ;
				if ( $c % 2 )
					$bgcolor = "#E6E6F2" ;

				// only print out if there are active chat parties
				if ( count( $sessionlogins ) > 0 )
				{
					print "
						<tr class=\"altcolor2\">
							<td>$session[sessionID]</td>
							<td>$date</td>
							<td>$sessionlogins[admin]</td>
							<td>$sessionlogins[visitor]</td>
							<td><a href=\"JavaScript:confirm_kill( $session[sessionID] )\">kill process</a></td>
						</tr>
					" ;
				}
			}
		?>
	</table></td>
  <td style="background-image: url(../images/g_sessions_big.jpg);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="1"></td>
</tr>
</table>
		
<?php
	elseif ( $action == "consol" ):
	$admins = AdminUsers_get_AllUsers( $dbh, 0, 0, $session_setup['aspID'] ) ;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
  <td height="350" valign="top"> <p><span class="title">Sessions: Active 
	  Operator Sessions</span><br>
	  Below is a complete list of all operators and their active Operator 
	  Console status. If there is an admin console that was left open, 
	  you may manually kill and end the process by clicking the &quot;kill&quot; 
	  link next to the process ID. </p>
	<p>
	<li> Monitor your operator's console status continuously.  Just launch the below monitor and keep it open to periodically view the status.  The monitor's window will update the current status every minute.<br>
	[ <big><strong><a href="JavaScript:launch_monitor()">Click to launch Admin Console Monitor</a></strong></big> ]
	</p>
	<p>
	<li> View operator's online/offline activity by clicking on the "view status history" link.
	</p>
	<table width="100%" border=0 cellpadding=2 cellspacing=1>
	  <tr align="left"> 
		<th nowrap>Name</th>
		<th nowrap>Login</th>
		<th nowrap width="150" align="center">Online/Offline History</th>
		<th align="center" nowrap>Online</th>
		<th align="center" nowrap>Console</th>
		<th>&nbsp;</th>
	  </tr>
	 <?php
			for ( $c = 0; $c < count( $admins ); ++$c )
			{
				$admin = $admins[$c] ;

				$bgcolor = "#EEEEF7" ;
				if ( $c % 2 )
					$bgcolor = "#E6E6F2" ;

				$online_status = "Offline" ;
				$bgcolor_status = "#FFE8E8" ;
				if ( $admin['available_status'] == 1 )
				{
					$online_status = "Online" ;
					$bgcolor_status = "#E1FFE9" ;
				}
				else if ( $admin['available_status'] == 2 )
				{
					$online_status = "Away" ;
					$bgcolor_status = "#FEC65B" ;
				}

				$consol_status = "Closed" ;
				$bgcolor_consol = "#FFE8E8" ;
				$kill_string = "&nbsp;" ;
				if ( $admin['signal'] == 9 )
				{
					$consol_status = "Open" ;
					$kill_string = "closing console..." ;
					$bgcolor_consol = "#E1FFE9" ;
				}
				else if ( $admin['last_active_time'] > $admin_idle )
				{
					$consol_status = "Open" ;
					$kill_string = "<a href=\"JavaScript:confirm_close( $admin[userID] )\">close console</a>" ;
					$bgcolor_consol = "#E1FFE9" ;
				}

				print "
					<tr class=\"altcolor2\">
						<td>$admin[name]</td>
						<td>$admin[login]</td>
						<td align=\"center\">[ <a href=\"JavaScript:console_stats( $admin[userID] )\">view status history</a> ]</td>
						<td align=\"center\" bgColor=\"$bgcolor_status\">$online_status</td>
						<td align=\"center\" bgColor=\"$bgcolor_consol\">$consol_status</td>
						<td align=\"center\" class=\"altcolor3\">$kill_string</td>
					</tr>
				" ;
			}
		?>
	</table></td>
  <td style="background-image: url(../images/g_sessions_big.jpg);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="1"></td>
</tr>
</table>
<?php endif ;?>
<?php include_once( "./footer.php" ) ; ?>
