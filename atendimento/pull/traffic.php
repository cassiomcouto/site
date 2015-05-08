<?php
	session_start() ;
	$sid = $x = "" ;
	if ( isset( $_GET['sid'] ) ) { $sid = $_GET['sid'] ; }
	if ( isset( $_GET['x'] ) ) { $x = $_GET['x'] ; }

	include_once("../web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Footprint_unique/get.php") ;

	// get all the non-idle/non-expired visitors
	$total_active_footprints = ServiceFootprintUnique_get_TotalActiveFootprints( $dbh, $x, $_SESSION['session_admin'][$sid]['dept_string'] ) ;

	$image_path = "$DOCUMENT_ROOT/images/counters/0.gif" ;
	if ( file_exists( "$DOCUMENT_ROOT/images/counters/$total_active_footprints.gif" ) )
		$image_path = "$DOCUMENT_ROOT/images/counters/$total_active_footprints.gif" ;
	else if ( $total_active_footprints > 40 )
		$image_path = "$DOCUMENT_ROOT/images/counters/40.gif" ;

	// if the active visitors are changed (reduced or increased), let's put the
	// image so it tells the system to reload the window
	if ( $total_active_footprints != $_SESSION['session_admin'][$sid]['active_footprints'] )
	{
		Header( "Content-type: image/gif" ) ;
		readfile( $image_path ) ;
	}
?>
