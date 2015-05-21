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
	include_once("$DOCUMENT_ROOT/web/$session_setup[login]/$session_setup[login]-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/Form.php") ;
	$section = 5;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = '<a href="options.php" class="nav">:: Home</a>';
?>
<?php

	// initialize
	$action = "" ;
	$success = 0 ;
	$error_mesg = "" ;

	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
		$text_width = "12" ;
	else
		$text_width = "9" ;

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_POST['success'] ) ) { $success = $_POST['success'] ; }
	if ( isset( $_GET['success'] ) ) { $success = $_GET['success'] ; }
?>
<?php
	// functions
?>
<?php
	// conditions
	if ( $action == "exclude_ip" )
	{
		$action = "footprints" ;
		$ip_notrack_string = $IPNOTRACK ;
		$new_ip = $_POST['ip1'].".".$_POST['ip2'].".".$_POST['ip3'].".".$_POST['ip4']." " ;

		// make sure it's not already in the list
		if ( !preg_match( "/$new_ip/", $ip_notrack_string ) )
			$ip_notrack_string .= $new_ip ;
		$COMPANY_NAME = addslashes( $COMPANY_NAME ) ;

		$conf_string = "0LEFT_ARROW0?php
			\$LOGO = '$LOGO' ;
			\$COMPANY_NAME = '$COMPANY_NAME' ;
			\$SUPPORT_LOGO_ONLINE = '$SUPPORT_LOGO_ONLINE' ;
			\$SUPPORT_LOGO_OFFLINE = '$SUPPORT_LOGO_OFFLINE' ;
			\$SUPPORT_LOGO_AWAY = '$SUPPORT_LOGO_AWAY' ;
			\$VISITOR_FOOTPRINT = '$VISITOR_FOOTPRINT' ;
			\$THEME = '$THEME' ;
			\$POLL_TIME = '$POLL_TIME' ;
			\$INITIATE = '$INITIATE' ;
			\$INITIATE_IMAGE = '$INITIATE_IMAGE' ;
			\$IPNOTRACK = '$ip_notrack_string' ;
			\$LANG_PACK = '$LANG_PACK' ;?0RIGHT_ARROW0" ;

		$conf_string = preg_replace( "/0LEFT_ARROW0/", "<", $conf_string ) ;
		$conf_string = preg_replace( "/0RIGHT_ARROW0/", ">", $conf_string ) ;
		$fp = fopen ("../web/$session_setup[login]/$session_setup[login]-conf-init.php", "wb+") ;
		fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
		fclose( $fp ) ;

		$IPNOTRACK = $ip_notrack_string ;
		$success = 1 ;
	}
	else if ( $action == "remove_excluded_ip" )
	{
		$action = "footprints" ;
		$ip_notrack_string = $IPNOTRACK ;
		$ip_notrack_string = preg_replace( "/$_POST[excluded_ips] /", "", $ip_notrack_string ) ;
		$COMPANY_NAME = addslashes( $COMPANY_NAME ) ;

		$conf_string = "0LEFT_ARROW0?php
			\$LOGO = '$LOGO' ;
			\$COMPANY_NAME = '$COMPANY_NAME' ;
			\$SUPPORT_LOGO_ONLINE = '$SUPPORT_LOGO_ONLINE' ;
			\$SUPPORT_LOGO_OFFLINE = '$SUPPORT_LOGO_OFFLINE' ;
			\$SUPPORT_LOGO_AWAY = '$SUPPORT_LOGO_AWAY' ;
			\$VISITOR_FOOTPRINT = '$VISITOR_FOOTPRINT' ;
			\$THEME = '$THEME' ;
			\$POLL_TIME = '$POLL_TIME' ;
			\$INITIATE = '$INITIATE' ;
			\$INITIATE_IMAGE = '$INITIATE_IMAGE' ;
			\$IPNOTRACK = '$ip_notrack_string' ;
			\$LANG_PACK = '$LANG_PACK' ;?0RIGHT_ARROW0" ;

		$conf_string = preg_replace( "/0LEFT_ARROW0/", "<", $conf_string ) ;
		$conf_string = preg_replace( "/0RIGHT_ARROW0/", ">", $conf_string ) ;
		$fp = fopen ("../web/$session_setup[login]/$session_setup[login]-conf-init.php", "wb+") ;
		fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
		fclose( $fp ) ;

		$IPNOTRACK = $ip_notrack_string ;
		$success = 1 ;
	}
	else if ( $action == "update_timezone" )
	{
		$action = "timezone" ;
		$hour = $_POST['hour'] ;
		if ( $_POST['ampm'] == "pm" )
			$hour += 12 ;
		$my_time = mktime( $hour, $_POST['minute'], date( "s", time() ), $_POST['month'], $_POST['day'], date( "Y", time() ) ) ;
		$system_time = time() ;
		$timezone = $my_time - $system_time ;

		LIST( $COMPANY_NAME ) = EXPLODE( "<:>", $COMPANY_NAME ) ;
		$COMPANY_NAME = addslashes( $COMPANY_NAME ) ;

		$conf_string = "0LEFT_ARROW0?php
			\$LOGO = '$LOGO' ;
			\$COMPANY_NAME = '$COMPANY_NAME<:>$_POST[format]$timezone' ;
			\$SUPPORT_LOGO_ONLINE = '$SUPPORT_LOGO_ONLINE' ;
			\$SUPPORT_LOGO_OFFLINE = '$SUPPORT_LOGO_OFFLINE' ;
			\$SUPPORT_LOGO_AWAY = '$SUPPORT_LOGO_AWAY' ;
			\$VISITOR_FOOTPRINT = '$VISITOR_FOOTPRINT' ;
			\$THEME = '$THEME' ;
			\$POLL_TIME = '$POLL_TIME' ;
			\$INITIATE = '$INITIATE' ;
			\$INITIATE_IMAGE = '$INITIATE_IMAGE' ;
			\$IPNOTRACK = '$IPNOTRACK' ;
			\$LANG_PACK = '$LANG_PACK' ;?0RIGHT_ARROW0" ;

		$conf_string = preg_replace( "/0LEFT_ARROW0/", "<", $conf_string ) ;
		$conf_string = preg_replace( "/0RIGHT_ARROW0/", ">", $conf_string ) ;
		$fp = fopen ("../web/$session_setup[login]/$session_setup[login]-conf-init.php", "wb+") ;
		fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
		fclose( $fp ) ;

		HEADER( "location: prefs.php?action=timezone&success=1" ) ;
		exit ;
	}
