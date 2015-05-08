<?php
	if ( ISSET( $_OFFICE_PUT_AdminUsers_LOADED ) == true )
		return ;

	$_OFFICE_PUT_AdminUsers_LOADED = true ;

	include_once( "$DOCUMENT_ROOT/API/Users/get.php" ) ;

	/*****  AdminUsers_put_user  *******************************
	 *
	 *  History:
	 *	Nate Lee				Dec 2, 2001
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_put_user( &$dbh,
					$login,
					$password,
					$name,
					$email,
					$aspid,
					$rateme,
					$op2op )
	{
		if ( ( $login == "" ) || ( $password == "" )
			|| ( $name == "" ) || ( $email == "" )
			|| ( $aspid == "" ) )
		{
			return false ;
		}
		$login = database_mysql_quote( $login ) ;
		$name = database_mysql_quote( $name ) ;
		$email = database_mysql_quote( $email ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		$rateme = database_mysql_quote( $rateme ) ;
		$op2op = database_mysql_quote( $op2op ) ;
		$password = md5( database_mysql_quote( $password ) ) ;
		$now = time() ;

		$query = "SELECT userID FROM chat_admin WHERE login = '$login' AND aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow ( $dbh ) ;

			if ( $data['userID'] )
			{
				return false ;
			}

			$query = "INSERT INTO chat_admin VALUES (0, '$login', '$password', '$name', '$email', 0, 0, 1, 10, 0, $now, $aspid, 0, '$rateme', 0, 0, 0, '$op2op', 10, '', '')" ;
			database_mysql_query( $dbh, $query ) ;

			if ( $dbh[ 'ok' ] )
			{
				$id = database_mysql_insertid ( $dbh ) ;
				return $id ;
			}
			return false ;
		}
		return false ;
	}

	FUNCTION AdminUsers_put_DeptUser( &$dbh,
					$userid,
					$deptid )
	{
		if ( ( $userid == "" ) || ( $deptid == "" ) )
		{
			return false ;
		}
		$userid = database_mysql_quote( $userid ) ;
		$deptid = database_mysql_quote( $deptid ) ;
		
		$query = "SELECT * FROM chatuserdeptlist WHERE userID = $userid AND deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow ( $dbh ) ;

			if ( $data['userID'] )
			{
				return false ;
			}

			$query = "INSERT INTO chatuserdeptlist VALUES( $userid, $deptid, 1, 0 )" ;
			database_mysql_query( $dbh, $query ) ;

			if ( $dbh[ 'ok' ] )
			{
				return true ;
			}
		}
		return false ;
	}

	/*****  AdminUsers_put_department  *******************************
	 *
	 *  History:
	 *	Lance Wall				Nov 23, 2004
	 *
	 *****************************************************************/
	FUNCTION AdminUsers_put_department( &$dbh,
					$deptid,
					$name,
					$visible,
					$email,
					$save_transcripts,
					$share_transcripts,
					$email_transcripts,
					$exp_value,
					$exp_word,
					$show_que,
					$aspid,
					$initiate_chat,
					$greeting )
	{
		if ( ( $name == "" ) || ( $email == "" ) 
			|| ( $aspid == "" ) )
		{
			return false ;
		}
		global $LANG ;
		$deptid = database_mysql_quote( $deptid ) ;
		$name = database_mysql_quote( $name ) ;
		$visible = database_mysql_quote( $visible ) ;
		$email = database_mysql_quote( $email ) ;
		$save_transcripts = database_mysql_quote( $save_transcripts ) ;
		$share_transcripts = database_mysql_quote( $share_transcripts ) ;
		$email_transcripts = database_mysql_quote( $email_transcripts ) ;
		$show_que = database_mysql_quote( $show_que ) ;
		$exp_value = database_mysql_quote( $exp_value ) ;
		$exp_word = database_mysql_quote( $exp_word ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		$initiate_chat = database_mysql_quote( $initiate_chat ) ;
		$greeting = database_mysql_quote( $greeting ) ;
		$deptinfo = AdminUsers_get_DeptInfo( $dbh, $deptid, $aspid ) ;

		// only do checking of duplicate if it is new (deptid not passed)
		if ( ( $deptinfo['name'] == "$name" ) && !$deptid )
		{
			return false ;
		}

		if ( !$exp_value )
			$exp_value = 0 ;
		$exp_string = "$exp_value<:>$exp_word" ;

		// $exp_word: 1=Days, 2=Months, 3=Years
		switch( $exp_word )
		{
			case 1:
				$duration = ( 60*60*24*$exp_value ) ;
				break ;
			case 2:
				$duration = ( 60*60*24*28*$exp_value ) ;
				break ;
			case 3:
				$duration = ( 60*60*24*28*356*$exp_value ) ;
				break ;
			default:
				$duration = 0 ;
		}

		if ( $deptinfo['deptID'] )
		{
			$message_box = database_mysql_quote( $deptinfo['message'] ) ;
			$greeting = database_mysql_quote( $deptinfo['greeting'] ) ;
			$query = "REPLACE INTO chatdepartments VALUES ('$deptid', '$name', '$visible', '$save_transcripts', '$share_transcripts', '$exp_string', '$duration', '$show_que', '$email', $aspid, '$initiate_chat', '$email_transcripts', '$deptinfo[status_image_offline]', '$deptinfo[status_image_online]', '$deptinfo[status_image_away]', '$message_box', '$deptinfo[away_message]', '$greeting')" ;
		}
		else
		{
			$message_box = database_mysql_quote( $LANG['MESSAGE_BOX_MESSAGE'] ) ;
			$query = "REPLACE INTO chatdepartments VALUES (0, '$name', '$visible', '$save_transcripts', '$share_transcripts', '$exp_string', '$duration', '$show_que', '$email', $aspid, '$initiate_chat', '$email_transcripts', '', '', '', '$message_box', '', '$greeting')" ;
		}
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$id = database_mysql_insertid ( $dbh ) ;
			return $id ;
		}
		return false ;
	}

?>