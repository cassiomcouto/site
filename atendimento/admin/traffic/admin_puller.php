<?php
	session_start() ;
	$sid = $action = $ip = $start = $sound = "" ;
	$surveyid = $counter = 0 ;
	$do_pull = 1 ;
	$class = "bg3" ;
	$sid = ( isset( $_GET['sid'] ) ) ? $_GET['sid'] : "" ;
	$l = ( isset( $_GET['l'] ) ) ? $_GET['l'] : "" ;
	$x = ( isset( $_GET['x'] ) ) ? $_GET['x'] : "" ;
	$ip = ( isset( $_GET['ip'] ) ) ? $_GET['ip'] : "" ;
	$start = ( isset( $_GET['start'] ) ) ? $_GET['start'] : "" ;
	$sound = ( isset( $_GET['sound'] ) ) ? $_GET['sound'] : "" ;

	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_POST['surveyid'] ) ) { $surveyid = $_POST['surveyid'] ; }
	if ( isset( $_GET['surveyid'] ) ) { $surveyid = $_GET['surveyid'] ; }
	if ( isset( $_POST['counter'] ) ) { $counter = $_POST['counter'] ; }
	if ( isset( $_GET['counter'] ) ) { $counter = $_GET['counter'] ; }

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
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Chat/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Footprint_unique/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Footprint_unique/update.php") ;
	include_once("$DOCUMENT_ROOT/API/Footprint/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Refer/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Clicks/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Canned/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Footprint_unique/remove.php") ;

	// make sure they have access to this page
	// if admin session is set, then they have access
	if ( !$_SESSION['session_admin'][$sid]['admin_id'] )
	{
		HEADER( "location: ../../index.php" ) ;
		exit ;
	}

	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
	{
		$optimized = 1 ;
		$text_width = "65" ;
	}
	else
	{
		$optimized = 0 ;
		$text_width = "30" ;
	}

	// initialize
	$m = date( "m",mktime() ) ;
	$y = date( "Y",mktime() ) ;
	$d = date( "j",mktime() ) ;

	// the timespan to get the stats
	$begin = mktime( 0,0,0,$m,$d,$y ) ;
	$end = mktime( 23,59,59,$m,$d,$y ) ;

	// we use $rand to prevent loading from cached pages
	mt_srand ( ( double ) microtime() * 1000000 ) ;
	$rand = mt_rand() ;

	ServiceFootprintUnique_remove_IdleFootprints( $dbh, $_SESSION['session_admin'][$sid]['aspID'] ) ;
	$admin = AdminUsers_get_UserInfo( $dbh, $_SESSION['session_admin'][$sid]['admin_id'], $_SESSION['session_admin'][$sid]['aspID'] ) ;
	$can_initiate = AdminUsers_get_CanUserInitiate( $dbh, $_SESSION['session_admin'][$sid]['admin_id'] ) ;
	if ( !$can_initiate || !$INITIATE )
		$action = "close_console" ;
	$idle = time() - $FOOTPRINT_IDLE ;

	$rating_hash = Array() ;
	$rating_hash[4] = "Excellent" ;
	$rating_hash[3] = "Very Good" ;
	$rating_hash[2] = "Good" ;
	$rating_hash[1] = "Needs Improvement" ;
	$rating_hash[0] = "&nbsp;" ;

	// conditions

	if ( $action == "footprints" )
	{
		$do_pull = 0 ;
	}
	else if ( $action == "chat" )
	{
		$do_pull = 0 ;
	}
	else if ( $action == "transcripts" )
	{
		$do_pull = 0 ;
	}
	else if ( $action == "close_console" )
	{
		$do_pull = 0 ;
		$_SESSION['session_admin'][$sid]['traffic_monitor'] = 0 ;
		$class = "bg4" ;
	}
	else if ( $action == "open_console" )
	{
		$_SESSION['session_admin'][$sid]['traffic_monitor'] = 1 ;
	}
	else if ( $action == "push_survey" )
	{
		$do_pull = 0 ;
		ServiceFootprintUnique_update_FootprintValue( $dbh, $ip, "surveyID", $surveyid ) ;
		$flag_string = "$admin[userID]<:>$_GET[deptid]" ;
		$fp = fopen( "$DOCUMENT_ROOT/web/chatrequests/$ip.s", "wb+" ) ;
		fwrite( $fp, $flag_string, strlen( $flag_string ) ) ;
		fclose( $fp ) ;
	}
	else if ( $action == "survey" )
		$do_pull = 0 ;
	else if ( $action == "sound" )
		$_SESSION['session_admin'][$sid]['sound'] = $sound ;
	else if ( $start )
		$class = "bg4" ;
?>
<html>
<head>
<title> Operator [ visitor traffic monitor ] </title>
<?php $css_path = "../../" ; include_once( "../../css/default.php" ) ; ?>

