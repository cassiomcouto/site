<?php
	if ( ISSET( $_OFFICE_REMOVE_Knowledge_LOADED ) == true )
		return ;

	$_OFFICE_REMOVE_Knowledge_LOADED = true ;

	/*****  Knowledge_remove_Question  ************************************
	 *
	 *  History:
	 *	Seth Adams				Sept 14, 2003
	 *
	 *****************************************************************/
	FUNCTION Knowledge_remove_Question( &$dbh,
						$aspid,
						$questid )
	{
		if ( ( $aspid == "" ) || ( $questid == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$questid = database_mysql_quote( $questid ) ;

		$query = "DELETE FROM chatkbquestions WHERE aspID = $aspid AND questid = $questid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}

		return false ;
	}

	/*****  Knowledge_remove_Category  ************************************
	 *
	 *  History:
	 *	Seth Adams				Sept 14, 2003
	 *
	 *****************************************************************/
	FUNCTION Knowledge_remove_Category( &$dbh,
						$aspid,
						$catid )
	{
		if ( ( $aspid == "" ) || ( $catid == "" ) )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$catid = database_mysql_quote( $catid ) ;
		$subcats = Array() ;

		$query = "SELECT * FROM chatkbcats WHERE aspID = $aspid AND catID_parent = $catid" ;
		database_mysql_query( $dbh, $query ) ;
		while( $data = database_mysql_fetchrow( $dbh ) )
				$subcats[] = $data ;

		for ( $c = 0; $c < count( $subcats ); ++$c )
		{
			$category = $subcats[$c] ;
			Knowledge_remove_Category( $dbh, $aspid, $category['catID'] ) ;
		}
		$query = "DELETE FROM chatkbquestions WHERE aspID = $aspid AND catID = $catid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM chatkbcats WHERE aspID = $aspid AND catID_parent = $catid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM chatkbcats WHERE aspID = $aspid AND catID = $catid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM chatkbratings WHERE aspID = $aspid AND catID = $catid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}

		return false ;
	}

?>