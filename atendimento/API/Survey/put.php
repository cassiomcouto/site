<?php
	if ( ISSET( $_OFFICE_PUT_ServiceSurvey_LOADED ) == true )
		return ;

	$_OFFICE_PUT_ServiceSurvey_LOADED = true ;

	include_once( "$DOCUMENT_ROOT/API/Users/get.php" ) ;
	FUNCTION ServiceSurvey_put_AdminSurvey( &$dbh,
					$userid,
					$deptid,
					$aspid,
					$sessionid,
					$rating )
	{
		if ( ( $userid == "" ) || ( $aspid == "" ) ||
			( $sessionid == "" ) || ( $rating == 0 ) 
			|| ( $rating == "" ) || ( $deptid == "" ) )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$deptid = database_mysql_quote( $deptid ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		$sessionid = database_mysql_quote( $sessionid ) ;
		$rating = database_mysql_quote( $rating ) ;
		$now = time() ;
		$userinfo = AdminUsers_get_UserInfo( $dbh, $userid, $aspid ) ;

		/** check to make sure session has not been rated **/
		$query = "SELECT * FROM chat_adminrate WHERE sessionID = '$sessionid'" ;
		database_mysql_query( $dbh, $query ) ;
		$sessionrating = database_mysql_fetchrow( $dbh ) ;
		/***************************************************/

		if ( !isset( $sessionrating['sessionID'] ) )
		{
			$next_rate_total = $userinfo['rate_total'] + 1 ;
			$next_rate_sum = $userinfo['rate_sum'] + $rating ;
			$next_rate_ave = round( $next_rate_sum/$next_rate_total ) ;

			$query = "UPDATE chat_admin SET rate_total = '$next_rate_total', rate_sum = '$next_rate_sum', rate_ave = '$next_rate_ave' WHERE userID = $userid" ;
			database_mysql_query( $dbh, $query ) ;

			if ( $dbh[ 'ok' ] )
			{
				$query = "INSERT INTO chat_adminrate VALUES( $userid, '$sessionid', '$rating', $deptid, $now, $aspid )" ;
				database_mysql_query( $dbh, $query ) ;

				if ( $dbh[ 'ok' ] )
				{
					return true ;
				}
			}
		}
		return false ;
	}

?>