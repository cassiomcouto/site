<?php
	if ( ISSET( $_OFFICE_update_ServiceSurvey_LOADED ) == true )
		return ;

	$_OFFICE_update_ServiceSurvey_LOADED = true ;

	include_once( "$DOCUMENT_ROOT/admin/traffic/APISurvey/get.php" ) ;

	/*****  ServiceSurvey_update_SurveyValue  *********************
	 *
	 *  History:
	 *	Kyle Hicks				Nov 9, 2002
	 *
	 *****************************************************************/
	FUNCTION ServiceSurvey_update_SurveyValue( &$dbh,
					$aspid,
					$surveyid,
					$tbl_name,
					$value )
	{
		if ( ( $aspid == "" ) || ( $surveyid == "" )
			|| ( $tbl_name == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$surveyid = database_mysql_quote( $surveyid ) ;
		$tbl_name = database_mysql_quote( $tbl_name ) ;
		$value = database_mysql_quote( $value ) ;

		$query = "UPDATE chatsurveys SET $tbl_name = '$value' WHERE surveyID = '$surveyid' AND aspID = '$aspid'" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}
?>