?>
<?php include_once("./header.php") ; ?>
<script language="JavaScript">
<!--
	function add_ip()
	{
		if ( ( document.ip.ip1.value == "" ) || ( document.ip.ip2.value == "" )
			|| ( document.ip.ip3.value == "" ) || ( document.ip.ip4.value == "" ) )
			alert( "IP is Invalid." ) ;
		else if ( ( document.ip.ip1.value > 255 ) || ( document.ip.ip2.value > 255 )
			|| ( document.ip.ip3.value > 255 ) || ( document.ip.ip4.value > 255 ) )
			alert( "Each IP value cannot be greater then 255." ) ;
		else
		{
			if ( confirm( "Don't track page view data and footprints for this IP?" ) )
				document.ip.submit() ;
		}
	}

	function do_remove_ip( index )
	{
		if ( index < 0 )
			alert( "Please select an IP to remove from list." ) ;
		else
		{
			if ( confirm( "Remove this IP from exclude list?" ) )
				document.ip_excluded.submit() ;
		}
	}

	function update_tracking()
	{
		if ( confirm( "Are you sure?" ) )
			document.tracking.submit() ;
	}

	function update_polling()
	{
		if ( document.polling.polltime.value < 20 )
			alert( "Must be at LEAST 20 seconds or more." ) ;
		else
			document.polling.submit() ;
	}

	function do_update_timezone()
	{
		if ( confirm( "Update Time?" ) )
			document.form.submit() ;
	}
//-->
</script>
<?php 
	if ( $action == "footprints" ):
	if ( $VISITOR_FOOTPRINT == 0 )
		$checked = "checked" ;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
  <td height="100%" valign="top"> <p><span class="title">Preferences: Internal Excluded IPs </span><br>
	  To avoid misleading visitor page view and footprint tracking stats, 
	  you can <strong>EXCLUDE IPs</strong> from the tracking system.  <?php echo ( isset( $success ) && $success ) ? "<font color=\"#29C029\"><big><b>Update Success!</b></big></font>" : "" ?></p>
	<ul>
	  <li>All IPs that are EXCLUDED WILL NOT be tracked for the total 
		page views and visitor footprint data.</li>
	  <li>This is useful when you are developing your site and need to 
		refresh your pages quite often, and DON'T want to track each test 
		visit. Also quite useful so your internal company visits are not 
		tracked.</li>
	</ul>
	<p>NOTE: excluded IPs do not get stored to the database for footprint 
	  and page view stat reporting, but they will still show on the operator 
	  traffic monitor.</p>
	<p>Your current IP is: <span class="hilight"><?php echo $_SERVER['REMOTE_ADDR'] ?></span></p>
	<table border="0" cellpadding="1" cellspacing="2">
	  <form method="POST" action="prefs.php" name="ip_excluded">
		<tr> 
		  <td colspan="4" valign="top"><strong>Exclude IP</strong> </td>
		  <input type="hidden" name="action" value="remove_excluded_ip">
		  <td width="300" rowspan="3" align="center" valign="top"> 
			<select name="excluded_ips" size=5 style="width:200;font-size:12px" width="200">
			<?php
				$ips = explode( " ", $IPNOTRACK ) ;
				for( $c = 0; $c < count( $ips ); ++$c )
				{
					if ( $ips[$c] )
						print "<option value=\"$ips[$c]\">$ips[$c]</option>" ;
				}
			?>
			</select> <br>
			[<a href="JavaScript:do_remove_ip(document.ip_excluded.excluded_ips.selectedIndex)">remove 
			SELECTED ip from list</a>]</td>
		</tr>
	  </form>
	  <form method="POST" action="prefs.php" name="ip">
		<input type="hidden" name="action" value="exclude_ip">
		<tr> 
		  <td valign="top"> <input type="text" name="ip1" size=3 maxlength=3 style="width:30px;" onKeyPress="return numbersonly(event)"></td>
		  <td valign="top"><input type="text" name="ip2" size=3 maxlength=3 style="width:30px;" onKeyPress="return numbersonly(event)"></td>
		  <td valign="top"><input type="text" name="ip3" size=3 maxlength=3 style="width:30px;" onKeyPress="return numbersonly(event)"></td>
		  <td valign="top"><input type="text" name="ip4" size=3 maxlength=3 style="width:30px;" onKeyPress="return numbersonly(event)"></td>
		</tr>
		<tr> 
		  <td colspan="4" valign="top"> 
			<input type="button" class="mainButton" value="Add IP Address" OnClick="add_ip()">
		  </td>
		</tr>
	  </form>
	</table>
	<p>&nbsp;</p></td>






