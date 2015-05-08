<?php
	session_start() ;
	include_once("../web/conf-init.php");
	include_once("$DOCUMENT_ROOT/API/sql.php" ) ;
	$action = ( isset( $_GET['action'] ) ) ? $_GET['action'] : "" ;
	$x = ( isset( $_GET['x'] ) ) ? $_GET['x'] : "" ;
	$l = ( isset( $_GET['l'] ) ) ? $_GET['l'] : "" ;
	$chat_session = ( isset( $_GET['chat_session'] ) ) ? $_GET['chat_session'] : "" ;
	$sid = ( isset( $_GET['sid'] ) ) ? $_GET['sid'] : "" ;
	$requestid = ( isset( $_GET['requestid'] ) ) ? $_GET['requestid'] : "" ;
	$deptid = ( isset( $_GET['deptid'] ) ) ? $_GET['deptid'] : "" ;
	$theme_admin = ( isset( $_GET['theme_admin'] ) ) ? $_GET['theme_admin'] : "" ;

	if ( ( !isset( $_SESSION['session_admin'] ) && !isset( $_SESSION['session_chat'] ) && !isset( $_SESSION['session_setup'] ) ) || ( $action == "set" ) )
	{
		// called from WinApp - since it won't register the session, read from cookie and reset
		if ( $action == "set" )
		{
			if ( !isset( $_SESSION['session_admin'] ) )
			{
				session_register( "session_admin" ) ;
				$session_admin = ARRAY() ;
				$_SESSION['session_admin'] = ARRAY() ;
			}
			include_once("$DOCUMENT_ROOT/API/ASP/get.php") ;
			include_once("$DOCUMENT_ROOT/API/Users/get.php") ;

			$aspinfo = AdminASP_get_UserInfo( $dbh, $x ) ;
			$admin = AdminUsers_get_UserInfoBySession( $dbh, $x, $sid ) ;
			
			$departments = AdminUsers_get_UserDepartments( $dbh, $admin['userID'] ) ;
			$dept_string = "" ;
			for ( $c = 0; $c < count( $departments ); ++$c )
			{
				$the_department = $departments[$c] ;
				$dept_string .= "deptID = $the_department[deptID] AND" ;
			}
			$dept_string .= "deptID = 0" ;

			// reset $sid so it registered online if launched admin console again
			$sid  = time() ;
			$_SESSION['session_admin'][$sid] = ARRAY() ;
			$_SESSION['session_admin'][$sid]['dept_string'] = $dept_string ;
			$_SESSION['session_admin'][$sid]['admin_id'] = $admin['userID'] ;
			$_SESSION['session_admin'][$sid]['requests'] = 0 ;
			$_SESSION['session_admin'][$sid]['aspID'] = $aspinfo['aspID'] ;
			$_SESSION['session_admin'][$sid]['asp_login'] = $aspinfo['login'] ;
			$_SESSION['session_admin'][$sid]['active_footprints'] = 0 ;
			$_SESSION['session_admin'][$sid]['winapp'] = 0 ;
			$_SESSION['session_admin'][$sid]['close_timer'] = 0 ;
			$_SESSION['session_admin'][$sid]['traffic_monitor'] = 0 ;
			$_SESSION['session_admin'][$sid]['available_status'] = 1 ;
			$_SESSION['session_admin'][$sid]['sound'] = "on" ;
			$_SESSION['session_admin'][$sid]['request_ids'] = "" ;
			$_SESSION['session_admin'][$sid]['traffic_timer'] = $admin['console_refresh'] ;

			$url = "$BASE_URL/admin/jump.php?x=$x&l=$l&chat_session=$chat_session&sid=$sid&requestid=&page=transcript&theme_admin=$theme_admin" ;
			HEADER( "location: $url" ) ;
			exit ;
		}
		else
		{
			print "<font color=\"#FF0000\">[Access Denied-transcript] Exiting...</font>" ;
			exit ;
		}
	}

	include_once( "../API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( "..", $l ) )
	{
		print "<font color=\"#FF0000\">[Configuration Error in view_transcript.php: config files not found!] Exiting...</font>" ;
		exit ;
	}
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("../web/$l/$l-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;

	// initialize
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> View Transcript </title>

<script type="text/javascript" language="JavaScript1.2">
<!--
	var pullimage ;

	function doprint()
	{
		window.frames['fmain'].focus() ;
		window.frames['fmain'].print() ;
	}

	function checkifloaded()
	{
		loaded = pullimage.width ;
		if ( loaded == 1 )
		{
			document.form.email.value = "" ;
			document.form.optmessage.value = "" ;
			document.form.submitbutton.disabled = false ;
			document.form.submitbutton.value = "Email Transcript" ;
			alert( "Transcript has been sent!" ) ;
		}
		else
			alert( "Error: Transcript did not send.  Please try again." ) ;
	}

	function do_submit()
	{
		if ( document.form.email.value == "" )
			alert( "Please input a valid email address." ) ;
		else if ( document.form.email.value.indexOf("@") == -1 )
			alert( "Please input a valid email address. (example: someone@somewhere.com)" ) ;
		else
		{
			document.form.submitbutton.disabled = true ;
			document.form.submitbutton.value = "Please hold ..." ;

			email = document.form.email.value ;
			optmessage = escape( document.form.optmessage.value ) ;

			pullimage = new Image ;
			var pull_url = "<?php echo $BASE_URL ?>/admin/view_transcripts.php?action=send&l=<?php echo $l ?>&x=<?php echo $x ?>&chat_session=<?php echo $chat_session ?>&sid=<?php echo $sid ?>&requestid=<?php echo $requestid ?>&deptid=<?php echo $deptid ?>&email="+email+"&optmessage="+optmessage ;
			pullimage.src = pull_url ;
			pullimage.onload = checkifloaded ;
		}
	}

//-->
</script>

<link href="../css/layout.css" rel="stylesheet" type="text/css" />
<link href="../themes/<?php echo ( $theme_admin ) ? $theme_admin : $THEME ?>/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form name="form" OnSubmit="return false;">
<div id="maintrans">
	<center>
	<h1>Ver Transcrição do Chat</h1>
	<div id="chat">
		<iframe src="view_transcriptm.php?x=<?php echo $x ?>&l=<?php echo $l ?>&chat_session=<?php echo $chat_session ?>&sid=<?php echo $sid ?>&requestid=<?php echo $requestid ?>&deptid=<?php echo $deptid ?>&action=<?php echo $action ?>&theme_admin=<?php echo $theme_admin ?>" width="100%" height="196" frameborder="0" name="fmain" id="fmain"></iframe>
	</div>
	</center>
	<fieldset>
		<dl>
			<dt>&nbsp;&nbsp;<label for="email">Email To</label></td>
			<dd class="textbox"><input type="text" size=45 name="email" maxlength="160" id="email"></dd>
		</dl>
		<dl>
			<dt>&nbsp;&nbsp;<label for="message">Message</label></dt>
			<dd><textarea rows=3 cols=50 name="optmessage" id="message"></textarea></dd>
		</dl>
		<dl>
			<dt></dt><dd><input type="button" value="Email Transcript" OnClick="do_submit()" class="button" name="submitbutton">  &nbsp;&nbsp; <input type="button" class="button" value="Imprimir" OnClick="doprint()"></dd>
		</dl>
	</fieldset>
</div>
</form>
</body>
</html>