<script language="JavaScript">
<!--
	var temp ;
	var newwin ;
	var url_initiate ;

	function start_pulling()
	{
		<?php if ( !$_SESSION['session_admin'][$sid]['traffic_monitor'] ): ?>
			parent.window.control_pull_traffic( "stop" ) ;
		<?php elseif ( !$do_pull ): ?>
			parent.window.control_pull_traffic( "stop" ) ;
		<?php else: ?>

			parent.window.traffic_monitor_on = 0 ;
			if ( parent.window.traffic_timer > 10 )
				temp = setTimeout("parent.window.control_pull_traffic('start')",<?php echo ( $admin['console_refresh'] * 1000 ) ?>) ;
			else
			{
				parent.window.control_pull_traffic('start') ;
				temp = setTimeout("start_pulling()",<?php echo ( $admin['console_refresh'] * 1000 ) ?> ) ;
			}

		<?php endif ; ?>
	}

	function config_chat( ip, page )
	{
		for ( c = 0; c < document.form.method.length; ++c )
		{
			if ( document.form.method[c].checked )
				method_index = document.form.method[c].value ;
		}
		dept_index = document.form.deptid.selectedIndex ;
		deptid = document.form.deptid[dept_index].value ;
		message_index = document.form.message.selectedIndex ;
		messageid = document.form.message[message_index].value ;
		page = escape( page ) ;
		url_initiate = "<?php echo $BASE_URL ?>/request.php?action=initiate&ip="+ip+"&sid=<?php echo $sid ?>&userid=<?php echo $_SESSION['session_admin'][$sid]['admin_id'] ?>&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&messageid="+messageid+"&pagex="+page+"&method="+method_index+"&deptid="+deptid+"&" ;
		temp = setTimeout("location.href = '<?php echo $BASE_URL ?>/admin/traffic/admin_puller.php?action=chat&start=1&rand=<?php echo $rand ?>&sid=<?php echo $sid ?>&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&ip=<?php echo $ip ?>'",15000) ;
	}

	function push_survey( form )
	{
		document.form.submit() ;
	}

	function switch_sound( flag )
	{
		location.href = "admin_puller.php?action=sound&sid=<?php echo $sid ?>&l=<?php echo $_SESSION['session_admin'][$sid]['asp_login'] ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&sound="+flag+"&counter=<?php echo $counter ?>" ;
	}

	// used for popup info box
	var info_array = new Array() ;
//-->
</script>

<style type="text/css">
<!--
#dek {POSITION:absolute;VISIBILITY:hidden;Z-INDEX:200;}
//-->
</style>

</head>
<body bgColor="#FFFFFF" text="#000000" link="#35356A" vlink="#35356A" alink="#35356A" marginheight="0" marginwidth="0" topmargin="0" leftmargin="0" OnLoad="start_pulling()" class="<?php echo $class ?>">


<?php
	if ( $action == "footprints" ):
	$footprint = ServiceFootprintUnique_get_IPFootprintInfo( $dbh, $ip, $x ) ;
?>
<table cellspacing=0 cellpadding=0 border=0 width="100%">
<form>
<tr bgColor="#5D85AA">
	<td>
		<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td><img src="<?php echo $BASE_URL ?>/images/op/tab_left.gif" width="5" height="18" border="0" alt=""></td>
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center.gif); background-repeat: repeat-x;">&nbsp;<a href="<?php echo $BASE_URL ?>/admin/traffic/admin_puller.php?sid=<?php echo $sid ?>&action=open_console&start=1&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>">traffic monitor</a>&nbsp;</td>
			<td><img src="<?php echo $BASE_URL ?>/images/op/tab_right_flap.gif" width="5" height="18" border="0" alt=""></td>

			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;"><img src="<?php echo $BASE_URL ?>/images/spacer.gif" width="5" height="18" border="0" alt=""></td>
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;">&nbsp;<a href="<?php echo $BASE_URL ?>/admin/traffic/console_timer.php?sid=<?php echo $sid ?>&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&admin_puller.php?counter=0">refresh rate</a>&nbsp;</td>
			<td width="5"><img src="<?php echo $BASE_URL ?>/images/op/tab_right_off.gif" width="5" height="18" border="0" alt=""></td>

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
	<td align="left"><span class="mini"></td>