<?php
	elseif ( $action == "timezone" ):
	$sys_date = date( "F d, Y (h:i:s a)", time() ) ;
	$your_date = date( "F d, Y ($TIMEZONE_FORMAT:i:s$TIMEZONE_AMPM)", ( time() + $TIMEZONE ) ) ;
	$month = date( "m", ( time() + $TIMEZONE ) ) ;
	$day = date( "d", ( time() + $TIMEZONE ) ) ;
	$year = date( "Y", ( time() + $TIMEZONE ) ) ;
	$hour = date( "h", ( time() + $TIMEZONE ) ) ;
	$minute = date( "i", ( time() + $TIMEZONE ) ) ;
	$ampm = date( "a", ( time() + $TIMEZONE ) ) ;
	$tformat = $TIMEZONE_FORMAT ;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
	<td width="100%" valign="top"> <p><span class="title">Preferences: Time Zone </span><br>
		Set the Time Zone to match your current location.  <?php echo ( isset( $success ) && $success ) ? "<font color=\"#29C029\"><big><b>Update Success!</b></big></font>" : "" ?></p>

		The system time is: <b><?php echo $sys_date ?></b><br>
		Your current time is: <b><?php echo $your_date ?></b>
		<p>

		<table cellspacing=0 cellpadding=1 border=0>
		<form method="POST" action="prefs.php" name="form">
		<input type="hidden" name="action" value="update_timezone">
		<tr>
			<td>Your Date: </td>
			<td>
				<select name="month">
				<?php
					for( $c = 1; $c <= 12; ++$c )
					{
						$this_month = date( "m", mktime (0,0,0,$c,1,$year) ) ;
						$this_month_display = date( "F", mktime (0,0,0,$c,1,$year) ) ;
						$selected = "" ;
						if ( $this_month == $month )
							$selected = "selected" ;
						print "<option value=\"$this_month\" $selected>$this_month_display</option>" ;
					}
				?>
				</select>
			</td>
			<td><select name="day"><?php echo numbers( $day, 1, 31 ) ; ?></select></td>
			<td>&nbsp;</td>
			<td>Time: </td>
			<td><select name="hour"><?php echo numbers( $hour, 1, 12 ) ; ?></select>:<select name="minute"><?php echo numbers_fill( $minute, 0, 59 ) ; ?></select></td>
			<td><select name="ampm"><?php echo ( $ampm == "am" ) ? "<option value=am selected>am</option><option value=pm>pm</option>" : "<option value=am>am</option><option value=pm selected>pm</option>" ?></select></td>
		</tr>
		<tr>
			<td>Format: </td>
			<td colspan=4><select name="format"><option value="h" <?php echo ( $tformat == "h" ) ? "selected" : "" ?>>12 hour</option><option value="H" <?php echo ( $tformat == "H" ) ? "selected" : "" ?>>24 hour</option></select></td>
		</tr>
		<tr><td colspan=4>&nbsp;</td></tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan=4><input type="button" class="mainButton" onClick="do_update_timezone()" value="Update Time"> </td>
		</tr>
		</form>
		</table>
	</td>



<?php else: ?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
    <td width="100%" height="350" valign="top"> 
	  <p><span class="title">Preferences</span><br></p>
	  <p>
		To avoid misleading visitor page view and footprint tracking stats, you can EXCLUDE IPs from the tracking system.<br>
		<big><li> <strong><a href="prefs.php?action=footprints">Exclude IP Tracking</a></strong></big></p>
	  <p>
		"Email Transcripts" message settings and transcript letter.<br>
		<big><li> <strong><a href="email_transcript.php">Email Transcript</a></strong></big></p>
	 <p>
		Set the Time Zone to match your current time at your current location.<br>
		<big><li> <strong><a href="prefs.php?action=timezone">Time Zone</a></strong></big></p>
	  </td>


<?php endif ;?>
  <td style="background-image: url(../images/g_prefs_big.jpg);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="350"></td>
</tr>
 </table>
<?php include_once( "./footer.php" ) ; ?>