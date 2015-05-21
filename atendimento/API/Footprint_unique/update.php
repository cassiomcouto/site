<?php
	if ( ISSET( $_OFFICE_update_ServiceFootprintUnique_LOADED ) == true )
		return ;

	$_OFFICE_update_ServiceFootprintUnique_LOADED = true ;

	/*****  ServiceFootprintUnique_update_FootprintValue  ***************************
	 *
	 *  History:
	 *	Kyle Hicks				Nov 15, 2002
	 *
	 *****************************************************************/
	FUNCTION ServiceFootprintUnique_update_FootprintValue( &$dbh,
						$ip,
						$tbl_name,
						$value )
	{
		if ( ( $ip == "" ) || ( $tbl_name == "" ) )
		{
			return false ;
		}
		$ip = database_mysql_quote( $ip ) ;
		$tbl_name = database_mysql_quote( $tbl_name ) ;
		$value = database_mysql_quote( $value ) ;

		$query = "UPDATE chatfootprintsunique SET $tbl_name = '$value' WHERE ip = '$ip'" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

?>