</tr>
</form>
<tr>
	<td colspan=2 width="100%" class="tdtrafficborder">
	<table cellspacing=0 cellpadding=0 border=0 width="100%" bgColor="#F2F2F2">
	<tr> 
		<td colspan="2">
		&nbsp; <big><b><?php echo $ip ?></b></big> &rsaquo; view footprints <a href="<?php echo $BASE_URL ?>/admin/traffic/admin_puller.php?action=transcripts&rand=<?php echo $rand ?>&sid=<?php echo $sid ?>&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&ip=<?php echo $ip ?>"><font color="#669900">&rsaquo; past transcripts</font></a> <a href="<?php echo $BASE_URL ?>/admin/traffic/admin_puller.php?action=chat&rand=<?php echo $rand ?>&sid=<?php echo $sid ?>&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&ip=<?php echo $ip ?>"><font color="#669900">&rsaquo; initiate chat with visitor</a></td>
	</tr>
	<tr><td colspan="2"><img src="<?php echo $BASE_URL ?>/images/spacer.gif" width=1 height="12"></td></tr>
	<?php if ( !$footprint['ip'] ): ?>
	<tr><td colspan=2><span class="panelTitle"><font color="#FF0000">Visitor has left the site or is unavailable.</font></td></tr>
	<?php 
		else:
		$footprints_today = ServiceFootprint_get_DayFootprint( $dbh, $ip, $begin, $end, 0, $x, 0, 0 ) ;
		$footprints_beforetoday = ServiceFootprint_get_BeforeDayFootprint( $dbh, $ip, $begin, 20, $x ) ;
	?>

	<tr align="left"><td></td><td><b>pages visited today</td></tr>
	<?php
		for ( $c = 0; $c < count( $footprints_today );++$c )
		{
			$footprint = $footprints_today[$c] ;
			print "<tr><td align=\"left\" class=\"tdtrafficunder\">$footprint[total]</td><td align=\"left\" class=\"tdtrafficunder\"><a href=\"JavaScript:void(0)\" OnClick=\"window.open('$footprint[url]', 'newwin', 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1')\">$footprint[url]</a></td></tr>\n" ;
		}
	?>
	<tr><td colspan=2 class="hdash">&nbsp;</td></tr>
	<tr align="left" bgColor="#CBD3D9"><td></td><td class="tdtrafficunder">top 20 pages visited before today</td></tr>
	<?php
		for ( $c = 0; $c < count( $footprints_beforetoday );++$c )
		{
			$footprint = $footprints_beforetoday[$c] ;
			print "<tr bgColor=\"#CBD3D9\"><td class=\"tdtrafficunder\">$footprint[total]</td><td class=\"tdtrafficunder\"><a href=\"JavaScript:void(0)\" OnClick=\"window.open('$footprint[url]', 'newwin', 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1')\">$footprint[url]</a></td></tr>\n" ;
		}
	?>
	</table>
	</td>
</tr>
</table>

	<?php endif; ?>
<!-- end visitor footprings -->










<?php
	elseif ( $action == "transcripts" ):
	$footprint = ServiceFootprintUnique_get_IPFootprintInfo( $dbh, $ip, $x ) ;
	include_once("$DOCUMENT_ROOT/API/Util.php") ;
	include_once("$DOCUMENT_ROOT/API/Transcripts/get.php") ;
	$departments = AdminUsers_get_UserDepartments( $dbh, $_SESSION['session_admin'][$sid]['admin_id'] ) ;
	$dept_hash = Array() ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		$deptid = $department['deptID'] ;
		$dept_hash[$deptid] = $department['transcript_share'] ;
	}
	$transcripts = ServiceTranscripts_get_TranscriptsByIP( $dbh, $ip, $_SESSION['session_admin'][$sid]['aspID'] ) ;
?>
<table cellspacing=0 cellpadding=0 border=0 width="100%">
<form>
<tr bgColor="#5D85AA">
	<td>
		<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td><img src="<?php echo $BASE_URL ?>/images/op/tab_left.gif" width="5" height="18" border="0" alt=""></td>
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center.gif); background-repeat: repeat-x;">&nbsp;<a href="<?php echo $BASE_URL ?>/admin/traffic/admin_puller.php?sid=<?php echo $sid ?>&action=open_console&start=1&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>">traffic monitor</a>&nbsp;</td>
			<td><img src="<?php echo $BASE_URL ?>/images/op/tab_right_flap.gif" width="5" height="18" border="0" alt=""></td>

			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;"><img src="<?php echo $BASE_URL ?>/images/spacer.gif" width="5" height="18" border="0" alt=""></td>
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;">&nbsp;<a href="<?php echo $BASE_URL ?>/admin/traffic/console_timer.php?sid=<?php echo $sid ?>&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&admin_puller.php?counter=0">refresh rate</a>&nbsp;</td>
			<td width="5"><img src="<?php echo $BASE_URL ?>/images/op/tab_right_off.gif" width="5" height="18" border="0" alt=""></td>

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
	<td align="left"><span class="mini"></td>
