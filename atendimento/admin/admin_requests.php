<?php
	session_start() ;
	$sid = $action = $error = $requestid = $sessionid = $logout_string = $status_string = "" ;
	$close_window = $focus_window = $close_window = $do_countdown = $reload_tracker = $alert = 0 ;
	$do_pull = 1 ;
	$sid = ( isset( $_GET['sid'] ) ) ? $_GET['sid'] : $_POST['sid'] ;
	$start = ( isset( $_GET['start'] ) ) ? $_GET['start'] : "" ;
	$reload_tracker = ( isset( $_GET['reload_tracker'] ) ) ? $_GET['reload_tracker'] : "" ;
	if ( isset( $_POST['sessionid'] ) ) { $sessionid = $_POST['sessionid'] ; }
	if ( isset( $_GET['sessionid'] ) ) { $sessionid = $_GET['sessionid'] ; }
	if ( isset( $_POST['requestid'] ) ) { $requestid = $_POST['requestid'] ; }
	if ( isset( $_GET['requestid'] ) ) { $requestid = $_GET['requestid'] ; }
	if ( isset( $_POST['l'] ) ) { $l = $_POST['l'] ; }
	if ( isset( $_GET['l'] ) ) { $l = $_GET['l'] ; }
	if ( isset( $_POST['x'] ) ) { $x = $_POST['x'] ; }
	if ( isset( $_GET['x'] ) ) { $x = $_GET['x'] ; }

	include_once( "../API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( "..", $l ) )
	{
		HEADER( "location: index.php" ) ;
		exit ;
	}
	include_once("../web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("../web/$l/$l-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Chat/Util.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Chat/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/put.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/remove.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/update.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/update.php") ;

	// make sure they have access to this page
	// if admin session is set, then they have access
	if ( !$_SESSION['session_admin'][$sid]['admin_id'] )
	{
		HEADER( "location: ../index.php" ) ;
		exit ;
	}

	// initialize
	if ( isset( $LOGO ) && file_exists( "$DOCUMENT_ROOT/web/$l/$LOGO" ) && $LOGO )
		$logo = "$BASE_URL/web/$l/$LOGO" ;
	else if ( file_exists( "$DOCUMENT_ROOT/web/$LOGO_ASP" ) && $LOGO_ASP )
		$logo = "$BASE_URL/web/$LOGO_ASP" ;
	else
		$logo = "$BASE_URL/images/logo.gif" ;

	$sound_file = "cellular.wav" ;
	$winapp = ( $_SESSION['session_admin'][$sid]['winapp'] ) ? $_SESSION['session_admin'][$sid]['winapp'] : 0 ;
	// reset this to ZERO when they login so it starts the tracking of request process
	$_SESSION['session_admin'][$sid]['requests_reload'] = 0 ;

	// we use $rand to prevent loading from cached pages
	mt_srand ((double) microtime() * 1000000) ;
	$rand = mt_rand() ;
	$admin = AdminUsers_get_UserInfo( $dbh, $_SESSION['session_admin'][$sid]['admin_id'], $_SESSION['session_admin'][$sid]['aspID'] ) ;

	// if there is a kill signal, then let's close this window
	if ( $admin['signal'] == 9 )
	{
		// set the close window var, then put admin status back to normal
		$close_window = 1 ;
		// set the last activity time back so it is offline status instantly
		AdminUsers_update_UserValue( $dbh, $admin['userID'], "utrigger", 1 ) ;
		AdminUsers_update_LastActiveTime( $dbh, $admin['userID'], time() - 60, $sid ) ;
		AdminUsers_update_Status( $dbh, $admin['userID'], 0 ) ;
		AdminUsers_update_Signal( $dbh, $admin['userID'], 0, $_SESSION['session_admin'][$sid]['aspID'] ) ;
	}
	else if ( ( $admin['last_active_time'] > $admin_idle ) && $start && ( $admin['session_sid'] != $sid ) )
	{
		// go ahead and login if activity time has been idel.
		// why?  this prevents so multiple login at various places... only check when
		// $start variable is passed ($start = first loading of console)
		$error = "Voce esta logando em outro local!" ;
		$do_pull = 0 ;
	}
	else if ( $start )
	{
		// the $start variable comes from the admin page.  it passes this variable
		// so the admin console (this) knows to update status to Active on
		// initial start of console.  make sure there is no error.
		AdminUsers_update_UserValue( $dbh, $admin['userID'], "utrigger", 1 ) ;
		AdminUsers_update_LastActiveTime( $dbh, $admin['userID'], time(), $sid ) ;
		AdminUsers_update_Status( $dbh, $admin['userID'], 1 ) ;
		AdminUsers_update_Signal( $dbh, $admin['userID'], 0, $_SESSION['session_admin'][$sid]['aspID'] ) ;
	}

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }

	// conditions
	if ( $action == "reject" )
	{
		$requestinfo = ServiceChat_get_ChatRequestInfo( $dbh, $requestid ) ;
		//$sessioninfo = ServiceChat_get_ChatSessionInfo( $dbh, $sessionid ) ;
		ServiceChat_update_ChatRequestLogStatus( $dbh, $sessionid, 3 ) ;

		// create a flag to indicate that it has been rejected
		$fp = fopen ("$DOCUMENT_ROOT/web/chatpolling/$sessionid.txt", "wb+") ;
		fclose( $fp ) ;
		// put a big number as new admin ID for now... it will be updated by visitor
		// pulling script phplive/pull/chat.php
		ServiceChat_update_TransferCall( $dbh, $requestid, 999999999, $requestinfo['deptID'], 2 ) ;
	}
	else if ( $action == "status" )
	{
		$_SESSION['session_admin'][$sid]['available_status'] = $_GET['status'] ;
		AdminUsers_update_Status( $dbh, $admin['userID'], $_GET['status'] ) ;
		$_SESSION['session_admin'][$sid]['close_timer'] = time() ;
		AdminUsers_update_UserValue( $dbh, $admin['userID'], "utrigger", $_GET['status'] ) ;
	}
	else if ( $action == "kill" )
	{
		// in UNIX -9 is kill... so let's use 9 as kill signal
		AdminUsers_update_Signal( $dbh, $admin['userID'], 9, $_SESSION['session_admin'][$sid]['aspID'] ) ;
		$do_pull = 0 ;
		$do_countdown = 1 ;
	}

	// call admin again here just incase the status is set from above
	// action == "status"... so we want the latest admin information
	$admin = AdminUsers_get_UserInfo( $dbh, $admin['userID'], $_SESSION['session_admin'][$sid]['aspID'] ) ;

	// do chat request session adding so we know when to ring alarm
	if ( !$error )
	{
		$chat_requests = ServiceChat_get_UserChatRequests( $dbh, $admin['userID'] ) ;
		$total_requests = count( $chat_requests ) ;
		if ( $total_requests > 0 )
		{
			if ( ( $total_requests != $_SESSION['session_admin'][$sid]['requests'] ) && !$winapp )
				$focus_window = 1 ;     // only do it if not a winapp
			$alert = 1 ;
		}
		$_SESSION['session_admin'][$sid]['requests'] = $total_requests ;
		$_SESSION['session_admin'][$sid]['requests'] = $total_requests ;

		// set this to equal so it does not trigger the reload while calling the pull image
		$_SESSION['session_admin'][$sid]['requests_reload'] = $_SESSION['session_admin'][$sid]['requests'] ;
	}

	// check to see if status if offline and window has been idel for a while
	// .. if offline and idel, let's close it (just incase they left the window open)
	$time_to_close = time() - ( 60 * $admin['console_close_min'] ) ;
	$minutes_left = round( ( $_SESSION['session_admin'][$sid]['close_timer'] - $time_to_close )/60 ) ;
	if ( ( $admin['available_status'] == 0 ) && ( $_SESSION['session_admin'][$sid]['close_timer'] < $time_to_close ) && ( $admin['session_sid'] == $sid ) )
		$close_window = 1 ;

	$nav_line = "&nbsp;";
	$section = 5 ;

	$close_flag = 1 ;
	if ( $winapp )
		$close_flag = -1 ;
	$logout_string = "&nbsp; <a href=\"JavaScript:void(0)\" OnClick=\"do_logout($winapp, $close_flag)\"><img src=\"../images/extra/btn_logout.gif\" width=\"41\" height=\"18\" border=\"0\" alt=\"logout\"></a>" ;
	if ( $admin['available_status'] == 1 )
	{
		$checked_online = "checked" ;
		$checked_offline = "" ;
	}
	else
	{
		$checked_online = "" ;
		$checked_offline = "checked" ;
	}
	$status_string = "<td><input type=\"radio\" OnClick=\"parent.window.update_status(1, 0)\" class=\"radio1\" $checked_online></td><td> <b><font color=\"29C029\">Ligado</font></b></td><td> &#124; </td><td><input type=\"radio\" OnClick=\"parent.window.update_status(0, 0)\" class=\"radio1\" $checked_offline></td><td> <b><font color=\"#38385E\">Desligado</font></b></td>" ;
?>
<html>
<head>
<title> Operator [ request monitor ] </title>

<?php $css_path = "../" ; include( "../css/default.php" ) ; ?>
<script language="JavaScript">
var section = <?php echo $section ?> ;	// Section number
<?php if ( $error ): ?>
var rating_str = '<?php echo $error ?>' ;
<?php elseif ( $action == "kill" ): ?>
var rating_str = 'Enviando comando...' ;
<?php else: ?>
var rating_str = '<table cellspacing=0 cellpadding=2 border=0><tr><td><a href="JavaScript:void(0)" OnClick="window.open(\'<?php echo $BASE_URL ?>/admin/index.php?x=<?php echo $x ?>&sid=<?php echo $sid ?>&action=set\', \'admin\', \'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1\')"><font color="#00478C"><b><u><?php echo stripslashes( $admin['name'] ) ?></u></b></font></a> :</td><td> status: </td><?php echo $status_string ?><td><?php echo $logout_string ?></td>' ;
<?php endif ; ?>
</script>
<script language="JavaScript" src="../js/admin.js"></script>

<script language="JavaScript">
<!--
	function do_countdown( counter )
	{
		document.form.ticker.value = counter ;
		if ( counter == 0 )
			location.href = "admin_requests.php?status=<?php echo $admin['available_status'] ?>&sid=<?php echo $sid ?>&start=1&l=<?php echo $l ?>&x=<?php echo $x ?>&reload_tracker=1" ;
		--counter ;
		var temp = setTimeout("do_countdown("+counter+")",1000) ;
	}

	function do_focus()
	{
		init() ;
		if ( <?php echo $focus_window ?> )
			parent.window.focus() ;

		if ( <?php echo $close_window ?> )
			do_window_close() ;
		else if ( <?php echo $do_countdown ?> )
			do_countdown( 10 ) ;
		else if ( <?php echo $do_pull ?> )
		{
			// only start the pull if it hasn't been started yet
			if ( parent.pflag == 0 )
			{
				parent.window.do_pull() ;
				parent.pflag = 1 ;
			}
		}

		<?php if ( $reload_tracker ): ?>
			parent.window.admin_puller.location.href = "<?php echo $BASE_URL ?>/admin/traffic/admin_puller.php?sid=<?php echo $sid ?>&start=1&l=<?php echo $l ?>&x=<?php echo $x ?>" ;
		<?php endif ; ?>
	}

	function do_logout( wflag, closewin )
	{
		if ( !wflag || closewin )
		{
			if ( confirm( "Realmente que sair?" ) )
				parent.window.location.href = "<?php echo $BASE_URL ?>/index.php?action=logout&sid=<?php echo $sid ?>&winapp=<?php echo $winapp ?>&wflag="+wflag+"&closewin="+closewin ;
		}
		else
			parent.window.location.href = "<?php echo $BASE_URL ?>/index.php?action=logout&sid=<?php echo $sid ?>&winapp=<?php echo $winapp ?>&wflag="+wflag+"&closewin="+closewin ;
	}

	function do_window_close()
	{
		if ( <?php echo $winapp ?> )
			parent.window.location.href = "<?php echo $BASE_URL ?>/index.php?action=logout&sid=<?php echo $sid ?>&winapp=<?php echo $winapp ?>" ;
		else
			parent.window.close() ;
	}

	// if status is Offline (0), then let's refresh this window every minute to
	// activate auto close if left idle
	<?php if ( $admin['available_status'] == 0 ): ?>
	var temp = setTimeout( "location.href='admin_requests.php?status=<?php echo $admin['available_status'] ?>&sid=<?php echo $sid ?>&l=<?php echo $l ?>&x=<?php echo $x ?>'",60000 ) ;
	<?php endif ; ?>

//-->
</script>

</head>
<body bgColor="#FFFFFF" text="#000000" link="#35356A" vlink="#35356A" alink="#35356A" marginheight="0" marginwidth="0" topmargin="0" leftmargin="0" OnLoad="do_focus()" class="bg2">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr> 
	<td valign="top" class="bgMenuBack" colspan="11"><table border="0" cellspacing="0" cellpadding="0">
		<tr>
		  <td width="121"><img src="<?php echo $css_path ?>images/spacer.gif" width="121" height="28"></td>
		  <td valign="top" class="bgNav"><table width="427" border="0" cellspacing="0" cellpadding="1">
			  <tr>
				<td height="24" align="center" class="nav"><b><div style="position:relative" id="navigation">&nbsp;</div></b></td>
			  </tr>
			</table></td>
		</tr>
	  </table></td>
  </tr>
<?php if ( $error ): ?>
<tr>
	<form>
	<td align="center">
		<br>
		<b><big><?php echo $error ?></big></b>
		<p>
		<?php if ( md5( "Demo" ) != $admin['password'] ) : ?>
		<input type="button" value="Close the window at the other location." OnClick="parent.window.do_kill()" class="button"> or <a href="JavaScript:do_window_close()">Cancelar</a>
		<?php endif ; ?>
	</td>
	</form>
</tr>
<?php elseif ( $action == "kill" ): ?>
<tr>
	<form name="form">
	<td align="center">
		<br>
		<b><big>Enviando comando ...</big></b>
		<p>
		Re-logging in <input type="text" name="ticker" size=2 maxlength=3 disabled> segundo(s).  Para cancelar <a href="JavaScript:history.go(-1)">clique aqui</a>.
	</td>
	</form>
</tr>
<?php else: ?>
</tr>
<tr>
	<td class="tdtrafficborder2" width="100%">
	<table cellspacing=0 cellpadding=1 border=0 width="100%">
	<tr>
		<th width="6" class="th1"><img src="../images/op/th.gif" width="6" height="18" border="0" alt=""></th>
		<th width="60" class="th1" nowrap align="left">Nome</th>
		<th width="5" class="th1">&nbsp;</th>
		<th width="100" class="th1" align="left">Departamento</th>
		<th width="5" class="th1">&nbsp;</th>
		<th class="th1" align="left">Questão</th>
		<th width="5" class="th1">&nbsp;</th>
		<th width="5" align="center" class="th1">&nbsp;</th>
		<td width="5" class="th1">&nbsp;</td>
		<th class="th1">&nbsp;</th>
		<td width="5" class="th1">&nbsp;</td>
	</tr>
	<?php
		for ( $c = 0; $c < count( $chat_requests ); ++$c )
		{
			$request = $chat_requests[$c] ;
			$department = AdminUsers_get_DeptInfo( $dbh, $request['deptID'], $_SESSION['session_admin'][$sid]['aspID'] ) ;
			$screen_name = stripslashes( $request['from_screen_name'] ) ;
			$dept_name = stripslashes( $department['name'] ) ;

			// operator-to-operator chats don't have department
			$chat_win_height = 635 ;
			if ( !isset( $department['name'] ) )
			{
				$department['name'] = "&nbsp;" ;
				$department['deptID'] = 0 ;
				$chat_win_height = 360 ;
			}

			$transfer_string = "&nbsp;" ;
			if ( $request['tflag'] == 1 )
				$transfer_string = "<img src=\"$BASE_URL/images/extra/polled.gif\" width=10 height=10 alt=\"previous operator not available - call auto polled\">" ;
			else if ( $request['tflag'] == 2 )
				$transfer_string = "<img src=\"$BASE_URL/images/extra/busy.gif\" width=10 height=10 alt=\"previous operator 'busy' - call polled\">" ;
			else if ( $request['tflag'] == 3 )
				$transfer_string = "<img src=\"$BASE_URL/images/extra/transfer.gif\" width=10 height=10 alt=\"operator transferred call\">" ;

			$question = preg_replace( "/<question>/", "", $request['question'] ) ;
			$question = preg_replace( "/<\/question>/", "", $question ) ;
			$question = stripslashes( preg_replace( "/</", "&lt;", $question ) ) ;
			$question = stripslashes( preg_replace( "/>/", "&gt;", $question ) ) ;
			$date = date( "D m/d/y h:i", $request['created'] ) ;

			print "
			<tr>
				<td width=\"6\"><img src=\"../images/spacer.gif\" width=\"6\" height=\"22\" border=\"0\" alt=\"\"></td>
				<td>$screen_name</td>
				<td width=\"5\">&nbsp;</td>
				<td>$dept_name</td>
				<td width=\"5\">&nbsp;</td>
				<td>$question</td>
				<td width=\"5\">&nbsp;</td>
				<td align=\"center\">$transfer_string</td>
				<td width=\"5\">&nbsp;</td>
				<td nowrap align=\"center\"><a href=\"JavaScript:void(0)\" OnClick=\"window.open('$BASE_URL/request.php?action=accept&sessionid=$request[sessionID]&requestid=$request[requestID]&sid=$sid&userid=".$_SESSION['session_admin'][$sid]['admin_id']."&l=".$_SESSION['session_admin'][$sid]['asp_login']."&x=".$_SESSION['session_admin'][$sid]['aspID']."', '$request[created]', 'status=no,scrollbars=no,menubar=no,resizable=no,location=no,width=450,height=$chat_win_height,screenX=50,screenY=100')\"><b>ACEITAR</b></a> &nbsp; | &nbsp; <a href=\"JavaScript:parent.window.do_reject($request[requestID], $request[sessionID])\">ocupado</a></td>
				<td width=\"5\">&nbsp;</td>
			</tr>
			" ;

			$question = preg_replace( "/&#039/", "'", $question ) ;
			$question = preg_replace( "/&quot;/", "\"", $question ) ;
			$requestid_string = "_".$request['requestID']."_" ;
			if ( !preg_match( "/$requestid_string/", $_SESSION['session_admin'][$sid]['request_ids'] || $request['tstatus'] ) )
			{
				$from_screen_name = strip_tags( $request['from_screen_name'] ) ;
				print "<!-- WinApp_Popup [$question<:>parent.window.open_chat($request[requestID], $request[sessionID],$department[deptID])<:>parent.window.do_reject($request[requestID], $request[sessionID])<:>$from_screen_name<:>$department[name]] -->" ;
				$_SESSION['session_admin'][$sid]['request_ids'] .= $requestid_string ;
			}
		}
	?>
	</table>
	</td>
</tr>
<?php endif ; ?>
</table>
<?php if ( $alert ): ?>
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="//download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="0" height="0" id="cellular" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="<?php echo $BASE_URL ?>/sounds/cellular.swf" />
<param name="quality" value="high" />
<param name="bgcolor" value="#ffffff" />
<embed src="<?php echo $BASE_URL ?>/sounds/cellular.swf" quality="high" bgcolor="#ffffff" width="0" height="0" name="cellular" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="//www.macromedia.com/go/getflashplayer" />
</object>
<?php endif ; ?>

<div id="navBack" style="position:absolute; left:8px; top:76px; width:62px; height:16px; z-index:1; visibility: hidden;"> 
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	  <td><?php echo $nav_line; ?></td>
	</tr>
  </table>
</div>
</body>
</html>
<?php
	mysql_close( $dbh['con'] ) ;
?>