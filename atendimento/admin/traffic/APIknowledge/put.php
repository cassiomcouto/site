<?php
	if ( ISSET( $_OFFICE_PUT_Knowledge_LOADED ) == true )
		return ;

	$_OFFICE_PUT_Knowledge_LOADED = true ;

	/*****  Knowledge_put_Category  *************************
	 *
	 *  History:
	 *	Seth Adams				Sept 13, 2003
	 *
	 *****************************************************************/
	FUNCTION Knowledge_put_Category( &$dbh,
				$aspid,
				$deptid,
				$parentid,
				$name,
				$order )
	{
		if ( ( $aspid == "" ) || ( $name == "" )
			|| ( $deptid == "" ) )
		{
			return false ;
		}

		$aspid = database_mysql_quote( $aspid ) ;
		$deptid = database_mysql_quote( $deptid ) ;
		$parentid = database_mysql_quote( $parentid ) ;
		$name = database_mysql_quote( $name ) ;
		$order = database_mysql_quote( $order ) ;

		$query = "INSERT INTO chatkbcats VALUES( 0, '$aspid', '$deptid', '$parentid', '$order', '$name' )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$id = database_mysql_insertid ( $dbh ) ;
			return $id ;
		}
		return false ;
	}

	/*****  Knowledge_put_Question  *************************
	 *
	 *  History:
	 *	Seth Adams				Sept 13, 2003
	 *
	 *****************************************************************/
	FUNCTION Knowledge_put_Question( &$dbh,
				$aspid,
				$deptid,
				$catid,
				$question,
				$answer )
	{
		if ( ( $aspid == "" ) || ( $deptid == "" )
			|| ( $catid == "" ) || ( $question == "" )
			|| ( $answer == "" ) )
		{
			return false ;
		}

		$aspid = database_mysql_quote( $aspid ) ;
		$deptid = database_mysql_quote( $deptid ) ;
		$catid = database_mysql_quote( $catid ) ;
		$question = database_mysql_quote( $question ) ;
		$answer = database_mysql_quote( $answer ) ;

		$query = "INSERT INTO chatkbquestions VALUES( 0, '$aspid', '$catid', '$deptid', '0', 0, '$question', '$answer' )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$id = database_mysql_insertid ( $dbh ) ;
			return $id ;
		}
		return false ;
	}

	/*****  Knowledge_put_QuestionRating  *************************
	 *
	 *  History:
	 *	Seth Adams				Sept 14, 2003
	 *
	 *****************************************************************/
	FUNCTION Knowledge_put_QuestionRating( &$dbh,
				$aspid,
				$questid,
				$catid,
				$rating )
	{
		if ( ( $aspid == "" ) || ( $questid == "" )
			|| ( $catid == "" ) )
		{
			return false ;
		}

		$aspid = database_mysql_quote( $aspid ) ;
		$questid = database_mysql_quote( $questid ) ;
		$catid = database_mysql_quote( $catid ) ;
		$rating = database_mysql_quote( $rating ) ;
		$ip = database_mysql_quote( $_SERVER['REMOTE_ADDR'] ) ;
		$now = time() ;

		$query = "REPLACE INTO chatkbratings VALUES( '$aspid', '$questid', '$catid', '$rating', $now, '$ip' )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			if ( $rating == 1 )
			{
				$query = "UPDATE chatkbquestions SET points = points + 1 WHERE questID = $questid AND aspID = $aspid" ;
				database_mysql_query( $dbh, $query ) ;
			}
			else
			{
				$query = "UPDATE chatkbquestions SET points = points - 1 WHERE questID = $questid AND aspID = $aspid" ;
				database_mysql_query( $dbh, $query ) ;
			}
			return true ;
		}
		return false ;
	}

	/*****  Knowledge_put_SearchTerm  *************************
	 *
	 *  History:
	 *	Kyle Hicks				June 19, 2004
	 *
	 *****************************************************************/
	FUNCTION Knowledge_put_SearchTerm( &$dbh,
				$aspid,
				$searchterm )
	{
		if ( ( $aspid == "" ) || ( $searchterm == "" ) )
		{
			return false ;
		}

		$aspid = database_mysql_quote( $aspid ) ;
		$searchterm = database_mysql_quote( $searchterm ) ;

		$query = "SELECT * FROM chatkbsearchterms WHERE searchterm = '$searchterm'" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			
			if ( isset( $data['searchID'] ) && $data['searchID'] )
			{
				$counter = $data['counter'] + 1 ;
				$query = "REPLACE INTO chatkbsearchterms VALUES( $data[searchID], $aspid, $counter, '$searchterm', '$data[correction]', '$data[related]' )" ;
			}
			else
				$query = "INSERT INTO chatkbsearchterms VALUES( 0, $aspid, 1, '$searchterm', '', '' )" ;
			database_mysql_query( $dbh, $query ) ;

			return true ;
		}
		return false ;
	}

?>