</tr>
</form>
<tr>
	<td colspan=2 width="100%" class="tdtrafficborder">
	<table cellspacing=0 cellpadding=0 border=0 width="100%" bgColor="#F2F2F2">
	<tr> 
		<td colspan="2">
		&nbsp; <big><b><?php echo $ip ?></b></big> <a href="<?php echo $BASE_URL ?>/admin/traffic/admin_puller.php?action=footprints&rand=<?php echo $rand ?>&sid=<?php echo $sid ?>&l=<?php echo $l ?>&x=<?php echo $x ?>&ip=<?php echo $ip ?>"><font color="#669900">&rsaquo; view footprints</font></a> &rsaquo; past transcripts <a href="<?php echo $BASE_URL ?>/admin/traffic/admin_puller.php?action=chat&rand=<?php echo $rand ?>&sid=<?php echo $sid ?>&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&ip=<?php echo $ip ?>"><font color="#669900">&rsaquo; initiate chat with visitor</a></td>
	</tr>
	<tr><td colspan="2"><img src="<?php echo $BASE_URL ?>/images/spacer.gif" width=1 height="12"></td></tr>
	<?php if ( !$footprint['ip'] ): ?>
	<tr><td colspan=2><span class="panelTitle"><font color="#FF0000">Visitor has left the site or is unavailable.</font></td></tr>
	<?php else: ?>
	<tr>
		<td colspan=2>
			<table cellspacing="1" width="100%">
			<tbody class="subhead">
			<tr>
				<th align="left" width="190">Session Date</th>
				<th align="left">Department</th>
				<th align="left">Rating</th>
				<th align="left">Screen Name</th>
				<th align="left">Size</th>
				<th align="left">Duration</th>
			</tr>
			</tbody>
			<tbody>
			<?php
				for ( $c = 0; $c < count( $transcripts );++$c )
				{
					$transcript = $transcripts[$c] ;
					$deptinfo = AdminUsers_get_DeptInfo( $dbh, $transcript['deptID'], $_SESSION['session_admin'][$sid]['aspID'] ) ;
					$deptname = stripslashes( $deptinfo['name'] ) ;

					$rating = ( isset( $transcript['rating'] ) ) ? $transcript['rating'] : 0 ;
					$rating = $rating_hash[$rating] ;

					$duration = $transcript['created'] - $transcript['chat_session'] ;
					if ( $duration <= 0 ) { $duration = 1 ; }
					if ( $duration > 60 )
						$duration = round( $duration/60 ) . " min" ;
					else
						$duration = $duration . " sec" ;
					$date = date( "D M d, Y $TIMEZONE_FORMAT:i$TIMEZONE_AMPM", ( $transcript['created'] + $TIMEZONE ) ) ;

					$view_link = "<font color=\"#B0B0B0\">$date</font>" ;
					if ( isset( $dept_hash[$transcript['deptID']] ) )
						$view_link = "<a href=\"javascript:void(0)\" OnClick=\"window.open('$BASE_URL/admin/view_transcript.php?x=$x&l=$l&chat_session=$transcript[chat_session]&sid=$sid&requestid=&action=set&theme_admin=$admin[theme]', 'newwin', 'status=no,scrollbars=no,menubar=no,toolbar=no,resizable=yes,location=no,width=450,height=360')\">$date</a>" ;
					$size = Util_Format_Bytes( strlen( strip_tags( $transcript['plain'] ) ) ) ;
					print "<tr><td class=\"tdtrafficunder\">&raquo; $view_link</td><td class=\"tdtrafficunder\">$deptname</td><td class=\"tdtrafficunder\">$rating</td><td class=\"tdtrafficunder\">$transcript[from_screen_name]</td><td class=\"tdtrafficunder\">$size</td><td class=\"tdtrafficunder\">$duration</td></tr>\n" ;
					}
			?>
			</tbody>
			</table>
		</td>
	</tr>
	<?php endif ; ?>
	</table>
	</td>
</tr>
</table>












<?php
	elseif ( $action == "chat" ):
	$chatrequestinfo = ServiceChat_get_IPChatRequestInfo( $dbh, $x, $ip ) ;
	$admin_departments = AdminUsers_get_UserDepartments( $dbh, $_SESSION['session_admin'][$sid]['admin_id'] ) ;
	$select_dept = "" ;
	for ( $c = 0; $c < count( $admin_departments ); ++$c )
	{
		$department = $admin_departments[$c] ;
		$select_dept .= "<option value=\"$department[deptID]\">".stripslashes( $department['name'] )."</option>" ;
	}
	$canneds = ServiceCanned_get_UserCannedByType( $dbh, $_SESSION['session_admin'][$sid]['admin_id'], 0, 'i', '' ) ;
	$select_canned = "" ;
	for ( $c = 0; $c < count( $canneds ); ++$c )
	{
		$canned = $canneds[$c] ;
		$select_canned .= "<option value=\"$canned[cannedID]\">$canned[name]</option>" ;
	}

	$footprint = ServiceFootprintUnique_get_IPFootprintInfo( $dbh, $ip, $x ) ;
	$duration = $footprint['updated'] - $footprint['created'] ;
	if ( $duration > 60 )
		$duration = floor( $duration/60 ) . " min" ;
	else
		$duration = $duration . " sec" ;

	$start_date = mktime( 0,0,0,date("m"),date("j"),date("Y") ) ;
	$end_date = mktime( 23,59,59,date("m"),date("j"),date("Y") ) ;
	$total_initiated = ServiceChat_get_TotalInitiatedOnDate( $dbh, $x, $ip, $start_date, $end_date ) ;
	if ( count( $canneds ) <= 0 )
		$status = "<span class=\"smallTitle\"><a href=\"JavaScript:void(0)\" OnClick=\"window.open('$BASE_URL/admin/index.php?x=".$_SESSION['session_admin'][$sid]['aspID']."&sid=$sid&action=set&canned=1&pagex=initiate&', 'newwin', 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1')\"><font color=\"#FF0000\">You must create Canned Initiate Messages.</font></a></span>" ;
	else if ( $chatrequestinfo['requestID'] )
		$status = "<span class=\"smallTitle\"><font color=\"#FF0000\">Visitor is currently on a support call.</font></span>" ;
	else if ( $footprint['updated'] > $idle )
		$status = "<span class=\"smallTitle\"><form><input type=\"button\" OnClick=\"config_chat( '$footprint[ip]', '$footprint[url]' ); window.open(url_initiate, '$rand', 'scrollbars=no,menubar=no,resizable=0,location=no,width=450,height=635') ;\" value=\"Click to initiate chat with visitor.\"></form></span>" ;
	else
		$status = "<span class=\"smallTitle\"><font color=\"#FF0000\">Visitor has left the site or is unavailable.</font></font>" ;
