<?php
	if ( ISSET( $_OFFICE_update_Knowledge_LOADED ) == true )
		return ;

	$_OFFICE_update_Knowledge_LOADED = true ;

	/*****  Knowledge_update_QuestionClicks  *********************
	 *
	 *  History:
	 *	Seth Adams				Sept 14, 2003
	 *
	 *****************************************************************/
	FUNCTION Knowledge_update_QuestionClicks( &$dbh,
					$aspid,
					$questid )
	{
		if ( ( $aspid == "" ) || ( $questid == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$questid = database_mysql_quote( $questid ) ;

		$query = "UPDATE chatkbquestions SET clicks = clicks + 1 WHERE questID = '$questid' AND aspID = '$aspid'" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

	/*****  Knowledge_update_Category  *********************
	 *
	 *  History:
	 *	Seth Adams				Sept 16, 2003
	 *
	 *****************************************************************/
	FUNCTION Knowledge_update_Category( &$dbh,
					$aspid,
					$catid,
					$name,
					$display_order )
	{
		if ( ( $aspid == "" ) || ( $catid == "" )
			|| ( $name == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$catid = database_mysql_quote( $catid ) ;
		$name = database_mysql_quote( $name ) ;
		$display_order = database_mysql_quote( $display_order ) ;

		$query = "UPDATE chatkbcats SET name = '$name', display_order = '$display_order' WHERE catID = $catid AND aspID = '$aspid'" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

	/*****  Knowledge_update_Question  *********************
	 *
	 *  History:
	 *	Seth Adams				Sept 16, 2003
	 *
	 *****************************************************************/
	FUNCTION Knowledge_update_Question( &$dbh,
					$aspid,
					$questid,
					$question,
					$answer )
	{
		if ( ( $aspid == "" ) || ( $questid == "" )
			|| ( $question == "" ) || ( $answer == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$questid = database_mysql_quote( $questid ) ;
		$question = database_mysql_quote( $question ) ;
		$answer = database_mysql_quote( $answer ) ;

		$query = "UPDATE chatkbquestions SET question = '$question', answer = '$answer' WHERE questID = '$questid' AND aspID = '$aspid'" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

?>
