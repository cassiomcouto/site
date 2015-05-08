<?php
	session_start() ;
	$sid = $action = "" ;
	$success = 0 ;
	$sid = ( isset( $_GET['sid'] ) ) ? $_GET['sid'] : "" ;
	$l = ( isset( $_GET['l'] ) ) ? $_GET['l'] : "" ;
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
	include_once("$DOCUMENT_ROOT/API/Users/update.php") ;
	include_once("$DOCUMENT_ROOT/API/Footprint_unique/get.php") ;

	// make sure they have access to this page
	// if admin session is set, then they have access
	if ( !$_SESSION['session_admin'][$sid]['admin_id'] )
	{
		HEADER( "location: ../../index.php" ) ;
		exit ;
	}

	// conditions
	if ( $action == "submit" )
	{
		switch ( $_GET['type'] )
		{
			case "every":
				$value = 10 ;
				break ;
			case "set":
				$value = $_GET['minute'] * 60 ;
				break ;
			default:
				$value = 0 ;

		}
		AdminUsers_update_UserValue( $dbh, $_SESSION['session_admin'][$sid]['admin_id'], "console_refresh", $value ) ;
		$_SESSION['session_admin'][$sid]['traffic_timer'] = $value ;
		$success = 1 ;
	}

	$admin = AdminUsers_get_UserInfo( $dbh, $_SESSION['session_admin'][$sid]['admin_id'], $_SESSION['session_admin'][$sid]['aspID'] ) ;
	$total_active_footprints = ServiceFootprintUnique_get_TotalActiveFootprints( $dbh, $_SESSION['session_admin'][$sid]['aspID'], $_SESSION['session_admin'][$sid]['dept_string'] ) ;
?>
<html>
<head>
<title> Operator [ Set Traffic Monitor Refresh Rate ] </title>
<?php $css_path = "../../" ; include_once( "../../css/default.php" ) ; ?>

<script language="JavaScript">
<!--
	parent.window.control_pull_traffic( "stop" ) ;
	parent.window.traffic_timer = <?php echo $admin['console_refresh'] ?> ;

	function do_alert()
	{
		window.status = '' ;	
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
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;"><img src="<?php echo $BASE_URL ?>/images/spacer.gif" width="5" height="18" border="0" alt=""></td>

			<td><img src="<?php echo $BASE_URL ?>/images/op/tab_left_flap.gif" width="5" height="18" border="0" alt=""></td>
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center.gif); background-repeat: repeat-x;">&nbsp;<a href="<?php echo $BASE_URL ?>/admin/traffic/console_timer.php?sid=<?php echo $sid ?>&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&admin_puller.php?counter=0">refresh rate</a>&nbsp;</td>
			<td><img src="<?php echo $BASE_URL ?>/images/op/tab_right_flap.gif" width="5" height="18" border="0" alt=""></td>

			<?php if ( $admin['op2op'] && file_exists( "$DOCUMENT_ROOT/admin/traffic/ops.php" ) && ( AdminUsers_get_TotalUsers( $dbh, $_SESSION['session_admin'][$sid]['aspID'] ) > 1 ) ): ?>
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;"><img src="<?php echo $BASE_URL ?>/images/spacer.gif" width="5" height="18" border="0" alt=""></td>
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;">&nbsp;<a href="<?php echo $BASE_URL ?>/admin/traffic/ops.php?sid=<?php echo $sid ?>&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&admin_puller.php?counter=0">operators</a>&nbsp;</td>
			<td><img src="<?php echo $BASE_URL ?>/images/op/tab_right_off.gif" width="5" height="18" border="0" alt=""></td>
			<?php endif ; ?>

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
			<form method="GET" action="console_timer.php">
			<input type="hidden" name="action" value="submit">
			<input type="hidden" name="sid" value="<?php echo $sid ?>">
			<input type="hidden" name="l" value="<?php echo $l ?>">
			<td>
				<table cellspacing=0 cellpadding=2 border=0>
				<tr>
					<td colspan=2>
						<big><b>Set your traffic monitor refresh rate.  <?php if( $success ): ?><font color="#29C029">Update success!</font><?php endif ; ?></b></big><br>
					</td>
				</tr>
				<tr>
					<td><input type="radio" name="type" value="every" <?php echo ( $admin['console_refresh'] == 10 ) ? "checked" : "" ?> class="radio4"></td>
					<td> Update traffic monitor every time there is change in traffic.</td>
				</tr>
				<tr>
					<td><input type="radio" name="type" value="set" <?php echo ( $admin['console_refresh'] > 10 ) ? "checked" : "" ?> class="radio4"></td>
					<td> Update traffic monitor every <select name="minute"><option <?php echo ( ( $admin['console_refresh']/60 ) == 1 ) ? "selected" : "" ?> >1</option><option <?php echo ( ( $admin['console_refresh']/60 ) == 3 ) ? "selected" : "" ?>>3</option><option <?php echo ( ( $admin['console_refresh']/60 ) == 5 ) ? "selected" : "" ?>>5</option><option <?php echo ( ( $admin['console_refresh']/60 ) == 10 ) ? "selected" : "" ?>>10</option></select> minute(s)</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td> <input type="submit" value="Update Changes" class="mainButton"></td>
				</tr>
				</table>
			</td>
			</form>
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