?>
<table cellspacing=0 cellpadding=0 border=0 width="100%">
<form>
<tr bgColor="#5D85AA">
	<td>
		<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td><img src="<?php echo $BASE_URL ?>/images/op/tab_left.gif" width="5" height="18" border="0" alt=""></td>
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center.gif); background-repeat: repeat-x;">&nbsp;<a href="<?php echo $BASE_URL ?>/admin/traffic/admin_puller.php?sid=<?php echo $sid ?>&action=open_console&start=1&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>">traffic monitor</a>&nbsp;</td>
			<td><img src="<?php echo $BASE_URL ?>/images/op/tab_right_flap.gif" width="5" height="18" border="0" alt=""></td>

			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;"><img src="<?php echo $BASE_URL ?>/images/spacer.gif" width="5" height="18" border="0" alt=""></td>
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;">&nbsp;<a href="<?php echo $BASE_URL ?>/admin/traffic/console_timer.php?sid=<?php echo $sid ?>&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&admin_puller.php?counter=0">refresh rate</a>&nbsp;</td>
			<td width="5"><img src="<?php echo $BASE_URL ?>/images/op/tab_right_off.gif" width="5" height="18" border="0" alt=""></td>

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
	<td align="left"><span class="mini"></td>
</tr>
</form>
<tr>
	<td colspan=2 width="100%" class="tdtrafficborder">
	<table cellspacing=0 cellpadding=0 border=0 width="100%" bgColor="#F2F2F2">
	<tr> 
		<td colspan="2">
		&nbsp; <big><b><?php echo $ip ?></b></big> <a href="<?php echo $BASE_URL ?>/admin/traffic/admin_puller.php?action=footprints&rand=<?php echo $rand ?>&sid=<?php echo $sid ?>&l=<?php echo $l ?>&x=<?php echo $x ?>&ip=<?php echo $ip ?>"><font color="#669900">&rsaquo; view footprints</font></a> <a href="<?php echo $BASE_URL ?>/admin/traffic/admin_puller.php?action=transcripts&rand=<?php echo $rand ?>&sid=<?php echo $sid ?>&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&ip=<?php echo $ip ?>"><font color="#669900">&rsaquo; past transcripts</font></a> &rsaquo; initiate chat with visitor</td>
	</tr>
	<tr><td colspan="2"><img src="<?php echo $BASE_URL ?>/images/spacer.gif" width=1 height="12"></td></tr>
	<?php if ( !$footprint['ip'] ): ?>
	<tr><td colspan=2><span class="panelTitle"><font color="#FF0000">Visitor has left the site or is unavailable.</font></td></tr>
	<?php else: ?>
	<tr>
		<td colspan=2>
			<table cellspacing=0 cellpadding=1 border=0 width="100%">
			<form name="form" method="GET" action="<?php echo $BASE_URL ?>/admin/traffic/admin_puller.php" OnSubmit="alert('To initiate chat, click on the link below.'); return false">
			<input type="hidden" name="action" value="chat">
			<input type="hidden" name="rand" value="<?php echo $rand ?>">
			<input type="hidden" name="sid" value="<?php echo $sid ?>">
			<input type="hidden" name="l" value="<?php echo $l ?>">
			<input type="hidden" name="x" value="<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>">
			<input type="hidden" name="ip" value="<?php echo $ip ?>">
			<!-- <tr><td></td><td><span class="small">duration on site: <b><?php echo $duration ?></b> &nbsp; previously initiated <b><?php echo $total_initiated ?> time(s)</b></td></tr> -->
			<tr>
				<td align="right" width="70">On Page</td>
				<td align="left"><a href="JavaScript:void(0)" OnClick="window.open('<?php echo $footprint['url'] ?>', 'newwin', 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1')"><?php echo $footprint['url'] ?></a></td>
			</tr>
			<tr>
				<td colspan=2 class="tdtrafficborder" >
					<table cellspacing=0 cellpadding=0 border=0>
					<tr>
						<td>
							<table cellspacing=0 cellpadding=1 border=0>
							<tr>
								<td align="right" width="70" bgColor="#D5DBE2" class="tdtrafficborder">Department</td>
								<td><select name="deptid"><?php echo $select_dept ?></select></td>
							</tr>
							<tr>
								<td align="right" width="70" bgColor="#D5DBE2" class="tdtrafficborder">Question</td>
								<td nowrap><span class="small">
									<?php if ( count( $canneds ) <= 0 ): ?>
									<a href="JavaScript:void(0)" OnClick="window.open('<?php echo $BASE_URL ?>/admin/index.php?x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&sid=<?php echo $sid ?>&action=set&canned=1&pagex=initiate&', 'newwin', 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1')">create initiate message</a> - [ <a href="JavaScript:location.reload( true )">reload</a> ]
									<?php else: ?>
									<select name="message"><?php echo $select_canned ?></select> <a href="JavaScript:void(0)" OnClick="window.open('<?php echo $BASE_URL ?>/admin/index.php?x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&sid=<?php echo $sid ?>&action=set&canned=1&pagex=initiate&', 'newwin', 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1')">create</a> - [ <a href="JavaScript:location.reload( true )">reload</a> ]
									<?php endif; ?>
								</td>
							</tr>
							<tr>
								<td bgColor="#D5DBE2" class="tdtrafficborder" nowrap>Initiate Type</td>
								<td colspan=3 nowrap>
									<?php if ( count( $canneds ) > 0 ): ?>
									 <input type="radio" name="method" value="0" class="radio2" checked> pop-up |
									<input type="radio" name="method" value="2" class="radio2"> image scroll
									<?php endif; ?>
								</td>
							</tr>
							</form>
							</table>
						</td>
						<td><img src="<?php echo $BASE_URL ?>/images/spacer.gif" width="50" height=1></td>
						<td align="center" valign="center" width="100%">
							<table cellspacing=0 cellpadding=5 border=0>
							<tr>
								<td align="center"><b><?php echo $status ; ?></b></td>
							</tr>
							</table>
						</td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?php endif; ?>










