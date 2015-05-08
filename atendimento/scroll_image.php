<?php
	$x = $l = "" ;
	if ( isset( $_GET['x'] ) ) { $x = $_GET['x'] ; }
	if ( isset( $_GET['l'] ) ) { $l = $_GET['l'] ; }
	
	include_once( "./API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( ".", $l ) )
	{
		print "<font color=\"#FF0000\">[Configuration Error: config files not found!] Exiting...</font>" ;
		exit ;
	}
	include_once("./web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("$DOCUMENT_ROOT/web/$l/$l-conf-init.php") ;

	// image to load
	$image = "$DOCUMENT_ROOT/images/initiate_chat.gif" ;
	if ( isset( $INITIATE_IMAGE ) && file_exists( "$DOCUMENT_ROOT/web/$l/$INITIATE_IMAGE" ) && $INITIATE_IMAGE && $INITIATE )
		$image = "$DOCUMENT_ROOT/web/$l/$INITIATE_IMAGE" ;
	Header( "Content-type: image/gif" ) ;
	readfile( $image ) ;
?>