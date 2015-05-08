<?php
	if ( ISSET( $_OFFICE_GET_ServiceTranscripts_LOADED ) == true )
		return ;

	$_OFFICE_GET_ServiceTranscripts_LOADED = true ;

	FUNCTION ServiceTranscripts_get_DeptTranscripts( &$dbh,
						$aspid,
						$deptid,
						$order_by,
						$sort_by,
						$page,
						$page_per,
						$search_string )
	{
		if ( ( $aspid == "" ) || ( $deptid == "" )
			|| ( $page_per == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$deptid = database_mysql_quote( $deptid ) ;
		$order_by = database_mysql_quote( $order_by ) ;
		$sort_by = database_mysql_quote( $sort_by ) ;
		$page = database_mysql_quote( $page ) ;
		$page_per = database_mysql_quote( $page_per ) ;
		$search_string = database_mysql_quote( $search_string ) ;
		$chat_transcripts = ARRAY() ;

		$page -= 1 ;
		if ( $page < 1 )
			$begin_index = 0 ;
		else
			$begin_index = $page * $page_per ;

		if ( !$order_by )
			$order_by = "chattranscripts.created" ;
		if ( !$sort_by )
			$sort_by = "DESC" ;

		// if $search_string is provided, then we want to search
		if ( $search_string )
			$query = "SELECT chat_adminrate.rating AS rating, chattranscripts.* FROM chattranscripts LEFT JOIN chat_adminrate ON ( chattranscripts.chat_session = chat_adminrate.sessionID ) WHERE chattranscripts.deptID = '$deptid' AND chattranscripts.aspID = '$aspid' AND plain LIKE '%$search_string%' ORDER BY $order_by $sort_by LIMIT $begin_index, $page_per" ;
		else
			$query = "SELECT chat_adminrate.rating AS rating, chattranscripts.* FROM chattranscripts LEFT JOIN chat_adminrate ON ( chattranscripts.chat_session = chat_adminrate.sessionID ) WHERE chattranscripts.deptID = '$deptid' AND chattranscripts.aspID = '$aspid' ORDER BY $order_by $sort_by LIMIT $begin_index, $page_per" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
			{
				$chat_transcripts[] = $data ;
			}
			return $chat_transcripts ;
		}
		return false ;
	}

	/*****  ServiceTranscripts_get_UserDeptTranscripts  *************************
	 *
	 *  History:
	 *	Kyle Hicks				Dec 15, 2001
	 *
	 *****************************************************************/
	FUNCTION ServiceTranscripts_get_UserDeptTranscripts( &$dbh,
						$aspid,
						$userid,
						$deptid,
						$order_by,
						$sort_by,
						$page,
						$page_per,
						$search_string)
	{
		if ( ( $userid == "" ) || ( $page_per == "" )
			|| ( $aspid == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$userid = database_mysql_quote( $userid ) ;
		$deptid = database_mysql_quote( $deptid ) ;
		$order_by = database_mysql_quote( $order_by ) ;
		$sort_by = database_mysql_quote( $sort_by ) ;
		$page = database_mysql_quote( $page ) ;
		$page_per = database_mysql_quote( $page_per ) ;
		$search_string = database_mysql_quote( $search_string ) ;
		$chat_transcripts = ARRAY() ;
		$dept_string = "" ;

		$page -= 1 ;
		if ( $page < 1 )
			$begin_index = 0 ;
		else
			$begin_index = $page * $page_per ;

		if ( !$order_by )
			$order_by = "chattranscripts.created" ;
		if ( !$sort_by )
			$sort_by = "DESC" ;
		if ( $deptid )
			$dept_string = "AND chattranscripts.deptID = $deptid" ;


		// if $search_string is provided, then we want to search
		if ( $search_string )
			$query = "SELECT chat_adminrate.rating AS rating, chattranscripts.* FROM chattranscripts LEFT JOIN chat_adminrate ON ( chattranscripts.chat_session = chat_adminrate.sessionID ) WHERE chattranscripts.userID = $userid $dept_string AND chattranscripts.aspID = '$aspid' AND plain LIKE '%$search_string%' ORDER BY $order_by $sort_by LIMIT $begin_index, $page_per" ;
		else
		{
			//$query = "SELECT * FROM chattranscripts WHERE userID = $userid $dept_string AND aspID = '$aspid' ORDER BY $order_by $sort_by LIMIT $begin_index, $page_per" ;
			$query = "SELECT chat_adminrate.rating AS rating, chattranscripts.* FROM chattranscripts LEFT JOIN chat_adminrate ON ( chattranscripts.chat_session = chat_adminrate.sessionID ) WHERE chattranscripts.userID = $userid $dept_string AND chattranscripts.aspID = '$aspid' ORDER BY $order_by $sort_by LIMIT $begin_index, $page_per" ;
		}
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
			{
				$chat_transcripts[] = $data ;
			}
			return $chat_transcripts ;
		}
		return false ;
	}

	/*****  ServiceTranscripts_get_TotalDeptTranscripts  *************************
	 *
	 *  History:
	 *	Kyle Hicks				Dec 15, 2001
	 *
	 *****************************************************************/
	FUNCTION ServiceTranscripts_get_TotalDeptTranscripts( &$dbh,
						$deptid,
						$search_string )
	{
		if ( $deptid == "" )
		{
			return false ;
		}
		$deptid = database_mysql_quote( $deptid ) ;
		$search_string = database_mysql_quote( $search_string ) ;

		// if $search_string is provided, then we want to search
		if ( $search_string )
			$query = "SELECT COUNT(*) AS total FROM chattranscripts WHERE deptID = '$deptid' AND plain LIKE '%$search_string%'" ;
		else
			$query = "SELECT COUNT(*) AS total FROM chattranscripts WHERE deptID = '$deptid'" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data['total'] ;
		}
		return false ;
	}

	/*****  ServiceTranscripts_get_TotalUserDeptTranscripts  *************************
	 *
	 *  History:
	 *	Kyle Hicks				Dec 15, 2001
	 *
	 *****************************************************************/
	FUNCTION ServiceTranscripts_get_TotalUserDeptTranscripts( &$dbh,
						$userid,
						$deptid,
						$search_string )
	{
		if ( $userid == "" )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$deptid = database_mysql_quote( $deptid ) ;
		$search_string = database_mysql_quote( $search_string ) ;
		$dept_string = "" ;
		if ( $deptid )
			$dept_string = "AND deptID = $deptid" ;

		// if $search_string is provided, then we want to search
		if ( $search_string )
			$query = "SELECT COUNT(*) AS total FROM chattranscripts WHERE userID = $userid $dept_string AND plain LIKE '%$search_string%'" ;
		else
			$query = "SELECT COUNT(*) AS total FROM chattranscripts WHERE userID = $userid $dept_string" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data['total'] ;
		}
		return false ;
	}

	/*****  ServiceTranscripts_get_TranscriptInfo  *************************
	 *
	 *  History:
	 *	Kyle Hicks				Dec 15, 2001
	 *
	 *****************************************************************/
	FUNCTION ServiceTranscripts_get_TranscriptInfo( &$dbh,
						$chat_session,
						$aspid )
	{
		if ( ( $chat_session == "" ) || ( $aspid == "" ) )
		{
			return false ;
		}
		$chat_session = database_mysql_quote( $chat_session ) ;
		$aspid = database_mysql_quote( $aspid ) ;

		$query = "SELECT chat_adminrate.rating AS rating, chattranscripts.* FROM chattranscripts LEFT JOIN chat_adminrate ON ( chattranscripts.chat_session = chat_adminrate.sessionID ) WHERE chattranscripts.chat_session = '$chat_session' AND chattranscripts.aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	/*****  ServiceTranscripts_get_TranscriptsByIP  *************************
	 *
	 *  History:
	 *	Kyle Hicks				May 8, 2003
	 *							modified Sep 26, 2006
	 *
	 *****************************************************************/
	FUNCTION ServiceTranscripts_get_TranscriptsByIP( &$dbh,
						$ip,
						$aspid )
	{
		if ( ( $ip == "" ) || ( $aspid == "" ) )
		{
			return false ;
		}
		$ip = database_mysql_quote( $ip ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		$transcripts = Array() ;

		$query = "SELECT chat_adminrate.rating AS rating, chattranscripts.* FROM chattranscripts, chatrequestlogs LEFT JOIN chat_adminrate ON ( chatrequestlogs.chat_session = chat_adminrate.sessionID ) WHERE chatrequestlogs.ip = '$ip' AND chatrequestlogs.aspID = $aspid AND chatrequestlogs.chat_session = chattranscripts.chat_session ORDER BY created DESC LIMIT 75" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			while ( $data = database_mysql_fetchrow( $dbh ) )
				$transcripts[] = $data ;
			return $transcripts ;
		}
		return false ;
	}

	/*****  ServiceTranscripts_get_TotalIPTranscripts  *************************
	 *
	 *  History:
	 *	Kyle Hicks				May 27, 2005
	 *
	 *****************************************************************/
	FUNCTION ServiceTranscripts_get_TotalIPTranscripts( &$dbh,
						$aspid,
						$ips )
	{
		if ( $aspid == "" )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$transcripts = Array() ;

		$query = "SELECT ip, count(*) AS total FROM chattranscripts, chatrequestlogs WHERE ( chatrequestlogs.ip = '0' $ips ) AND chatrequestlogs.aspID = $aspid AND chatrequestlogs.chat_session = chattranscripts.chat_session GROUP BY chatrequestlogs.ip" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			while ( $data = database_mysql_fetchrow( $dbh ) )
				$transcripts[] = $data ;
			return $transcripts ;
		}
		return false ;
	}

?>