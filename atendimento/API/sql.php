<?php
	if ( ISSET( $_OFFICE_sql_LOADED ) == true )
		return ;
 
	$_OFFICE_sql_LOADED = true ;

	if ( $NO_PCONNECT )
		$connection = mysql_connect( $SQLHOST, $SQLLOGIN, $SQLPASS ) ;
	else
		$connection = mysql_pconnect( $SQLHOST, $SQLLOGIN, $SQLPASS ) ;
	mysql_select_db( $DATABASE ) ;
	$dbh['con'] = $connection ;

	// The &$dbh is passed by reference so you can call it
	// anywhere in the code.  you can always access
	// the $dbh[error] to see the most recent db error, if any.
	// the $dbh also hold other variables, as you see.
	function database_mysql_query( &$dbh, $query )
	{
		$dbh['ok'] = 0 ;
		$dbh['result'] = 0 ;
		$dbh['error'] = "None" ; 
		$dbh['query'] = $query ;

		$result = mysql_query( $query, $dbh['con'] ) ;
		if ( $result )
		{
			$dbh['result'] = $result ;
			$dbh['ok'] = 1 ;
			$dbh['error'] = "None" ;
		}
		else
		{
			$dbh['result'] = 0 ;
			$dbh['ok'] = 0 ;
			$dbh['error'] = mysql_error() ;
		}
	}

	function database_mysql_fetchrow( &$dbh )
	{
		$result = mysql_fetch_array( $dbh['result'] ) ;
		return $result ;
	}

	function database_mysql_insertid( &$dbh )
	{
		$id = mysql_insert_id( $dbh['con'] ) ;
		return $id ;
	}

	function database_mysql_nresults( &$dbh )
	{
		$total = mysql_num_rows( $dbh['result'] ) ;
		return $total ;
	}

	function database_mysql_quote( $var )
	{
		return mysql_escape_string( $var ) ;
	}

?>
