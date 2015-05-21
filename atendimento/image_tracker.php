<?php
	$page = $x = $l = $deptid = $action = "" ;
	if ( isset( $_GET['pagex'] ) ) { $page = $_GET['pagex'] ; }
	if ( isset( $_GET['deptid'] ) ) { $deptid = $_GET['deptid'] ; }
	if ( isset( $_GET['x'] ) ) { $x = $_GET['x'] ; }
	if ( isset( $_GET['l'] ) ) { $l = $_GET['l'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }

	include_once("./web/conf-init.php") ;
	include_once( "./API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( ".", $l ) )
	{
		$image_path = "$DOCUMENT_ROOT/images/counters/0.gif" ;
		Header( "Content-type: image/gif" ) ;
		readfile( $image_path ) ;
		exit ;
	}
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("$DOCUMENT_ROOT/web/$l/$l-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Footprint_unique/put.php") ;

	// image to load, if no request is made
	$image_path_no = "$DOCUMENT_ROOT/images/empty_nodelete.gif" ;
	// image to load if request is made (popup)
	$image_path_popup = "$DOCUMENT_ROOT/images/empty_nodelete2.gif" ;
	// image to load if request is made (scroll)
	$image_path_scroll = "$DOCUMENT_ROOT/images/empty_nodelete3.gif" ;

	$remote_addr = $_SERVER['REMOTE_ADDR'] ;
	if ( $action == "reject" )
	{
		include_once("$DOCUMENT_ROOT/API/Chat/update.php") ;
		include_once("$DOCUMENT_ROOT/API/Chat/Util.php") ;
		$initiate_file = "$remote_addr.pop" ;
		if ( file_exists( "$DOCUMENT_ROOT/web/chatrequests/$remote_addr.scr" ) )
			$initiate_file = "$remote_addr.scr" ;
		$requestarray = file( "$DOCUMENT_ROOT/web/chatrequests/$initiate_file" ) ;
		$requestid = rtrim( $requestarray[0] ) ;
		$sessionid = rtrim ( $requestarray[1] ) ;
		$adminid = rtrim( $requestarray[2] ) ;
		ServiceChat_update_ChatRequestLogStatus( $dbh, $sessionid, 5 ) ;
		$message = "Visitante não aceitou a sessão de chat." ;
		$string = "window.parent.frames[\"main\"].window.addMessage( \"$message\", \"\", \"alert\", \"receive\" ) ;<br$sessionid> window.parent.frames[\"main\"].window.stop_timer() ;" ;
		$transcript_string = "<p class=\"alert\">$message</p><br$sessionid>" ;
		UtilChat_AppendToChatfile( $sessionid."_admin.txt", $string ) ;
		UtilChat_AppendToChatfile( $sessionid."_transcript.txt", $transcript_string ) ;
		unlink( "$DOCUMENT_ROOT/web/chatrequests/$initiate_file" ) ;
	}
	else if ( $action == "notifypopup" )
	{
		include_once("$DOCUMENT_ROOT/API/Chat/Util.php") ;
		$initiate_file = "$remote_addr.pop" ;
		if ( file_exists( "$DOCUMENT_ROOT/web/chatrequests/$remote_addr.scr" ) )
			$initiate_file = "$remote_addr.scr" ;
		$requestarray = file( "$DOCUMENT_ROOT/web/chatrequests/$initiate_file" ) ;
		$requestid = rtrim( $requestarray[0] ) ;
		$sessionid = rtrim ( $requestarray[1] ) ;
		$adminid = rtrim( $requestarray[2] ) ;
		$message = "Detectando bloqueador de popup..." ;
		$string = "window.parent.frames[\"main\"].window.addMessage( \"$message\", \"\", \"notice\", \"receive\" ) ;<br$sessionid> window.parent.frames[\"main\"].window.stop_timer() ;" ;
		UtilChat_AppendToChatfile( $sessionid."_admin.txt", $string ) ;
	}
	else
	{
		$page = urldecode( $page ) ;
		ServiceFootprintUnique_put_Footprint( $dbh, $remote_addr, $page, $x, $deptid ) ;
	}
	
	Header( "Content-type: image/gif" ) ;
	if ( file_exists( "$DOCUMENT_ROOT/web/chatrequests/$remote_addr.pop" ) && $remote_addr )
		readfile( $image_path_popup ) ;
	else if ( file_exists( "$DOCUMENT_ROOT/web/chatrequests/$remote_addr.scr" ) && $remote_addr )
		readfile( $image_path_scroll ) ;
	else
		readfile( $image_path_no ) ;
?>