<?php
	elseif ( !$_SESSION['session_admin'][$sid]['traffic_monitor'] ):
	$total_active_footprints = ServiceFootprintUnique_get_TotalActiveFootprints( $dbh, $_SESSION['session_admin'][$sid]['aspID'], $_SESSION['session_admin'][$sid]['dept_string'] ) ;
?>
<?php if ( $can_initiate && $INITIATE ): ?>
<script language="JavaScript">window.status = "" ;</script>
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
			<td width="5"><img src="<?php echo $BASE_URL ?>/images/op/tab_right_off.gif" width="5" height="18" border="0" alt=""></td>

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
</table>
<?php else: ?>
	&nbsp;
<?php endif ; ?>










<?php
	else:
	include_once("$DOCUMENT_ROOT/API/Transcripts/get.php") ;
	$footprints = ServiceFootprintUnique_get_ActiveFootprints( $dbh, $x, $_SESSION['session_admin'][$sid]['dept_string'] ) ;
	$total_active_footprints = count( $footprints ) ;
	$footprint_hash = Array() ;
	$ip_query = $ip_query_tran = "" ;
	for( $c = 0; $c < count( $footprints ); ++$c )
	{
		$footprint = $footprints[$c] ;
		$ip = $footprint['ip'] ;
		$footprint_hash[$ip] = Array() ;
		$ip_query .= "OR ip = '$ip' " ;
		$ip_query_tran .= "OR chatrequestlogs.ip = '$ip' " ;
	}
	
	$total_transcripts = ServiceTranscripts_get_TotalIPTranscripts( $dbh, $x, $ip_query_tran ) ;
	for ( $c = 0; $c < count( $total_transcripts ); ++$c )
	{
		$transcripts = $total_transcripts[$c] ;
		$ip = $transcripts['ip'] ;
		$footprint_hash[$ip]['transcripts'] = $transcripts['total'] ;
	}
	$total_initiates = ServiceChat_get_TotalInitiatedIps( $dbh, $x, $ip_query ) ;
	for ( $c = 0; $c < count( $total_initiates ); ++$c )
	{
		$total_initiate = $total_initiates[$c] ;
		$ip = $total_initiate['ip'] ;
		$footprint_hash[$ip]['total_initiate'] = $total_initiate['total'] ;
	}

	if ( $admin['console_refresh'] == 10 )
		$refresh_string = "when there is change in traffic" ;
	else
	{
		$minutes = $admin['console_refresh']/60 ;
		$refresh_string = "every $minutes minute(s)" ;
	}
?>
<script language="JavaScript">
	window.status = "<?php echo count( $footprints ) ?> visitors [ monitor set to update <?php echo $refresh_string ?> ]" ;
