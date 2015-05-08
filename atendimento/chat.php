<?php
	session_start() ;
	$session_chat = $_SESSION['session_chat'] ;
	$sid = ( isset( $_GET['sid'] ) ) ? $_GET['sid'] : "" ;
	$requestid = ( isset( $_GET['requestid'] ) ) ? $_GET['requestid'] : "" ;
	$sessionid = ( isset( $_GET['sessionid'] ) ) ? $_GET['sessionid'] : "" ;
	$userid = ( isset( $_GET['userid'] ) ) ? $_GET['userid'] : "" ;
	$action = ( isset( $_GET['action'] ) ) ? $_GET['action'] : "" ;
	if ( !file_exists( "web/".$session_chat[$sid]['asp_login']."/".$session_chat[$sid]['asp_login']."-conf-init.php" ) || !file_exists( "web/conf-init.php" ) )
	{
		print "<font color=\"#FF0000\">[Configuration Error: configurações não encontradas! -$sid] Saindo...</font>" ;
		exit ;
	}
	include_once("./web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("$DOCUMENT_ROOT/web/".$session_chat[$sid]['asp_login']."/".$session_chat[$sid]['asp_login']."-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/update.php") ;


	// set frame row properties depending if admin or regular request
	$frame_row_properties = "*,100%" ;
	if ( $session_chat[$sid]['isadmin'] && $session_chat[$sid]['deptid'] )
		$frame_row_properties = "*,100%" ;
	// let's start the poll time
	$_SESSION['session_chat'][$sid]['admin_poll_time'] = time() ;
	$window_title = preg_replace( "/<(.*)>/", "", $session_chat[$sid]['visitor_name'] ) .": Pedido de Suporte" ;
?>
<html>
<head>
<title> <?php echo $window_title  ?> </title>
<script language="JavaScript">
<!--
	function dopush( url, winname )
	{
		if ( parent.window.opener && !parent.window.opener.closed )
		{
			parent.window.opener.location.href = url ;
			var temp = setTimeout( "parent.window.opener.focus()", 1500 ) ;
		}
		else
		{
			var newwin = window.open( url, winname, "scrollbars=yes,menubar=no,toolbar=yes,resizable=1,location=yes") ;
			if ( newwin )
				newwin.focus() ;
		}
	}

	function messagebox()
	{
		window.frames['main'].window.respawn = 0 ;
		window.frames['main'].window.doclose = 0 ;
		var url = "message_box.php?l=<?php echo $session_chat[$sid]['asp_login'] ?>&x=<?php echo $session_chat[$sid]['aspID'] ?>&deptid=<?php echo $session_chat[$sid]['deptid'] ?>&action=exit&requestid=<?php echo $requestid ?>&sid=<?php echo $sid ?>" ;
		location.href = url ;
	}

//-->
</script>
</head>
<frameset rows="<?php echo $frame_row_properties ?>" cols="*" border="0" frameborder="0">
	<frame src="chat_session.php?sessionid=<?php echo $sessionid ?>&sid=<?php echo $sid ?>&requestid=<?php echo $requestid ?>" name="session" noresize border=0 scrolling=no>
	<frame src="chat_main.php?sessionid=<?php echo $sessionid ?>&requestid=<?php echo $requestid ?>&sid=<?php echo $sid ?>&start=1" name="main" noresize border=0 scrolling=no>
</frameset>
</html>
<?php
	mysql_close( $dbh['con'] ) ;
?>