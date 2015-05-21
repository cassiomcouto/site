<?php
	session_start() ;
	$sid = "" ;
	$sid = ( isset( $_GET['sid'] ) ) ? $_GET['sid'] : $_POST['sid'] ;

	include_once( "../API/Util_Dir.php" ) ;
	if ( !isset( $_SESSION['session_admin'][$sid]['asp_login'] ) || !Util_DIR_CheckDir( "..", $_SESSION['session_admin'][$sid]['asp_login'] ) )
	{
		HEADER( "location: index.php" ) ;
		exit ;
	}
	include_once("../web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("$DOCUMENT_ROOT/web/".$_SESSION['session_admin'][$sid]['asp_login']."/".$_SESSION['session_admin'][$sid]['asp_login']."-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Refer/remove.php") ;
	include_once("$DOCUMENT_ROOT/API/Footprint/remove.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;

	ServiceRefer_remove_OldRefer( $dbh, $_SESSION['session_admin'][$sid]['aspID'] ) ;
	ServiceFootprint_remove_OldFootprints( $dbh ) ;
	$admin = AdminUsers_get_UserInfo( $dbh, $_SESSION['session_admin'][$sid]['admin_id'], $_SESSION['session_admin'][$sid]['aspID'] ) ;
?>
<html>
<head>
<title> Monitor de Atendimento - Chat </title>
<script language="JavaScript" src="<?php echo $BASE_URL ?>/js/xmlhttp.js"></script>
<script language="JavaScript">
<!--

	// timer1=setTimeout("window.location.reload( true );", 10000);

	var date ;
	var unique ;
	var traffic_monitor_on = 0 ;
	var traffic_timer = <?php echo $admin['console_refresh'] ?> ;
	var reload_switch = 1 ;

	// we put the request check on this page because of Netscape does not
	// like frames and the var temp = setTimeout does not work when Netscape refreshes
	// the window when we do a resize of window.  so the var temp = setTimeout HAS to
	// be in the parent window, not in the framed window.... in FACT, put
	// most of the JavaScript in the parent window.  it seems Netscape does
	// not even recognize JavaScript functions inside framed window after resizing
	// window.
	var pullimage ;
	var loaded = 0 ;
	var pullimage = new Image ;

	var pullimage_traffic ;
	var loaded_traffic = 0 ;
	var pullimage_traffic = new Image ;
	var pflag = 0 ;

	// begin pulling of request
	function checkifloaded( flag )
	{
		if ( flag == 1 )
		{
			//parent.window.admin_requests.location.reload( true ) ;
			parent.window.admin_requests.location.href = "admin_requests.php?status=<?php echo $_SESSION['session_admin'][$sid]['available_status'] ?>&sid=<?php echo $sid ?>&l=<?php echo $_SESSION['session_admin'][$sid]['asp_login'] ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>" ;
		}
	}

	function dounique()
	{
		date = new Date() ;
		return date.getTime() ;
	}

	function do_pull()
	{
		unique = dounique() ;
		var xmlrequests = initxmlhttp() ;
		var url = '<?php echo $BASE_URL ?>/pull/requests.php?sid=<?php echo $sid ?>&unique='+unique ;
		xmlrequests.open( "GET", url, true ) ;
		xmlrequests.onreadystatechange=function()
		{
			if (xmlrequests.readyState==4)
			{
				checkifloaded( xmlrequests.responseText ) ;
			}
		}
		xmlrequests.send(null) ;
		var temp = setTimeout("do_pull()", <?php echo $NEW_CHAT_REQUEST_REFRESH * 1000 ?>) ;
	}

	// begin pulling of traffic monitor
	function checkifloaded_traffic()
	{
		loaded_traffic = pullimage_traffic.width ;
		if ( ( loaded_traffic && ( traffic_monitor_on == 1 ) ) || ( traffic_timer > 10 ) )
		{
			unique = dounique() ;
			if ( loaded_traffic == 100 )
				loaded_traffic = 0 ;
			parent.window.admin_puller.location.href = "<?php echo $BASE_URL ?>/admin/traffic/admin_puller.php?counter="+loaded_traffic+"&sid=<?php echo $sid ?>&l=<?php echo $_SESSION['session_admin'][$sid]['asp_login'] ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&unique="+unique ;
		}
	}

	function do_pull_traffic()
	{
		if ( traffic_monitor_on == 1 )
		{
			unique = dounique() ;
			pullimage_traffic = new Image ;
			pullimage_traffic.src = '<?php echo $BASE_URL ?>/pull/traffic.php?sid=<?php echo $sid ?>&l=<?php echo $_SESSION['session_admin'][$sid]['asp_login'] ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&unique='+unique ;
			pullimage_traffic.onload = checkifloaded_traffic ;
		}
	}

	function control_pull_traffic( action )
	{
		if ( action == "start" )
		{
			if ( traffic_monitor_on != 1 )
			{
				traffic_monitor_on = 1 ;
				do_pull_traffic() ;
			}
		}
		else
			traffic_monitor_on = 0 ;
	}

	function update_status( status, wflag )
	{
		parent.window.admin_requests.location.href = "admin_requests.php?action=status&status="+status+"&sid=<?php echo $sid ?>&l=<?php echo $_SESSION['session_admin'][$sid]['asp_login'] ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>" ;
	}

	function do_kill()
	{
		if ( confirm( "realmente que fechar esta janela?" ) )
			parent.window.admin_requests.location.href = "admin_requests.php?status=<?php echo $_SESSION['session_admin'][$sid]['available_status'] ?>&action=kill&sid=<?php echo $sid ?>&l=<?php echo $_SESSION['session_admin'][$sid]['asp_login'] ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>" ;
	}

	function do_reject( requestid, sessionid )
	{
		parent.window.admin_requests.location.href = "admin_requests.php?status=<?php echo $_SESSION['session_admin'][$sid]['available_status'] ?>&action=reject&sessionid="+sessionid+"&requestid="+requestid+"&sid=<?php echo $sid ?>&l=<?php echo $_SESSION['session_admin'][$sid]['asp_login'] ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>" ;
	}

	function open_chat( requestid, sessionid, deptid )
	{
		var winname = "win" + dounique() ;
		// if no deptid, then it is operator-to-operator
		window_height = 635 ;
		if ( deptid == 0 )
			window_height = 360 ;
		url = "../request.php?action=accept&sessionid="+sessionid+"&requestid="+requestid+"&sid=<?php echo $sid ?>&userid=<?php echo $_SESSION['session_admin'][$sid]['admin_id'] ?>&l=<?php echo $_SESSION['session_admin'][$sid]['asp_login'] ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>" ;
		newwin = window.open(url, winname, "status=no,scrollbars=no,menubar=no,resizable=no,location=no,width=450,height="+window_height+",screenX=50,screenY=100") ;
		newwin.focus() ;
	}
//-->
</script>
</head>
<frameset rows="150,*" cols="*" border="0" frameborder="0" framespacing="0">
	<frame src="admin_requests.php?status=<?php echo $_SESSION['session_admin'][$sid]['available_status'] ?>&sid=<?php echo $sid ?>&start=1&l=<?php echo $_SESSION['session_admin'][$sid]['asp_login'] ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>" name="admin_requests" noresize border=0 scrolling=auto>

	<?php if ( $INITIATE && file_exists( "$DOCUMENT_ROOT/admin/traffic/admin_puller.php" ) ): ?>
	<frame src="<?php echo $BASE_URL ?>/admin/traffic/admin_puller.php?sid=<?php echo $sid ?>&start=1&l=<?php echo $_SESSION['session_admin'][$sid]['asp_login'] ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&counter=0&" name="admin_puller" noresize border=0 scrolling=auto>
	<?php else: ?>
	<frame src="<?php echo $BASE_URL ?>/admin/blank.php?sid=<?php echo $sid ?>&start=1&l=<?php echo $_SESSION['session_admin'][$sid]['asp_login'] ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&counter=0&" name="admin_puller" noresize border=0 scrolling=auto>
	<?php endif ; ?>
</frameset>
<noframes>
</html>
<?php
	mysql_close( $dbh['con'] ) ;
?>