</script>
<table cellspacing=0 cellpadding=0 border=0 width="100%">
<form>
<tr bgColor="#5D85AA">
	<td>
		<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td><img src="<?php echo $BASE_URL ?>/images/op/tab_left.gif" width="5" height="18" border="0" alt=""></td>
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center.gif); background-repeat: repeat-x;">&nbsp;<a href="<?php echo $BASE_URL ?>/admin/traffic/admin_puller.php?sid=<?php echo $sid ?>&action=open_console&start=1&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>">traffic monitor</a>&nbsp;</td>
			<td><img src="<?php echo $BASE_URL ?>/images/op/tab_right_flap.gif" width="5" height="18" border="0" alt=""></td>

			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;"><img src="<?php echo $BASE_URL ?>/images/spacer.gif" width="5" height="18" border="0" alt=""></td>
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;">&nbsp;<a href="<?php echo $BASE_URL ?>/admin/traffic/console_timer.php?sid=<?php echo $sid ?>&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&admin_puller.php?counter=0">refresh rate</a>&nbsp;</td>
			<td width="5"><img src="<?php echo $BASE_URL ?>/images/op/tab_right_off.gif" width="5" height="18" border="0" alt=""></td>

			<?php if ( $admin['op2op'] && file_exists( "$DOCUMENT_ROOT/admin/traffic/ops.php" ) && ( AdminUsers_get_TotalUsers( $dbh, $_SESSION['session_admin'][$sid]['aspID'] ) > 1 ) ): ?>
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;"><img src="<?php echo $BASE_URL ?>/images/spacer.gif" width="5" height="18" border="0" alt=""></td>
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;">&nbsp;<a href="<?php echo $BASE_URL ?>/admin/traffic/ops.php?sid=<?php echo $sid ?>&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>&admin_puller.php?counter=0">operators</a>&nbsp;</td>
			<td><img src="<?php echo $BASE_URL ?>/images/op/tab_right_off.gif" width="5" height="18" border="0" alt=""></td>
			<?php endif ; ?>

			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;"><img src="<?php echo $BASE_URL ?>/images/spacer.gif" width="5" height="18" border="0" alt=""></td>
			<td style="background-image: url(<?php echo $BASE_URL ?>/images/op/tab_center_off.gif); background-repeat: repeat-x;">&nbsp;<a href="<?php echo $BASE_URL ?>/admin/traffic/admin_puller.php?counter=0&sid=<?php echo $sid ?>&action=close_console&start=1&l=<?php echo $l ?>&x=<?php echo $_SESSION['session_admin'][$sid]['aspID'] ?>">close</a>&nbsp;</td>
			<td><img src="<?php echo $BASE_URL ?>/images/op/tab_right_corner_off.gif" width="5" height="18" border="0" alt=""></td>
			<td><img src="<?php echo $BASE_URL ?>/images/spacer.gif" width="50" height=1><span class="small">traffic monitor sound : </td>
			<td><?php echo ( $_SESSION['session_admin'][$sid]['sound'] == "on" ) ? "<a href=\"JavaScript:switch_sound( 'off' )\"><img src=\"$BASE_URL/images/extra/sound_on.gif\" width=15 height=15 alt=\"Turn sound off.\" border=0\"></a>" : "<a href=\"JavaScript:switch_sound( 'on' )\"><img src=\"$BASE_URL/images/extra/sound_off.gif\" width=15 height=15 alt=\"Turn sound on.\" border=0\"></a>" ?></td>
		</tr>
		</table>
	</td>
