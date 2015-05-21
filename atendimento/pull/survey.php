<?php
	include_once("../web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	// image to load, if flag file does not exists
	$image_path = "$DOCUMENT_ROOT/images/empty_nodelete.gif" ;
	readfile( $image_path ) ;
?>