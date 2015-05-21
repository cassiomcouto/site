<?php
	if ( ISSET( $_OFFICE_REMOVE_ServiceSurvey_LOADED ) == true )
		return ;

	$_OFFICE_REMOVE_ServiceSurvey_LOADED = true ;

	/*****  ServiceSurvey_remove_Survey  ************************************
	 *
	 *  History:
	 *	Kyle Hicks				Nov 16, 2001
	 *
	 *****************************************************************/
	FUNCTION ServiceSurvey_remove_Survey( &$dbh,
						$aspid,
						$surveyid )
	{
		if ( ( $aspid == "" ) || ( $surveyid == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$surveyid = database_mysql_quote( $surveyid ) ;

		$query = "DELETE FROM chatsurveylogs WHERE aspID = $aspid AND surveyID = $surveyid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM chatsurveys WHERE aspID = $aspid AND surveyID = $surveyid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "UPDATE chatrequestlogs SET surveyID = 0 WHERE surveyID = $surveyid AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}

		return false ;
	}

?>