</tr>
</form>
<tr>
	<td colspan=2 width="100%">
	&nbsp; <big><b><?php echo count( $footprints ) ?> visitor(s) on website</b></big><span class="small"> &raquo; traffic monitor will auto update <?php echo $refresh_string ?><br>
	<table cellspacing=0 cellpadding=1 border=0 width="100%">
	<?php
		for ( $c = 0; $c < count( $footprints ); ++$c )
		{
			$footprint = $footprints[$c] ;
			$ip = $footprint['ip'] ;

			$duration = $footprint['updated'] - $footprint['created'] ;

			$minutes = 1 ;
			if ( $duration > 60 )
			{
				$minutes = floor( $duration/60 ) ;
				$duration = $minutes . " min" ;
			}
			else
				$duration = $duration . " sec" ;

			//$hostname = gethostbyaddr( $footprint['ip'] ) ;
			$referinfo = ServiceRefer_get_ReferInfo( $dbh, $x, $footprint['ip'] ) ;
			$numfootprints = ( isset( $referinfo['numfootprints'] ) ) ? $referinfo['numfootprints'] : 0 ;

			$refer_url = "<i>not available</i>" ;
			if ( isset( $referinfo['refer_url'] ) )
			{
				// just take the quotes (' and ") out because some browsers (IE and others)
				// replaces them with the ' and " again, even if using special html chars
				$refer_url = preg_replace( "/\"/", "", stripslashes( $referinfo['refer_url'] ) ) ;
				$refer_url = preg_replace( "/'/", "", $refer_url ) ;
				$string_length = strlen( $refer_url ) ;
				//if ( $string_length > 75 )
				//	$refer_url = wordwrap( $refer_url, 75, "<br>", 1 ) ;
				$refer_url = "<a href=\"JavaScript:void(0)\" OnClick=\"window.open('$refer_url', 'newwin', 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1')\">$refer_url</a>" ;
			}

			$footprint_url = preg_replace( "/\"/", "", stripslashes( $footprint['url'] ) ) ;
			$footprint_url = preg_replace( "/'/", "", $footprint_url ) ;
			$string_length = strlen( $footprint_url ) ;
			//if ( $string_length > 75 )
			//	$footprint_url = wordwrap( $footprint_url, 75, "<br>", 1 ) ;

			$total_chat_requests = ( isset( $footprint_hash[$ip]['chat_requests'] )  ) ? $footprint_hash[$ip]['chat_requests'] : 0 ;
			$total_initiated = ( isset( $footprint_hash[$ip]['total_initiate'] )  ) ? $footprint_hash[$ip]['total_initiate'] : 0 ;
			$total_transcripts = ( isset( $footprint_hash[$ip]['transcripts'] )  ) ? $footprint_hash[$ip]['transcripts'] : 0 ;

			$exclude_string = "" ;
			if ( preg_match( "/$footprint[ip]/", $IPNOTRACK ) )
				$exclude_string = "<font color=\"#FF0000\">excluded IP</font>" ;

			$tracking_string = "<tr><td>&nbsp;</td></tr>" ;
			if ( $referinfo['trackID'] )
			{
				$trackinfo = ServiceClicks_get_TrackingURLInfoByID( $dbh, $_SESSION['session_admin'][$sid]['aspID'], $referinfo['trackID'] ) ;
				$tracking_name = stripslashes( $trackinfo['name'] ) ;
				$tracking_string = "<tr><td bgColor=\"$trackinfo[color]\"><span class=\"small\">$tracking_name</td></tr>" ;
			}
			
			// visitor importance values
			$imp_value = 0 ;
			if ( $referinfo['trackID'] )
				$imp_value += 20 ;
			$imp_value += $total_chat_requests * 3 ;
			$imp_value += $minutes ;

			$percent = $imp_value ;
			if ( $percent > 100 )
				$percent = 100 ;

			$percent_blank = 100 - $percent ;

			print "
				<tr>
					<td width=\"100%\" class=\"tdtrafficborder\">
						<table cellspacing=0 cellpadding= border=0 width=\"100%\">
						<tr>
							<td valign=\"top\" width=\"105\" bgColor=\"#D3DAE1\">
								<table cellspacing=0 cellpadding=0 border=0 width=\"100%\">
								<tr>
									<td nowrap><b>$ip</td>
								</tr>
								<tr><td><img src=\"$BASE_URL/images/spacer.gif\" width=105 height=2></td></tr>
								<tr>
									<td nowrap>duration: $duration</td>
								</tr>
								$tracking_string
								</table>
							</td>
							<td valign=\"top\" bgColor=\"#F2F2F2\">
								<table cellspacing=1 cellpadding=0 border=0>
								<tr>
									<td nowrap class=\"tdtrafficborder2\" valign=\"top\"><b>on page </td>
									<td class=\"tdtrafficunder\" valign=\"top\"> :<img src=\"$BASE_URL/images/spacer.gif\" width=2 height=1></td>
									<td valign=\"top\"><a href=\"JavaScript:void(0)\" OnClick=\"window.open('$footprint_url', 'newwin', 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1')\">$footprint_url</a></td>
								</tr>
								<tr>
									<td nowrap class=\"tdtrafficborder2\" valign=\"top\"><b>refer </td>
									<td class=\"tdtrafficunder\" valign=\"top\"> :<img src=\"$BASE_URL/images/spacer.gif\" width=2 height=1></td>
									<td valign=\"top\">$refer_url</td>
								</tr>
								<tr>
									<td nowrap class=\"tdtrafficborder2\" valign=\"top\"><b>options </td>
									<td valign=\"top\"> :<img src=\"$BASE_URL/images/spacer.gif\" width=2 height=1></td>
									<td valign=\"top\"><a href=\"$BASE_URL/admin/traffic/admin_puller.php?action=footprints&rand=$rand&sid=$sid&l=$l&x=$x&ip=$ip\"><font color=\"#669900\"><b>&rsaquo; footprints</b></font></a> &nbsp; <a href=\"$BASE_URL/admin/traffic/admin_puller.php?action=transcripts&rand=$rand&sid=$sid&l=$l&x=$x&ip=$ip\"><font color=\"#669900\"><b>&rsaquo; transcripts ($total_transcripts)</b></font></a> &nbsp; <a href=\"$BASE_URL/admin/traffic/admin_puller.php?action=chat&rand=$rand&sid=$sid&l=$l&x=$x&ip=$ip\"><font color=\"#669900\"><b>&rsaquo; initiate</b></font></a> &nbsp; <span class=\"small\">(op initiated: $total_initiated) &nbsp; $exclude_string</small></td>
								</tr>
								</table>
							</td>
						</tr>
						</table>
					</td>
				</tr>
			" ;
		}
	?>
	</table>
	<?php if ( $total_active_footprints <= 0 ): ?>
	<table><tr><td><big><b>Currently no traffic at this time...</td></tr></table>
	<?php endif ; ?>

	<?php
		if ( !$start && ( count( $footprints ) > 0 ) && ( $_SESSION['session_admin'][$sid]['sound'] == "on" ) && ( $total_active_footprints != $_SESSION['session_admin'][$sid]['active_footprints'] ) ):
	?>
		<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="//download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="0" height="0" id="cellular" align="middle"><param name="allowScriptAccess" value="sameDomain" /><param name="movie" value="<?php echo $BASE_URL ?>/sounds/doorbell.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" /><embed src="<?php echo $BASE_URL ?>/sounds/doorbell.swf" quality="high" bgcolor="#ffffff" width="0" height="0" name="cellular" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="//www.macromedia.com/go/getflashplayer" /></object>
	<?php endif ; ?>
	<?php $_SESSION['session_admin'][$sid]['active_footprints'] = $total_active_footprints ; ?>
	</td>
</tr>
</table>

<?php endif ; ?>
</body>
</html>
<?php
	mysql_close( $dbh['con'] ) ;
?>
