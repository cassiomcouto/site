<?php
	session_start() ;
	$sid = $action = $ip = $start = $sound = "" ;
	$surveyid = $counter = 0 ;
	$do_pull = 1 ;
	$sid = ( isset( $_GET['sid'] ) ) ? $_GET['sid'] : $_POST['sid'] ;
	$l = ( isset( $_GET['l'] ) ) ? $_GET['l'] : "" ;
	$x = ( isset( $_GET['x'] ) ) ? $_GET['x'] : "" ;
	$ip = ( isset( $_GET['ip'] ) ) ? $_GET['ip'] : "" ;
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }

	include_once( "../../API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( "../..", $l ) )
	{
		HEADER( "location: ../index.php" ) ;
		exit ;
	}
	include_once("../../web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("../../web/$l/$l-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Footprint_unique/get.php") ;

	// make sure they have access to this page
	// if admin session is set, then they have access
	if ( !$_SESSION['session_admin'][$sid]['admin_id'] )
	{
		HEADER( "location: ../../index.php" ) ;
		exit ;
	}

	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
	{
		$text_width = "65" ;
	}
	else
	{
		$text_width = "30" ;
	}

	// initialize
	$admins = AdminUsers_get_AllUsers( $dbh, 0, 0, $_SESSION['session_admin'][$sid]['aspID'] ) ;
	$total_active_footprints = ServiceFootprintUnique_get_TotalActiveFootprints( $dbh, $_SESSION['session_admin'][$sid]['aspID'], $_SESSION['session_admin'][$sid]['dept_string'] ) ;

	// conditions
?>
<html>
<head>
<title> Operator [ operator-to-operator ] </title>
<?php $css_path = "../../" ; include_once( "../../css/default.php" ) ; ?>

<script language="JavaScript">
<!--
	var refresh ;
	parent.window.control_pull_traffic( "stop" ) ;

	function do_alert()
	{
		// every minute
		refresh = setTimeout( "window.location.reload( true );", 15000 ) ;
		parent.window.control_pull_traffic( "stop" ) ;
	}
//-->
</script>

</head>
<body bgColor="#FFFFFF" text="#000000" link="#35356A" vlink="#35356A" alink="#35356A" marginheight="0" marginwidth="0" topmargin="0" leftmargin="0" onLoad="do_alert()" class="bg3">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
<form>
<tr bgColor="#5D85AA">
	<td>
		<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td><img src="<?php echo $BASE_URL ?>/images/op/tab_left_corner_off.gif" width="5" height="18" border="0" alt=""></td>
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;">&nbsp;<a href="<?php echo $BASE_URL ?>/admin/traffic/admin_puller.php?sid=<?php echo $sid ?>&action=open_console&start=1&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>">traffic monitor</a>&nbsp;</td>
			<td><img src="<?php echo $BASE_URL ?>/images/op/tab_right_off.gif" width="5" height="18" border="0" alt=""></td>

			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;"><img src="<?php echo $BASE_URL ?>/images/spacer.gif" width="5" height="18" border="0" alt=""></td>
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;">&nbsp;<a href="<?php echo $BASE_URL ?>/admin/traffic/console_timer.php?sid=<?php echo $sid ?>&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&admin_puller.php?counter=0">refresh rate</a>&nbsp;</td>
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;"><img src="<?php echo $BASE_URL ?>/images/spacer.gif" width="5" height="18" border="0" alt=""></td>

			<td><img src="<?php echo $BASE_URL ?>/images/op/tab_left_flap.gif" width="5" height="18" border="0" alt=""></td>
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center.gif); background-repeat: repeat-x;">&nbsp;<a href="<?php echo $BASE_URL ?>/admin/traffic/ops.php?sid=<?php echo $sid ?>&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&admin_puller.php?counter=0">operators</a>&nbsp;</td>
			<td><img src="<?php echo $BASE_URL ?>/images/op/tab_right_flap.gif" width="5" height="18" border="0" alt=""></td>

			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;"><img src="<?php echo $BASE_URL ?>/images/spacer.gif" width="5" height="18" border="0" alt=""></td>
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;">&nbsp;<a href="<?php echo $BASE_URL ?>/admin/traffic/admin_puller.php?counter=0&sid=<?php echo $sid ?>&action=close_console&start=1&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>">close</a>&nbsp;</td>
			<td><img src="<?php echo $BASE_URL ?>/images/op/tab_right_corner_off.gif" width="5" height="18" border="0" alt=""></td>
		</tr>
		</table>
	</td>
</tr>
</form>
<tr>
	<td>
		<table cellspacing=1 cellpadding=0 border=0 width="100%">
		  <tr>
			<td>
				<table width="100%" border=0 cellpadding=2 cellspacing=1>
				<tr>
					<th nowrap><b>Name</b></th>
					<th nowrap align="center"><b>Online Status</b></th>
					<th nowrap align="center"><b>Console Status</b></th>
					<th nowrap align="center"><b>Supporting</b></th>
					<th nowrap align="center">&nbsp;</th>
				</tr>
				<?php
					for ( $c = 0; $c < count( $admins ); ++$c )
					{
						$admin = $admins[$c] ;
						$total_sessions = ServiceChat_get_UserTotalChatSessions( $dbh, $admin['name'] ) ;

						$bgcolor = "#EEEEF7" ;
						if ( $c % 2 )
							$bgcolor = "#E6E6F2" ;

						$online_status = "Offline" ;
						$bgcolor_status = "#FFE8E8" ;
						$activity = "not available" ;
						if ( ( $admin['available_status'] == 1 ) && ( $admin['last_active_time'] > $admin_idle ) )
						{
							$online_status = "Online" ;
							$bgcolor_status = "#E1FFE9" ;
							$activity = "$total_sessions requests" ;
						}
						else if ( $admin['available_status'] == 2 )
						{
							$online_status = "Away" ;
							$bgcolor_status = "#FEC65B" ;
						}

						$consol_status = "Closed" ;
						$bgcolor_consol = "#FFE8E8" ;
						if ( $admin['signal'] == 9 )
						{
							$consol_status = "Open" ;
							$bgcolor_consol = "#E1FFE9" ;
						}
						else if ( $admin['last_active_time'] > $admin_idle )
						{
							$consol_status = "Open" ;
							$bgcolor_consol = "#E1FFE9" ;
						}

						$request_string = "<font color=#BCBCBC>[ request chat ]</font>" ;
						if ( $admin['userID'] == $_SESSION['session_admin'][$sid]['admin_id'] )
							$request_string = "&nbsp;" ;
						else if ( $online_status == "Online" )
							$request_string = "<a href=\"JavaScript:void(0)\" OnClick=\"window.open( '$BASE_URL/request.php?action=op2op&userid=". $_SESSION['session_admin'][$sid]['admin_id']."&op2op=$admin[userID]&l=$l&x=$x&deptid=0&pagex=', 'op2op', 'scrollbars=no,menubar=no,resizable=0,location=no,screenX=50,screenY=100,width=450,height=360' )\">[ request chat ]</a>" ;

						print "
							<tr class=\"altcolor2\">
								<td>$admin[name]</td>
								<td align=\"center\" bgColor=\"$bgcolor_status\">$online_status</td>
								<td align=\"center\" bgColor=\"$bgcolor_consol\">$consol_status</td>
								<td align=\"center\">$activity</td>
								<td align=\"center\">
									$request_string
								</td>
							</tr>
						" ;
					}
				?>
				</table>
			</td>
		  </tr>
		</table>
	</td>
</tr>
</table>
</body>
</html>
<?php
	mysql_close( $dbh['con'] ) ;
?>