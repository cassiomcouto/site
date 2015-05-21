<?php
	if ( ISSET( $_OFFICE_PUT_ServiceTranscripts_LOADED ) == true )
		return ;

	$_OFFICE_PUT_ServiceTranscripts_LOADED = true ;
	FUNCTION ServiceTranscripts_put_ChatTranscript( &$dbh,
				$sessionid,
				$transcript,
				$userid,
				$from_screen_name,
				$email,
				$deptid,
				$aspid,
				$created )
	{
		if ( ( $transcript == "" ) || ( $userid == "" )
			|| ( $from_screen_name == "" ) || ( $sessionid == "" )
			|| ( $aspid == "" ) )
		{
			return false ;
		}

		if ( !$created )
			$created = time() ;
		$sessionid = database_mysql_quote( $sessionid ) ;
		$transcript = database_mysql_quote( $transcript ) ;
		$userid = database_mysql_quote( $userid ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		$from_screen_name = database_mysql_quote( $from_screen_name ) ;
		$email = database_mysql_quote( $email ) ;
		$deptid = database_mysql_quote( $deptid ) ;
		$created = database_mysql_quote( $created ) ;
		$plain = preg_replace( "/<admin_strip>(.*?)<\/admin_strip>/", "", $transcript ) ;
		$plain = database_mysql_quote( strip_tags( $plain ) ) ;
		$plain = preg_replace( "/'/", "&#039;", $plain ) ;

		$transcript = preg_replace( "/<script(.*?)<\/script>/", "", $transcript ) ;
		$transcript = preg_replace( "/<body(.*?)>/", "", $transcript ) ;
		$transcript = preg_replace( "/<admin_strip>(.*?)<\/admin_strip>/", "", $transcript ) ;
		$transcript = preg_replace( "/'/", "&#039;", $transcript ) ;

		$query = "SELECT * FROM chattranscripts WHERE chat_session = '$sessionid'" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			
			if ( $data['chat_session'] )
				$query = "UPDATE chattranscripts SET userID = $userid, from_screen_name = '$from_screen_name', email = '$email', created = $created, deptID = '$deptid', plain = '$plain', formatted = '$transcript', aspID = $aspid WHERE chat_session = '$sessionid'" ;
			else
				$query = "INSERT INTO chattranscripts VALUES('$sessionid', $userid, '$from_screen_name', '$email', $created, '$deptid', '$plain', '$transcript', $aspid)" ;
			database_mysql_query( $dbh, $query ) ;

			if ( $dbh[ 'ok' ] )
			{
				return true ;
			}
		}
		return false ;
	}
?>