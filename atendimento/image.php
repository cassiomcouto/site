<?php
	$l = $_GET['l'] ;

	include_once( "./API/Util_Dir.php" ) ;
//	if ( !Util_DIR_CheckDir( ".", $l ) )
/*	{
		if ( preg_match( "/(chatsupportlive.com)|(osicode.com)|(osicodes.com)|(osicodes.net)|(phplivesupport.com)/", $_SERVER['SERVER_NAME'] ) )
		{
			$image_path = "http://www.codedeli.com/pics/ad.gif" ;
			Header( "Content-type: image/gif" ) ;
			readfile( $image_path ) ;
		}
		else
			print "<font color=\"#FF0000\">Config error: reason: $_GET[l] config not found!  Exiting... [image.php]</font>" ;
		exit ;
	}*/
	include_once("./web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("$DOCUMENT_ROOT/web/$l/$l-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/update.php") ;
	include_once("$DOCUMENT_ROOT/API/Footprint/put.php") ;
	include_once("$DOCUMENT_ROOT/API/Refer/put.php") ;
	include_once("$DOCUMENT_ROOT/API/Footprint_unique/remove.php") ;
	include_once("$DOCUMENT_ROOT/API/Spam/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Spam/remove.php") ;

	if ( $SUPPORT_LOGO_OFFLINE )
		$status_image = $SUPPORT_LOGO_OFFLINE ;
	else
		$status_image = "phplive_support_offline.gif" ;

	if ( isset( $_GET['deptid'] ) ) { $deptid = $_GET['deptid'] ; } else { $deptid = "" ; }
	if ( isset( $_GET['pagex'] ) ) { $page = urldecode( $_GET['pagex'] ) ; } else { $page = "" ; }
	if ( isset( $_GET['text'] ) ) { $text = $_GET['text'] ; } else { $text = "" ; }

	ServiceSpam_remove_CleanOldIPs( $dbh ) ;
	ServiceFootprintUnique_remove_IdleFootprints( $dbh, $_GET['x'] ) ;
	// update all admins status to not available if they have been idle
	AdminUsers_update_IdleAdminStatus( $dbh, $admin_idle ) ;
	$deptinfo = AdminUsers_get_DeptInfo( $dbh, $deptid, $_GET['x'] ) ;
	if ( $deptinfo['status_image_offline'] && file_exists( "$DOCUMENT_ROOT/web/".$_GET['l']."/$deptinfo[status_image_offline]" ) )
		$status_image = $deptinfo['status_image_offline'] ;

	$blocked = 0 ;
	$ips = ServiceSpam_get_IPs( $dbh, $_GET['x'] ) ;
	for ( $c = 0; $c < count( $ips ); ++$c )
	{
		$ip = $ips[$c] ;
		if ( $ip['ip'] == $_SERVER['REMOTE_ADDR'] )
		{
			if ( $SUPPORT_LOGO_OFFLINE )
				$status_image = $SUPPORT_LOGO_OFFLINE ;
			else if ( isset( $deptinfo['status_image_offline'] ) )
				$status_image = $deptinfo['status_image_offline'] ;
			else
				$status_image = "phplive_support_offline.gif" ;
			$blocked = 1 ;
			break ;
		}
	}

	if ( !$blocked )
	{
		if ( AdminUsers_get_AreAnyAdminOnline( $dbh, $deptid, $admin_idle, $_GET['x'] ) )
		{
			if ( $SUPPORT_LOGO_ONLINE )
				$status_image = $SUPPORT_LOGO_ONLINE ;
			else
				$status_image = "phplive_support_online.gif" ;

			if ( $deptinfo['status_image_online'] && file_exists( "$DOCUMENT_ROOT/web/".$_GET['l']."/$deptinfo[status_image_online]" ) )
				$status_image = $deptinfo['status_image_online'] ;
		}
		/*else if ( AdminUsers_get_AreAnyAdminConsolesOnline( $dbh, $deptid, $admin_idle, $_GET['x'] ) )
		{
			if ( $SUPPORT_LOGO_AWAY )
				$status_image = $SUPPORT_LOGO_AWAY ;
			else
				$status_image = "phplive_support_away.gif" ;

			if ( $deptinfo['status_image_away'] && file_exists( "$DOCUMENT_ROOT/web/".$_GET['l']."/$deptinfo[status_image_away]" ) )
				$status_image = $deptinfo['status_image_away'] ;
		}*/

		// get ips that SHOULD NOT be tracked
		$ip_notrack_string = "" ;
		if ( isset( $IPNOTRACK ) )
			$ip_notrack_string = $IPNOTRACK ;

		// do the tracking, if needed
		if ( !preg_match( "/$_SERVER[REMOTE_ADDR]/", $ip_notrack_string ) )
			ServiceFootprint_put_Footprint( $dbh, $_SERVER['REMOTE_ADDR'], $page, $_GET['x'] ) ;
		
		// track refer
		$refer = "" ;
		if ( isset( $_GET['refer'] ) )
			$refer = urldecode( $_GET['refer'] ) ;
		ServiceRefer_put_Refer( $dbh, $_GET['x'], $_SERVER['REMOTE_ADDR'], $refer, 0 ) ;
	}

	if ( file_exists( "$DOCUMENT_ROOT/web/".$_GET['l']."/$status_image" ) && $status_image )
		$image_path = "$DOCUMENT_ROOT/web/".$_GET['l']."/$status_image" ;
	else
		$image_path = "$DOCUMENT_ROOT/images/$status_image" ;

	// override image setting if "text" variable is present... spit out
	// 1x1 pixle
	if ( $text )
		$image_path = "$DOCUMENT_ROOT/images/counters/1.gif" ;

	$from_page = "" ;
	if ( isset( $_SERVER['HTTP_REFERER'] ) ) {  $from_page = $_SERVER['HTTP_REFERER'] ; }
	if ( !$from_page )
		$from_page = $page;
	preg_match( "/^(http:\/\/)?([^\/]+)/i", $from_page, $matches ) ;
	// check to see if domain is internal (such as localhost). if it is, skip the
	// second layer of domain name check (taking out www. from www.domain.com)
	$domain = "" ;
	if ( isset( $matches[2] ) )
	{
		$domain = $matches[2] ;
		if ( preg_match( "/\./", $domain ) && $domain )
		{
			preg_match( "/[^\.\/]+\.[^\.\/]+$/", $domain, $matches ) ;
			$domain = $matches[0] ;
		}
	}

	mysql_close( $dbh['con'] ) ;
	Header( "Content-type: image/gif" ) ;
	readfile( $image_path ) ;
?>