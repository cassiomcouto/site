<?php
	/*****  ServiceFootprintUnique::put  ***************************
	 *
	 *  $Id: put.php,v 1.3 2005/02/06 00:42:27 osicodes Exp $
	 *
	 ****************************************************************/

	if ( ISSET( $_OFFICE_PUT_ServiceFootprintUnique_LOADED ) == true )
		return ;

	$_OFFICE_PUT_ServiceFootprintUnique_LOADED = true ;

	/*****  ServiceFootprintUnique_put_Footprint  *******************************
	 *
	 *  History:
	 *	Holger					Feb 26, 2002
	 *
	 *****************************************************************/
	FUNCTION ServiceFootprintUnique_put_Footprint( &$dbh,
					$ip,
					$url,
					$aspid,
					$deptid )
	{
		if ( ( $ip == "" ) || ( $url == "" )
			|| ( $aspid == "" ) )
		{
			return false ;
		}
		$ip = database_mysql_quote( $ip ) ;
		$url = database_mysql_quote( $url ) ;
		$aspid = database_mysql_quote( $aspid ) ;
		$deptid = database_mysql_quote( $deptid ) ;
		$now = time() ;

		$query = "SELECT * FROM chatfootprintsunique WHERE ip = '$ip'" ;
		database_mysql_query( $dbh, $query ) ;
		$data = database_mysql_fetchrow( $dbh ) ;

		if ( isset( $data['ip'] ) )
		{
			$query = "REPLACE INTO chatfootprintsunique VALUES( '$ip', $data[created], $now, '$url', $aspid, '$deptid' )" ;
			database_mysql_query( $dbh, $query ) ;
		}
		else
		{
			$query = "REPLACE INTO chatfootprintsunique VALUES( '$ip', $now, $now, '$url', $aspid, '$deptid' )" ;
			database_mysql_query( $dbh, $query ) ;
		}

		if ( $dbh[ 'ok' ] )
		{
			return true ;
		}
		return false ;
	}

?>