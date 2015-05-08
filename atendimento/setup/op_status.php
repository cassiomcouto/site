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
	include_once("$DOCUMENT_ROOT/API/Util_Cal.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Opstatus/get.php") ;
	$section = 4;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = '<a href="processes.php?action=consol" class="nav">:: Previous</a>';
?>
<?php

	// initialize
	$action = $error_mesg = $adminid = $sessionid = "" ;
	$m = $y = $d = $success = $userid = 0 ;

	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
	{
		$text_width = "12" ;
		$text_display_width = "19" ;
	}
	else
	{
		$text_width = "9" ;
		$text_display_width = "10" ;
	}

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_POST['userid'] ) ) { $userid = $_POST['userid'] ; }
	if ( isset( $_GET['userid'] ) ) { $userid = $_GET['userid'] ; }
	if ( isset( $_GET['m'] ) ) { $m = $_GET['m'] ; }
	if ( isset( $_GET['d'] ) ) { $d = $_GET['d'] ; }
	if ( isset( $_GET['y'] ) ) { $y = $_GET['y'] ; }

	if ((!$m) || (!$y))
	{
		$m = date( "m",mktime() ) ;
		$y = date( "Y",mktime() ) ;
		$d = date( "j",mktime() ) ;
	}

	// the timespan to get the stats
	$stat_begin = mktime( 0,0,1,$m,$d,$y ) ;
	$stat_end = mktime( 23,59,59,$m,$d,$y ) ;

	$stat_date = date( "D F d, Y", $stat_begin ) ;

	$userinfo = AdminUsers_get_UserInfo( $dbh, $userid, $session_setup['aspID'] ) ;
	$logs = OpStatus_get_UserStatusLogs( $dbh, $userid, $session_setup['aspID'], $stat_begin, $stat_end ) ;
?>
<html>
<head>
<title> Admin Console Online/Offline Monitor </title>
<?php $css_path = ( !isset( $css_path ) ) ? $css_path = "../" : $css_path ; include_once( $css_path."css/default.php" ) ; ?>
<script language="JavaScript">
<!--
//-->
</script>

<body bgColor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="height:100%">
  <tr> 
	<td height="47" valign="top" class="bgMenuBack"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		  <td width="10"><img src="<?php echo $css_path ?>images/spacer.gif" width="10" height="1"></td>
		</tr>
	  </table></td>
  </tr>
  <tr>
	<td valign="top" class="bg">
		<table cellspacing=0 cellpadding=2 border=0>
		<tr>
			<td valign="top"><?php Util_Cal_DrawCalendar( $dbh, $m, $y, "op_status.php?userid=$userid", "op_status.php?userid=$userid", "", $action ) ; ?></td>
			<td valign="top">
				 <b><?php echo stripslashes( $userinfo['name'] ) ?></b>'s online/offline status activity accurate to the +/- several minutes.
				 <form name="display">
				 Total Duration Online:<br><br><input type="text" name="duration" size="<?php echo $text_display_width ?>" maxlength="50" style="color : #002E5B; font-family : Arial, Helvetica, sans-serif; font-size : 12px; font-weight : bold; border-color : #F2F2F2; background : #F2F2F2;" >
				 </form>
			</td>
		</tr>
		<tr>
			<td colspan=2>
			<b><?php echo $stat_date ?></b><br>
			<table width="100%" border=0 cellpadding=2 cellspacing=1>
			  <tr align="left">
				<th nowrap>Name</th>
				<th width="75" nowrap>Status</th>
				<th>Time</th>
			  </tr>
			  <?php
				$duration = $total_duration = 0 ;
				for ( $c = 0; $c < count( $logs ); ++$c )
				{
					$log = $logs[$c] ;
					
					$created = date( "$TIMEZONE_FORMAT:i$TIMEZONE_AMPM", ( $log['created'] + $TIMEZONE ) ) ;

					$status = ( $log['status'] ) ? "Online" : "Offline" ;
					$status_color = ( $log['status'] ) ? "#E1FFE9" : "#FFE8E8" ;
					$name = stripslashes( $log['name'] ) ;

					$class = "class=\"altcolor1\"" ;
					if ( $c % 2 )
						$class = "class=\"altcolor2\"" ;

					$duration_display = "&nbsp;" ;
					if ( $log['status'] && isset( $logs[$c+1] ) )
					{
						$next_log = $logs[$c+1] ;
						$duration = $next_log['created'] - $log['created'] ;
						if ( $duration > 60 )
						{
							// if over 1 hour, then they must have left and came back
							// ... make it n/a so it does not display skewed data
							if ( $duration > 3600 )
								$duration_display = "n/a" ;
							else
								$duration_display = round( $duration/60 ) . " <font color=\"#FF6666\">min</font>" ;
						}
						else
							$duration_display = $duration . " sec" ;
						$total_duration += $duration ;
					}

					print "
						<tr $class>
							<td>$name</td>
							<td bgColor=\"$status_color\">$status</td>
							<td>$created</td>
						</tr>
					" ;
				}

				if ( $total_duration > 60 )
				{
					// if over 1 hour, then they must have left and came back
					// ... make it n/a so it does not display skewed data
					if ( $total_duration > 3600 )
					{
						$remainder = round( ( $total_duration - ( floor( $total_duration/3600 ) * 3600 ) )/60 ) ;
						$hours = floor( $total_duration/3600 ) ;
						$duration_display =  $hours . " <font color=\"#FF6666\">hour(s)</font> and $remainder <font color=\"#FF6666\">min</font>" ;
					}
					else
						$duration_display = round( $total_duration/60 ) . " <font color=\"#FF6666\">min</font>" ;
				}
				else
					$duration_display = $total_duration . " sec" ;
			?>
			</table>
			<script language="JavaScript">document.display.duration.value = '<?php echo strip_tags( $duration_display ) ?>' ;</script>
			</td>
		</tr>
		</table>
		<br><br>
</td>
  </tr>
  <tr> 
	<td height="20" align="right" class="bgFooter" style="height:20px"><img src="<?php echo $css_path ?>images/bg_corner_footer.gif" alt="" width="94" height="20"></td>
  </tr>
  <tr>
	<td height="20" align="center" class="bgCopyright" style="height:20px">
		<?php echo $LANG['DEFAULT_BRANDING'] ?>
		v<?php echo $PHPLIVE_VERSION ?> Sistema de Atendimento Online
	</td>
  </tr>
</table>

</body>
</html>
