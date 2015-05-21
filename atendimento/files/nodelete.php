<?php
	session_start() ;
	include_once("../../web/conf-init.php");
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/ASP/get.php") ;
	
	$l = "" ;
	if ( isset( $_GET['l'] ) ) { $l = $_GET['l'] ; }

	$userinfo = AdminASP_get_ASPInfoByASPLogin( $dbh, $l ) ;
	if ( isset( $userinfo['aspID'] ) )
		print $userinfo['aspID'] ;
	else
		print "0" ;
?>