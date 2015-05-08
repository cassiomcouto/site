<?php
	if ( ISSET( $_OFFICE_PUT_ServiceSurvey_LOADED ) == true )
		return ;

	$_OFFICE_PUT_ServiceSurvey_LOADED = true ;

	include_once( "$DOCUMENT_ROOT/admin/traffic/APISurvey/update.php" ) ;

?>