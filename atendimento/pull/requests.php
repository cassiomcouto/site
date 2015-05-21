<?php
	session_start() ;
	$session_admin = $_SESSION['session_admin'] ;
	$sid = ( isset( $_GET['sid'] ) ) ? $_GET['sid'] : $_POST['sid'] ;

	include_once("../web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/remove.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/update.php") ;

	// get admin info to see if there is kill signal
	$admin = AdminUsers_get_UserInfo( $dbh, $session_admin[$sid]['admin_id'], $session_admin[$sid]['aspID'] ) ;
	// update admin's activity time so it does not automatically have offline status
	AdminUsers_update_LastActiveTime( $dbh, $session_admin[$sid]['admin_id'], time(), $sid ) ;
	// auto update admin console to Online if offline because of connection lag
	if ( ( $admin['last_active_time'] > $admin_idle ) && ( $admin['utrigger'] == 1 ) && ( $admin['available_status'] == 0 ) )
		AdminUsers_update_AutoUpdateOnlineStatus( $dbh, $session_admin[$sid]['admin_id'] ) ;

	$total_requests = ServiceChat_get_UserTotalChatRequests( $dbh, $session_admin[$sid]['admin_id'] ) ;
	$_SESSION['session_admin'][$sid]['requests_reload'] = $total_requests ;

	// do the cleaning of the chat database and chat session txt files
	// of old requests and sessions.
	ServiceChat_remove_CleanChatSessionList( $dbh ) ;
	ServiceChat_remove_CleanChatSessions( $dbh ) ;
	ServiceChat_remove_CleanChatRequests( $dbh ) ;

	if ( ( $session_admin[$sid]['requests'] != $_SESSION['session_admin'][$sid]['requests_reload'] ) || ( $admin['signal'] == 9 ) )
		echo 1 ;
	else
		echo 0 ;
?>